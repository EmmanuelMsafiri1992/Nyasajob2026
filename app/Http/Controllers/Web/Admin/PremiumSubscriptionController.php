<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Web\Admin\Panel\PanelController;
use App\Models\PremiumSubscription;
use App\Models\User;
use App\Services\PremiumSubscriptionService;
use App\Http\Requests\Admin\Request as StoreRequest;
use App\Http\Requests\Admin\Request as UpdateRequest;

class PremiumSubscriptionController extends PanelController
{
    public function setup()
    {
        /*
        |--------------------------------------------------------------------------
        | BASIC CRUD INFORMATION
        |--------------------------------------------------------------------------
        */
        $this->xPanel->setModel(PremiumSubscription::class);
        $this->xPanel->with(['user']);
        $this->xPanel->setRoute(admin_uri('premium-subscriptions'));
        $this->xPanel->setEntityNameStrings('Premium Subscription', 'Premium Subscriptions');
        $this->xPanel->denyAccess(['create']); // Subscriptions are created via PayPal
        $this->xPanel->removeButton('create');
        if (!request()->input('order')) {
            $this->xPanel->orderByDesc('created_at');
        }

        // Filters
        $this->xPanel->disableSearchBar();

        $this->xPanel->addFilter(
            [
                'name'  => 'id',
                'type'  => 'text',
                'label' => 'ID',
            ],
            false,
            function ($value) {
                $this->xPanel->addClause('where', 'id', '=', $value);
            }
        );

        $this->xPanel->addFilter(
            [
                'name'  => 'from_to',
                'type'  => 'date_range',
                'label' => trans('admin.Date range'),
            ],
            false,
            function ($value) {
                $dates = json_decode($value);
                $this->xPanel->addClause('where', 'created_at', '>=', $dates->from);
                $this->xPanel->addClause('where', 'created_at', '<=', $dates->to);
            }
        );

        $this->xPanel->addFilter(
            [
                'name'  => 'user_id',
                'type'  => 'text',
                'label' => 'User ID/Email',
            ],
            false,
            function ($value) {
                if (is_numeric($value)) {
                    $this->xPanel->addClause('where', 'user_id', '=', $value);
                } else {
                    $this->xPanel->addClause('whereHas', 'user', function ($query) use ($value) {
                        $query->where('email', 'like', '%' . $value . '%')
                              ->orWhere('name', 'like', '%' . $value . '%');
                    });
                }
            }
        );

        $this->xPanel->addFilter(
            [
                'name'  => 'status',
                'type'  => 'dropdown',
                'label' => trans('admin.Status'),
            ],
            [
                'pending' => 'Pending',
                'active' => 'Active',
                'cancelled' => 'Cancelled',
                'expired' => 'Expired',
                'suspended' => 'Suspended',
            ],
            function ($value) {
                $this->xPanel->addClause('where', 'status', '=', $value);
            }
        );

        /*
        |--------------------------------------------------------------------------
        | COLUMNS AND FIELDS
        |--------------------------------------------------------------------------
        */
        // COLUMNS
        $this->xPanel->addColumn([
            'name'  => 'id',
            'label' => 'ID',
        ]);
        $this->xPanel->addColumn([
            'name'  => 'created_at',
            'label' => trans('admin.Date'),
        ]);
        $this->xPanel->addColumn([
            'name'          => 'user_id',
            'label'         => 'User',
            'type'          => 'model_function',
            'function_name' => 'getUserHtml',
        ]);
        $this->xPanel->addColumn([
            'name'  => 'paypal_payer_email',
            'label' => 'PayPal Email',
        ]);
        $this->xPanel->addColumn([
            'name'  => 'amount',
            'label' => 'Amount',
            'type'  => 'model_function',
            'function_name' => 'getAmountHtml',
        ]);
        $this->xPanel->addColumn([
            'name'  => 'status',
            'label' => trans('admin.Status'),
            'type'  => 'model_function',
            'function_name' => 'getStatusHtml',
        ]);
        $this->xPanel->addColumn([
            'name'  => 'expires_at',
            'label' => 'Expires',
        ]);
        $this->xPanel->addColumn([
            'name'  => 'auto_renew',
            'label' => 'Auto Renew',
            'type'  => 'boolean',
        ]);

        // FIELDS
        $this->xPanel->addField([
            'name'       => 'status',
            'label'      => 'Status',
            'type'       => 'select_from_array',
            'options'    => [
                'pending' => 'Pending',
                'active' => 'Active',
                'cancelled' => 'Cancelled',
                'expired' => 'Expired',
                'suspended' => 'Suspended',
            ],
            'allows_null' => false,
        ]);
        $this->xPanel->addField([
            'name'  => 'expires_at',
            'label' => 'Expires At',
            'type'  => 'datetime',
        ]);
        $this->xPanel->addField([
            'name'  => 'auto_renew',
            'label' => 'Auto Renew',
            'type'  => 'checkbox',
        ]);
        $this->xPanel->addField([
            'name'       => 'cancellation_reason',
            'label'      => 'Cancellation Reason',
            'type'       => 'textarea',
            'attributes' => ['rows' => 3],
        ]);
    }

    public function store(StoreRequest $request)
    {
        return parent::storeCrud();
    }

    public function update(UpdateRequest $request)
    {
        return parent::updateCrud();
    }

    /**
     * Show statistics dashboard
     */
    public function dashboard()
    {
        $service = new PremiumSubscriptionService();
        $stats = $service->getStatistics();

        $recentSubscriptions = PremiumSubscription::with('user')
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        $expiringSoon = PremiumSubscription::with('user')
            ->expiringSoon()
            ->get();

        return view('admin.premium-subscriptions.dashboard', [
            'stats' => $stats,
            'recentSubscriptions' => $recentSubscriptions,
            'expiringSoon' => $expiringSoon,
        ]);
    }

    /**
     * Manually expire overdue subscriptions
     */
    public function expireOverdue()
    {
        $service = new PremiumSubscriptionService();
        $count = $service->expireOverdueSubscriptions();

        flash("{$count} subscriptions marked as expired.")->success();

        return redirect()->back();
    }

    /**
     * Cancel subscription manually
     */
    public function cancel($id)
    {
        $subscription = PremiumSubscription::findOrFail($id);

        // Cancel in PayPal if applicable
        if ($subscription->paypal_subscription_id) {
            $service = new PremiumSubscriptionService();
            $service->cancelSubscription($subscription->paypal_subscription_id, 'Cancelled by admin');
        }

        $subscription->cancel('Cancelled by admin');

        flash('Subscription cancelled successfully.')->success();

        return redirect()->back();
    }

    /**
     * Extend subscription manually
     */
    public function extend($id)
    {
        $subscription = PremiumSubscription::findOrFail($id);

        $subscription->renew();

        flash('Subscription extended by 1 month.')->success();

        return redirect()->back();
    }
}
