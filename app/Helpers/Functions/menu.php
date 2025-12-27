<?php
/*
 * Menu Helper Functions
 */

use App\Models\Menu;
use Illuminate\Support\Collection;

if (!function_exists('getMenu')) {
    /**
     * Get menu by location
     *
     * @param string $location header|footer|sidebar
     * @return Menu|null
     */
    function getMenu(string $location): ?Menu
    {
        return Menu::getByLocation($location);
    }
}

if (!function_exists('getMenuItems')) {
    /**
     * Get visible menu items for a location
     *
     * @param string $location header|footer|sidebar
     * @return Collection
     */
    function getMenuItems(string $location): Collection
    {
        $menu = getMenu($location);

        if (!$menu) {
            return collect();
        }

        return $menu->getVisibleTree();
    }
}

if (!function_exists('renderMenu')) {
    /**
     * Render menu HTML
     *
     * @param string $location header|footer|sidebar
     * @param string $view View to use for rendering
     * @return string
     */
    function renderMenu(string $location, string $view = 'default'): string
    {
        $items = getMenuItems($location);

        if ($items->isEmpty()) {
            return '';
        }

        $viewPath = 'layouts.inc.menu.' . $view;

        if (!view()->exists($viewPath)) {
            $viewPath = 'layouts.inc.menu.default';
        }

        return view($viewPath, compact('items', 'location'))->render();
    }
}

if (!function_exists('hasMenu')) {
    /**
     * Check if a menu exists for a location
     *
     * @param string $location
     * @return bool
     */
    function hasMenu(string $location): bool
    {
        $menu = getMenu($location);
        return $menu !== null && $menu->items->isNotEmpty();
    }
}
