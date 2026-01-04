<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Web\Admin\Panel\PanelController;
use App\Models\ResumePackage;

class ResumePackageController extends PanelController
{
    public function setup()
    {
        /*
        |--------------------------------------------------------------------------
        | BASIC CRUD INFORMATION
        |--------------------------------------------------------------------------
        */
        $this->xPanel->setModel(ResumePackage::class);
        $this->xPanel->setRoute(admin_uri('resume-packages'));
        $this->xPanel->setEntityNameStrings('Resume Package', 'Resume Packages');
        $this->xPanel->enableReorder('name', 1);
        $this->xPanel->allowAccess(['reorder']);
        if (!request()->input('order')) {
            $this->xPanel->orderBy('sort_order');
        }

        $this->xPanel->addButtonFromModelFunction('top', 'bulk_activation_button', 'bulkActivationButton', 'end');
        $this->xPanel->addButtonFromModelFunction('top', 'bulk_deactivation_button', 'bulkDeactivationButton', 'end');
        $this->xPanel->addButtonFromModelFunction('top', 'bulk_deletion_button', 'bulkDeletionButton', 'end');

        // Filters
        $this->xPanel->disableSearchBar();
        $this->xPanel->addFilter(
            [
                'name'  => 'name',
                'type'  => 'text',
                'label' => mb_ucfirst(trans('admin.Name')),
            ],
            false,
            function ($value) {
                $this->xPanel->addClause('where', 'name', 'LIKE', "%$value%");
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
            'name'  => 'name',
            'label' => trans('admin.Name'),
        ]);
        $this->xPanel->addColumn([
            'name'  => 'credits',
            'label' => 'Credits',
        ]);
        $this->xPanel->addColumn([
            'name'  => 'price',
            'label' => trans('admin.Price'),
        ]);
        $this->xPanel->addColumn([
            'name'  => 'currency_code',
            'label' => trans('admin.Currency'),
        ]);
        $this->xPanel->addColumn([
            'name'  => 'validity_days',
            'label' => 'Validity (Days)',
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
            'name'              => 'name',
            'label'             => trans('admin.Name'),
            'type'              => 'text',
            'attributes'        => [
                'placeholder' => 'e.g., Starter Pack',
            ],
            'wrapperAttributes' => [
                'class' => 'col-md-6',
            ],
        ]);
        $this->xPanel->addField([
            'name'              => 'credits',
            'label'             => 'Number of Credits',
            'type'              => 'number',
            'attributes'        => [
                'placeholder' => 'e.g., 10',
                'min'         => 1,
                'step'        => 1,
            ],
            'hint'              => 'Number of candidate contacts the employer can unlock',
            'wrapperAttributes' => [
                'class' => 'col-md-6',
            ],
        ]);
        $this->xPanel->addField([
            'name'              => 'price',
            'label'             => trans('admin.Price'),
            'type'              => 'number',
            'attributes'        => [
                'placeholder' => 'e.g., 49.99',
                'min'         => 0,
                'step'        => '0.01',
            ],
            'hint'              => 'Set to 0 for a free package',
            'wrapperAttributes' => [
                'class' => 'col-md-6',
            ],
        ]);
        $this->xPanel->addField([
            'label'             => trans('admin.Currency'),
            'name'              => 'currency_code',
            'model'             => 'App\Models\Currency',
            'entity'            => 'currency',
            'attribute'         => 'code',
            'type'              => 'select2',
            'wrapperAttributes' => [
                'class' => 'col-md-6',
            ],
        ]);
        $this->xPanel->addField([
            'name'              => 'validity_days',
            'label'             => 'Validity Period (Days)',
            'type'              => 'number',
            'attributes'        => [
                'placeholder' => 'e.g., 30',
                'min'         => 1,
                'step'        => 1,
            ],
            'default'           => 30,
            'hint'              => 'How many days credits remain valid after purchase',
            'wrapperAttributes' => [
                'class' => 'col-md-6',
            ],
        ]);
        $this->xPanel->addField([
            'name'              => 'sort_order',
            'label'             => 'Sort Order',
            'type'              => 'number',
            'attributes'        => [
                'min'  => 0,
                'step' => 1,
            ],
            'default'           => 0,
            'hint'              => 'Lower numbers appear first',
            'wrapperAttributes' => [
                'class' => 'col-md-6',
            ],
        ]);
        $this->xPanel->addField([
            'name'       => 'description',
            'label'      => trans('admin.Description'),
            'type'       => 'textarea',
            'attributes' => [
                'placeholder' => 'Package description...',
                'rows'        => 4,
            ],
        ]);
        $this->xPanel->addField([
            'name'              => 'is_featured',
            'label'             => 'Featured Package',
            'type'              => 'checkbox_switch',
            'hint'              => 'Highlight this package as recommended',
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
        return parent::storeCrud();
    }

    public function update()
    {
        return parent::updateCrud();
    }
}
