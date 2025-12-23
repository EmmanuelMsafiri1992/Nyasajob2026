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

class ProductAdvertisementController extends PanelController
{
	public function setup()
	{
		/*
		|--------------------------------------------------------------------------
		| BASIC CRUD INFORMATION
		|--------------------------------------------------------------------------
		*/
		$this->xPanel->setModel('App\Models\ProductAdvertisement');
		$this->xPanel->setRoute(admin_uri('product_advertisements'));
		$this->xPanel->setEntityNameStrings(trans('admin.product_advertisement'), trans('admin.product_advertisements'));
		$this->xPanel->denyAccess(['create']);
		$this->xPanel->allowAccess(['update', 'delete', 'show']);
		$this->xPanel->orderBy('created_at', 'DESC');

		$this->xPanel->addButtonFromModelFunction('top', 'bulk_activation_btn', 'bulkActivationBtn', 'end');
		$this->xPanel->addButtonFromModelFunction('top', 'bulk_deactivation_btn', 'bulkDeactivationBtn', 'end');
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
			'name'  => 'title',
			'type'  => 'text',
			'label' => trans('admin.Title'),
		],
			false,
			function ($value) {
				$this->xPanel->addClause('where', 'title', 'LIKE', "%$value%");
			});
		// -----------------------
		$this->xPanel->addFilter([
			'name'  => 'status',
			'type'  => 'dropdown',
			'label' => trans('admin.Status'),
		], [
			'pending' => trans('admin.Pending'),
			'active' => trans('admin.Active'),
			'paused' => trans('admin.Paused'),
			'expired' => trans('admin.Expired'),
			'rejected' => trans('admin.Rejected'),
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
			'name'  => 'title',
			'label' => trans('admin.Title'),
		]);
		$this->xPanel->addColumn([
			'name'      => 'user_id',
			'label'     => trans('admin.User'),
			'type'      => 'select',
			'entity'    => 'user',
			'attribute' => 'name',
			'model'     => 'App\Models\User',
		]);
		$this->xPanel->addColumn([
			'name'  => 'status',
			'label' => trans('admin.Status'),
		]);
		$this->xPanel->addColumn([
			'name'  => 'impressions',
			'label' => trans('admin.Impressions'),
		]);
		$this->xPanel->addColumn([
			'name'  => 'clicks',
			'label' => trans('admin.Clicks'),
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
			'name'  => 'title',
			'label' => trans('admin.Title'),
			'type'  => 'text',
		]);
		$this->xPanel->addField([
			'name'  => 'description',
			'label' => trans('admin.Description'),
			'type'  => 'textarea',
		]);
		$this->xPanel->addField([
			'name'       => 'image_path',
			'label'      => trans('admin.Image'),
			'type'       => 'text',
			'attributes' => [
				'disabled' => 'disabled',
			],
			'hint'       => trans('admin.Image path or URL'),
		]);
		$this->xPanel->addField([
			'name'  => 'url',
			'label' => trans('admin.Product URL'),
			'type'  => 'url',
			'hint'  => trans('admin.External link to product or landing page'),
		]);
		$this->xPanel->addField([
			'name'    => 'status',
			'label'   => trans('admin.Status'),
			'type'    => 'select_from_array',
			'options' => [
				'pending' => trans('admin.Pending'),
				'active' => trans('admin.Active'),
				'paused' => trans('admin.Paused'),
				'expired' => trans('admin.Expired'),
				'rejected' => trans('admin.Rejected'),
			],
		]);
		$this->xPanel->addField([
			'name'       => 'impressions',
			'label'      => trans('admin.Impressions'),
			'type'       => 'number',
			'attributes' => [
				'disabled' => 'disabled',
			],
		]);
		$this->xPanel->addField([
			'name'       => 'clicks',
			'label'      => trans('admin.Clicks'),
			'type'       => 'number',
			'attributes' => [
				'disabled' => 'disabled',
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
