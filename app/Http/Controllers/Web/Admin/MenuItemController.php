<?php

namespace App\Http\Controllers\Web\Admin;

use App\Models\Menu;
use App\Models\MenuItem;
use App\Models\Page;
use App\Models\Category;
use App\Http\Controllers\Web\Admin\Panel\PanelController;
use Illuminate\Http\Request;

class MenuItemController extends PanelController
{
    protected ?int $menuId = null;
    protected ?Menu $menu = null;

    public function setup(): void
    {
        // Get menu ID from route
        $this->menuId = (int) request()->route('menuId');
        $this->menu = Menu::withoutGlobalScopes()->find($this->menuId);

        if (!$this->menu) {
            abort(404, 'Menu not found');
        }

        $this->xPanel->setModel(MenuItem::class);
        $this->xPanel->setRoute(admin_uri('menus/' . $this->menuId . '/items'));
        $this->xPanel->setEntityNameStrings(
            trans('admin.menu_item'),
            trans('admin.menu_items')
        );
        $this->xPanel->enableReorder('title', 2);
        $this->xPanel->allowAccess(['reorder']);
        $this->xPanel->denyAccess(['show']);

        // Only show items for this menu
        $this->xPanel->addClause('where', 'menu_id', '=', $this->menuId);

        // Order by nested set
        $this->xPanel->orderBy('lft');

        // Columns
        $this->xPanel->addColumn([
            'name'  => 'id',
            'label' => '',
            'type'  => 'checkbox',
            'orderable' => false,
        ]);
        $this->xPanel->addColumn([
            'name'          => 'title',
            'label'         => trans('admin.Title'),
            'type'          => 'model_function',
            'function_name' => 'getTitleWithIconHtml',
        ]);
        $this->xPanel->addColumn([
            'name'          => 'type',
            'label'         => trans('admin.Type'),
            'type'          => 'model_function',
            'function_name' => 'getTypeHtml',
        ]);
        $this->xPanel->addColumn([
            'name'  => 'url',
            'label' => trans('admin.URL'),
        ]);
        $this->xPanel->addColumn([
            'name'          => 'active',
            'label'         => trans('admin.Active'),
            'type'          => 'model_function',
            'function_name' => 'getActiveHtml',
        ]);

        // Parent items for this menu
        $parentOptions = MenuItem::withoutGlobalScopes()
            ->where('menu_id', $this->menuId)
            ->whereNull('parent_id')
            ->pluck('title', 'id')
            ->toArray();

        // Page options
        $pageOptions = [];
        if (class_exists(Page::class)) {
            $pageOptions = Page::withoutGlobalScopes()
                ->where('active', true)
                ->pluck('title', 'id')
                ->toArray();
        }

        // Category options
        $categoryOptions = [];
        if (class_exists(Category::class)) {
            $categoryOptions = Category::withoutGlobalScopes()
                ->whereNull('parent_id')
                ->where('active', true)
                ->pluck('name', 'id')
                ->toArray();
        }

        // Fields
        $this->xPanel->addField([
            'name'  => 'menu_id',
            'type'  => 'hidden',
            'value' => $this->menuId,
        ]);
        $this->xPanel->addField([
            'name'       => 'title',
            'label'      => trans('admin.Title'),
            'type'       => 'text',
            'attributes' => ['placeholder' => trans('admin.menu_item_title_placeholder')],
            'wrapperAttributes' => ['class' => 'col-md-6'],
        ]);
        $this->xPanel->addField([
            'name'    => 'type',
            'label'   => trans('admin.Type'),
            'type'    => 'select2_from_array',
            'options' => [
                'link'     => trans('admin.menu_type_link'),
                'button'   => trans('admin.menu_type_button'),
                'dropdown' => trans('admin.menu_type_dropdown'),
                'divider'  => trans('admin.menu_type_divider'),
                'text'     => trans('admin.menu_type_text'),
                'page'     => trans('admin.menu_type_page'),
                'category' => trans('admin.menu_type_category'),
            ],
            'allows_null' => false,
            'wrapperAttributes' => ['class' => 'col-md-6'],
        ]);
        $this->xPanel->addField([
            'name'    => 'parent_id',
            'label'   => trans('admin.Parent'),
            'type'    => 'select2_from_array',
            'options' => ['' => '-- ' . trans('admin.None') . ' --'] + $parentOptions,
            'allows_null' => true,
            'wrapperAttributes' => ['class' => 'col-md-6'],
            'hint'    => trans('admin.menu_item_parent_hint'),
        ]);
        $this->xPanel->addField([
            'name'       => 'url',
            'label'      => trans('admin.URL'),
            'type'       => 'text',
            'attributes' => ['placeholder' => 'https://...'],
            'wrapperAttributes' => ['class' => 'col-md-6'],
            'hint'       => trans('admin.menu_item_url_hint'),
        ]);
        $this->xPanel->addField([
            'name'       => 'route_name',
            'label'      => trans('admin.Route Name'),
            'type'       => 'text',
            'attributes' => ['placeholder' => 'route.name'],
            'wrapperAttributes' => ['class' => 'col-md-6'],
            'hint'       => trans('admin.menu_item_route_hint'),
        ]);
        $this->xPanel->addField([
            'name'       => 'icon',
            'label'      => trans('admin.Icon'),
            'type'       => 'text',
            'attributes' => ['placeholder' => 'fa-solid fa-home'],
            'wrapperAttributes' => ['class' => 'col-md-6'],
            'hint'       => trans('admin.menu_item_icon_hint'),
        ]);
        $this->xPanel->addField([
            'name'    => 'target',
            'label'   => trans('admin.Target'),
            'type'    => 'select2_from_array',
            'options' => [
                '_self'  => trans('admin.Same Window'),
                '_blank' => trans('admin.New Window'),
            ],
            'allows_null' => false,
            'wrapperAttributes' => ['class' => 'col-md-6'],
        ]);
        $this->xPanel->addField([
            'name'       => 'css_class',
            'label'      => trans('admin.CSS Class'),
            'type'       => 'text',
            'attributes' => ['placeholder' => 'btn btn-primary'],
            'wrapperAttributes' => ['class' => 'col-md-6'],
        ]);

        // Visibility conditions
        $this->xPanel->addField([
            'name'  => 'separator_visibility',
            'type'  => 'custom_html',
            'value' => '<hr><h5>' . trans('admin.visibility_conditions') . '</h5>',
        ]);
        $this->xPanel->addField([
            'name'  => 'visibility_conditions[auth_required]',
            'label' => trans('admin.auth_required'),
            'type'  => 'checkbox',
            'wrapperAttributes' => ['class' => 'col-md-3'],
        ]);
        $this->xPanel->addField([
            'name'  => 'visibility_conditions[guest_only]',
            'label' => trans('admin.guest_only'),
            'type'  => 'checkbox',
            'wrapperAttributes' => ['class' => 'col-md-3'],
        ]);

        $this->xPanel->addField([
            'name'  => 'active',
            'label' => trans('admin.Active'),
            'type'  => 'checkbox_switch',
        ]);
    }

    /**
     * Custom index to show menu info
     */
    public function index()
    {
        $this->xPanel->addButton('top', 'back_to_menus', 'view', 'admin.buttons.back_to_menus');

        return parent::index();
    }

    /**
     * Override store to set menu_id
     */
    public function store(Request $request)
    {
        $request->merge(['menu_id' => $this->menuId]);
        return parent::store($request);
    }
}
