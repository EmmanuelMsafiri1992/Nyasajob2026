<?php

namespace App\Http\Controllers\Web\Admin;

use App\Models\Menu;
use App\Http\Controllers\Web\Admin\Panel\PanelController;

class MenuController extends PanelController
{
    public function setup(): void
    {
        $this->xPanel->setModel(Menu::class);
        $this->xPanel->setRoute(admin_uri('menus'));
        $this->xPanel->setEntityNameStrings(
            trans('admin.menu'),
            trans('admin.menus')
        );
        $this->xPanel->enableReorder('name', 1);
        $this->xPanel->allowAccess(['reorder']);
        $this->xPanel->denyAccess(['show']);

        // Eager load items count to prevent N+1 queries
        $this->xPanel->query = $this->xPanel->query->withCount('items');

        // Columns
        $this->xPanel->addColumn([
            'name'  => 'id',
            'label' => '',
            'type'  => 'checkbox',
            'orderable' => false,
        ]);
        $this->xPanel->addColumn([
            'name'  => 'name',
            'label' => trans('admin.Name'),
        ]);
        $this->xPanel->addColumn([
            'name'          => 'location',
            'label'         => trans('admin.Location'),
            'type'          => 'model_function',
            'function_name' => 'getLocationHtml',
        ]);
        $this->xPanel->addColumn([
            'name'          => 'items_count',
            'label'         => trans('admin.Items'),
            'type'          => 'model_function',
            'function_name' => 'getItemsCountHtml',
        ]);
        $this->xPanel->addColumn([
            'name'          => 'active',
            'label'         => trans('admin.Active'),
            'type'          => 'model_function',
            'function_name' => 'getActiveHtml',
        ]);

        // Buttons
        $this->xPanel->addButtonFromModelFunction('line', 'manage_items', 'manageItemsButton', 'beginning');

        // Filters
        $this->xPanel->addFilter(
            [
                'name'  => 'location',
                'type'  => 'dropdown',
                'label' => trans('admin.Location'),
            ],
            [
                'header' => trans('admin.Header'),
                'footer' => trans('admin.Footer'),
                'sidebar' => trans('admin.Sidebar'),
            ],
            function ($value) {
                $this->xPanel->addClause('where', 'location', '=', $value);
            }
        );

        $this->xPanel->addFilter(
            [
                'name'  => 'status',
                'type'  => 'dropdown',
                'label' => trans('admin.Status'),
            ],
            [
                1 => trans('admin.active'),
                0 => trans('admin.inactive'),
            ],
            function ($value) {
                $this->xPanel->addClause('where', 'active', '=', $value);
            }
        );

        // Fields
        $this->xPanel->addField([
            'name'       => 'name',
            'label'      => trans('admin.Name'),
            'type'       => 'text',
            'attributes' => ['placeholder' => trans('admin.Name')],
            'wrapperAttributes' => ['class' => 'col-md-6'],
        ]);
        $this->xPanel->addField([
            'name'       => 'slug',
            'label'      => trans('admin.Slug'),
            'type'       => 'text',
            'attributes' => ['placeholder' => trans('admin.Slug')],
            'wrapperAttributes' => ['class' => 'col-md-6'],
            'hint'       => trans('admin.slug_hint'),
        ]);
        $this->xPanel->addField([
            'name'    => 'location',
            'label'   => trans('admin.Location'),
            'type'    => 'select2_from_array',
            'options' => [
                'header'  => trans('admin.Header'),
                'footer'  => trans('admin.Footer'),
                'sidebar' => trans('admin.Sidebar'),
            ],
            'allows_null' => false,
            'wrapperAttributes' => ['class' => 'col-md-6'],
        ]);
        $this->xPanel->addField([
            'name'       => 'description',
            'label'      => trans('admin.Description'),
            'type'       => 'textarea',
            'attributes' => ['rows' => 2],
            'wrapperAttributes' => ['class' => 'col-md-6'],
        ]);
        $this->xPanel->addField([
            'name'  => 'active',
            'label' => trans('admin.Active'),
            'type'  => 'checkbox_switch',
        ]);
    }
}
