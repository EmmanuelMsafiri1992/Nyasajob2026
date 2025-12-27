<?php

namespace App\Observers;

use App\Models\MenuItem;

class MenuItemObserver
{
    /**
     * Handle the MenuItem "created" event.
     */
    public function created(MenuItem $menuItem): void
    {
        $this->clearCache($menuItem);
    }

    /**
     * Handle the MenuItem "updated" event.
     */
    public function updated(MenuItem $menuItem): void
    {
        $this->clearCache($menuItem);
    }

    /**
     * Handle the MenuItem "deleted" event.
     */
    public function deleted(MenuItem $menuItem): void
    {
        $this->clearCache($menuItem);
    }

    /**
     * Clear related cache
     */
    private function clearCache(MenuItem $menuItem): void
    {
        try {
            // Clear menu cache for this item's menu
            if ($menuItem->menu) {
                cache()->forget('menu_' . $menuItem->menu->location);
            }

            // Clear all menu caches to be safe
            $locations = ['header', 'footer', 'sidebar'];
            foreach ($locations as $location) {
                cache()->forget('menu_' . $location);
            }
        } catch (\Exception $e) {
            // Silently fail
        }
    }
}
