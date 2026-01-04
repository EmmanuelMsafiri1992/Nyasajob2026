<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Web\Admin\Panel\PanelController;
use App\Models\Coupon;

class CouponController extends PanelController
{
    public function setup()
    {
        /*
        |--------------------------------------------------------------------------
        | BASIC CRUD INFORMATION
        |--------------------------------------------------------------------------
        */
        $this->xPanel->setModel(Coupon::class);
        $this->xPanel->setRoute(admin_uri('coupons'));
        $this->xPanel->setEntityNameStrings('Coupon', 'Coupons');
        if (!request()->input('order')) {
            $this->xPanel->orderByDesc('created_at');
        }

        $this->xPanel->addButtonFromModelFunction('top', 'bulk_activation_button', 'bulkActivationButton', 'end');
        $this->xPanel->addButtonFromModelFunction('top', 'bulk_deactivation_button', 'bulkDeactivationButton', 'end');
        $this->xPanel->addButtonFromModelFunction('top', 'bulk_deletion_button', 'bulkDeletionButton', 'end');

        // Filters
        $this->xPanel->disableSearchBar();
        $this->xPanel->addFilter(
            [
                'name'  => 'code',
                'type'  => 'text',
                'label' => 'Code',
            ],
            false,
            function ($value) {
                $this->xPanel->addClause('where', function ($query) use ($value) {
                    $query->where('code', 'LIKE', "%$value%")
                        ->orWhere('name', 'LIKE', "%$value%");
                });
            }
        );
        $this->xPanel->addFilter(
            [
                'name'  => 'type',
                'type'  => 'dropdown',
                'label' => 'Discount Type',
            ],
            [
                'percentage' => 'Percentage',
                'fixed'      => 'Fixed Amount',
            ],
            function ($value) {
                $this->xPanel->addClause('where', 'discount_type', '=', $value);
            }
        );
        $this->xPanel->addFilter(
            [
                'name'  => 'status',
                'type'  => 'dropdown',
                'label' => trans('admin.Status'),
            ],
            [
                1 => trans('admin.Activated'),
                2 => trans('admin.Unactivated'),
            ],
            function ($value) {
                if ($value == 1) {
                    $this->xPanel->addClause('where', 'active', '=', 1);
                }
                if ($value == 2) {
                    $this->xPanel->addClause('where', 'active', '=', 0);
                }
            }
        );

        /*
        |--------------------------------------------------------------------------
        | COLUMNS AND FIELDS
        |--------------------------------------------------------------------------
        */
        // COLUMNS
        $this->xPanel->addColumn([
            'name'      => 'id',
            'label'     => '',
            'type'      => 'checkbox',
            'orderable' => false,
        ]);
        $this->xPanel->addColumn([
            'name'  => 'code',
            'label' => 'Code',
        ]);
        $this->xPanel->addColumn([
            'name'  => 'name',
            'label' => trans('admin.Name'),
        ]);
        $this->xPanel->addColumn([
            'name'          => 'discount_text',
            'label'         => 'Discount',
            'type'          => 'model_function',
            'function_name' => 'getDiscountTextAttribute',
        ]);
        $this->xPanel->addColumn([
            'name'  => 'times_used',
            'label' => 'Used',
        ]);
        $this->xPanel->addColumn([
            'name'  => 'usage_limit',
            'label' => 'Max Uses',
        ]);
        $this->xPanel->addColumn([
            'name'  => 'starts_at',
            'label' => 'Valid From',
            'type'  => 'datetime',
        ]);
        $this->xPanel->addColumn([
            'name'  => 'expires_at',
            'label' => 'Valid Until',
            'type'  => 'datetime',
        ]);
        $this->xPanel->addColumn([
            'name'          => 'active',
            'label'         => trans('admin.Active'),
            'type'          => 'model_function',
            'function_name' => 'getActiveHtml',
            'on_display'    => 'checkbox',
        ]);

        // FIELDS
        $this->xPanel->addField([
            'name'              => 'code',
            'label'             => 'Coupon Code',
            'type'              => 'text',
            'attributes'        => [
                'placeholder'  => 'e.g., SAVE20',
                'style'        => 'text-transform: uppercase',
            ],
            'hint'              => 'Unique code customers will enter (auto-uppercased)',
            'wrapperAttributes' => [
                'class' => 'col-md-6',
            ],
        ]);
        $this->xPanel->addField([
            'name'              => 'name',
            'label'             => trans('admin.Name'),
            'type'              => 'text',
            'attributes'        => [
                'placeholder' => 'e.g., Summer Sale 20% Off',
            ],
            'hint'              => 'Internal name for this coupon',
            'wrapperAttributes' => [
                'class' => 'col-md-6',
            ],
        ]);
        $this->xPanel->addField([
            'name'              => 'discount_type',
            'label'             => 'Discount Type',
            'type'              => 'select_from_array',
            'options'           => [
                'percentage' => 'Percentage (%)',
                'fixed'      => 'Fixed Amount',
            ],
            'default'           => 'percentage',
            'wrapperAttributes' => [
                'class' => 'col-md-6',
            ],
        ]);
        $this->xPanel->addField([
            'name'              => 'discount_value',
            'label'             => 'Discount Value',
            'type'              => 'number',
            'attributes'        => [
                'placeholder' => 'e.g., 20',
                'min'         => 0,
                'step'        => '0.01',
            ],
            'hint'              => 'For percentage, use 20 for 20%. For fixed, enter the amount.',
            'wrapperAttributes' => [
                'class' => 'col-md-6',
            ],
        ]);
        $this->xPanel->addField([
            'name'              => 'min_order_amount',
            'label'             => 'Minimum Order Amount',
            'type'              => 'number',
            'attributes'        => [
                'placeholder' => '0.00',
                'min'         => 0,
                'step'        => '0.01',
            ],
            'hint'              => 'Minimum purchase amount required (0 = no minimum)',
            'wrapperAttributes' => [
                'class' => 'col-md-6',
            ],
        ]);
        $this->xPanel->addField([
            'name'              => 'max_discount_amount',
            'label'             => 'Maximum Discount Amount',
            'type'              => 'number',
            'attributes'        => [
                'placeholder' => 'Leave empty for no limit',
                'min'         => 0,
                'step'        => '0.01',
            ],
            'hint'              => 'Cap the discount at this amount (useful for percentage discounts)',
            'wrapperAttributes' => [
                'class' => 'col-md-6',
            ],
        ]);
        $this->xPanel->addField([
            'name'              => 'usage_limit',
            'label'             => 'Maximum Total Uses',
            'type'              => 'number',
            'attributes'        => [
                'placeholder' => 'Leave empty for unlimited',
                'min'         => 0,
                'step'        => 1,
            ],
            'hint'              => 'Total number of times this coupon can be used',
            'wrapperAttributes' => [
                'class' => 'col-md-6',
            ],
        ]);
        $this->xPanel->addField([
            'name'              => 'usage_limit_per_user',
            'label'             => 'Max Uses Per User',
            'type'              => 'number',
            'attributes'        => [
                'placeholder' => 'Leave empty for unlimited',
                'min'         => 0,
                'step'        => 1,
            ],
            'default'           => 1,
            'hint'              => 'How many times a single user can use this coupon',
            'wrapperAttributes' => [
                'class' => 'col-md-6',
            ],
        ]);
        $this->xPanel->addField([
            'name'              => 'starts_at',
            'label'             => 'Valid From',
            'type'              => 'datetime',
            'hint'              => 'When the coupon becomes active',
            'wrapperAttributes' => [
                'class' => 'col-md-6',
            ],
        ]);
        $this->xPanel->addField([
            'name'              => 'expires_at',
            'label'             => 'Valid Until',
            'type'              => 'datetime',
            'hint'              => 'When the coupon expires',
            'wrapperAttributes' => [
                'class' => 'col-md-6',
            ],
        ]);
        $this->xPanel->addField([
            'name'              => 'applicable_to',
            'label'             => 'Applicable To',
            'type'              => 'select2_from_array',
            'options'           => [
                'all'              => 'All Products/Services',
                'packages'         => 'Job Packages Only',
                'courses'          => 'Courses Only',
                'subscriptions'    => 'Subscriptions Only',
                'resume_packages'  => 'Resume Packages Only',
            ],
            'allows_multiple'   => true,
            'default'           => ['all'],
            'hint'              => 'What this coupon can be applied to',
            'wrapperAttributes' => [
                'class' => 'col-md-6',
            ],
        ]);
        $this->xPanel->addField([
            'name'       => 'description',
            'label'      => trans('admin.Description'),
            'type'       => 'textarea',
            'attributes' => [
                'placeholder' => 'Internal notes about this coupon...',
                'rows'        => 3,
            ],
        ]);
        $this->xPanel->addField([
            'name'              => 'is_first_order_only',
            'label'             => 'First Order Only',
            'type'              => 'checkbox_switch',
            'hint'              => 'Only allow this coupon for users who have never made a purchase',
            'wrapperAttributes' => [
                'class' => 'col-md-6',
            ],
        ]);
        $this->xPanel->addField([
            'name'              => 'active',
            'label'             => trans('admin.Active'),
            'type'              => 'checkbox_switch',
            'default'           => '1',
            'wrapperAttributes' => [
                'class' => 'col-md-6',
            ],
        ]);
    }

    public function store()
    {
        // Uppercase the code
        request()->merge(['code' => strtoupper(request()->input('code'))]);

        return parent::storeCrud();
    }

    public function update()
    {
        // Uppercase the code
        request()->merge(['code' => strtoupper(request()->input('code'))]);

        return parent::updateCrud();
    }
}
