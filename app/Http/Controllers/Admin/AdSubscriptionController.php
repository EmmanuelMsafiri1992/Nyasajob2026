<?php
/**
 * Nyasajob - Job Board Web Application
 * Copyright (c) BeDigit. All Rights Reserved
 *
 * Website: https://laraclassifier.com/jobclass
 *
 * LICENSE
 * -------
 * This software is furnished under a license and may be used and copied
 * only in accordance with the terms of such license and with the inclusion
 * of the above copyright notice. If you Purchased from CodeCanyon,
 * Please read the full License from here - http://codecanyon.net/licenses/standard
 */

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Panel\PanelController;

class AdSubscriptionController extends PanelController
{
	public function setup()
	{
		/*
		|--------------------------------------------------------------------------
		| BASIC CRUD INFORMATION
		|--------------------------------------------------------------------------
		*/
		$this->xPanel->setModel('App\Models\AdSubscription');
		$this->xPanel->setRoute(admin_uri('ad_subscriptions'));
		$this->xPanel->setEntityNameStrings(trans('admin.ad_subscription'), trans('admin.ad_subscriptions'));
		$this->xPanel->denyAccess(['create']);
		$this->xPanel->allowAccess(['update', 'delete', 'show']);
		$this->xPanel->orderBy('created_at', 'DESC');

		$this->xPanel->addButtonFromModelFunction('top', 'bulk_deletion_btn', 'bulkDeletionBtn', 'end');

		// Filters
		// -----------------------
		$this->xPanel->addFilter([
			'name'  => 'id',
			'type'  => 'text',
			'label' => 'ID',
		],
			false,
			function ($value) {
				$this->xPanel->addClause('where', 'id', '=', $value);
			});
		// -----------------------
		$this->xPanel->addFilter([
			'name'  => 'user_id',
			'type'  => 'text',
			'label' => trans('admin.User ID'),
		],
			false,
			function ($value) {
				$this->xPanel->addClause('where', 'user_id', '=', $value);
			});
		// -----------------------
		$this->xPanel->addFilter([
			'name'  => 'status',
			'type'  => 'dropdown',
			'label' => trans('admin.Status'),
		], [
			'pending' => trans('admin.Pending'),
			'active' => trans('admin.Active'),
			'expired' => trans('admin.Expired'),
			'cancelled' => trans('admin.Cancelled'),
		], function ($value) {
			$this->xPanel->addClause('where', 'status', '=', $value);
		});

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
			'name'  => 'id',
			'label' => 'ID',
		]);
		$this->xPanel->addColumn([
			'name'          => 'user_id',
			'label'         => trans('admin.User'),
			'type'          => 'model_function',
			'function_name' => 'getUserNameHtml',
		]);
		$this->xPanel->addColumn([
			'name'          => 'ad_package_id',
			'label'         => trans('admin.Package'),
			'type'          => 'model_function',
			'function_name' => 'getPackageNameHtml',
		]);
		$this->xPanel->addColumn([
			'name'          => 'payment_method_id',
			'label'         => trans('admin.Payment Method'),
			'type'          => 'model_function',
			'function_name' => 'getPaymentMethodNameHtml',
		]);
		$this->xPanel->addColumn([
			'name'  => 'transaction_id',
			'label' => trans('admin.Transaction ID'),
		]);
		$this->xPanel->addColumn([
			'name'          => 'amount',
			'label'         => trans('admin.Amount'),
			'type'          => 'model_function',
			'function_name' => 'getAmountHtml',
		]);
		$this->xPanel->addColumn([
			'name'  => 'status',
			'label' => trans('admin.Status'),
		]);
		$this->xPanel->addColumn([
			'name'  => 'starts_at',
			'label' => trans('admin.Starts At'),
			'type'  => 'datetime',
		]);
		$this->xPanel->addColumn([
			'name'  => 'expires_at',
			'label' => trans('admin.Expires At'),
			'type'  => 'datetime',
		]);
		$this->xPanel->addColumn([
			'name'          => 'active',
			'label'         => trans('admin.Active'),
			'type'          => 'model_function',
			'function_name' => 'getActiveHtml',
			'on_display'    => 'checkbox',
		]);
		$this->xPanel->addColumn([
			'name'  => 'created_at',
			'label' => trans('admin.Date'),
			'type'  => 'datetime',
		]);

		// FIELDS
		$this->xPanel->addField([
			'name'       => 'user_id',
			'label'      => trans('admin.User'),
			'type'       => 'select2',
			'entity'     => 'user',
			'model'      => 'App\Models\User',
			'attribute'  => 'name',
			'attributes' => [
				'disabled' => 'disabled',
			],
		]);
		$this->xPanel->addField([
			'name'       => 'ad_package_id',
			'label'      => trans('admin.Package'),
			'type'       => 'select2',
			'entity'     => 'package',
			'model'      => 'App\Models\AdPackage',
			'attribute'  => 'name',
			'attributes' => [
				'disabled' => 'disabled',
			],
		]);
		$this->xPanel->addField([
			'name'       => 'payment_method_id',
			'label'      => trans('admin.Payment Method'),
			'type'       => 'select2',
			'entity'     => 'paymentMethod',
			'model'      => 'App\Models\PaymentMethod',
			'attribute'  => 'display_name',
			'attributes' => [
				'disabled' => 'disabled',
			],
		]);
		$this->xPanel->addField([
			'name'       => 'transaction_id',
			'label'      => trans('admin.Transaction ID'),
			'type'       => 'text',
			'attributes' => [
				'disabled' => 'disabled',
			],
		]);
		$this->xPanel->addField([
			'name'       => 'amount',
			'label'      => trans('admin.Amount'),
			'type'       => 'text',
			'attributes' => [
				'disabled' => 'disabled',
			],
		]);
		$this->xPanel->addField([
			'name'       => 'currency_code',
			'label'      => trans('admin.Currency'),
			'type'       => 'text',
			'attributes' => [
				'disabled' => 'disabled',
			],
		]);
		$this->xPanel->addField([
			'name'    => 'status',
			'label'   => trans('admin.Status'),
			'type'    => 'select_from_array',
			'options' => [
				'pending' => trans('admin.Pending'),
				'active' => trans('admin.Active'),
				'expired' => trans('admin.Expired'),
				'cancelled' => trans('admin.Cancelled'),
			],
		]);
		$this->xPanel->addField([
			'name'  => 'starts_at',
			'label' => trans('admin.Starts At'),
			'type'  => 'datetime_picker',
		]);
		$this->xPanel->addField([
			'name'  => 'expires_at',
			'label' => trans('admin.Expires At'),
			'type'  => 'datetime_picker',
		]);
		$this->xPanel->addField([
			'name'  => 'active',
			'label' => trans('admin.Active'),
			'type'  => 'checkbox_switch',
		]);
	}
}
