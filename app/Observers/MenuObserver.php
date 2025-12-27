<?php

namespace App\Observers;

use App\Models\Menu;

class MenuObserver
{
    /**
     * Handle the Menu "created" event.
     */
    public function created(Menu $menu): void
    {
        $this->clearCache($menu);
    }

    /**
     * Handle the Menu "updated" event.
     */
    public function updated(Menu $menu): void
    {
        $this->clearCache($menu);
    }

    /**
     * Handle the Menu "deleted" event.
     */
    public function deleted(Menu $menu): void
    {
        $this->clearCache($menu);
    }

    /**
     * Clear related cache
     */
    private function clearCache(Menu $menu): void
    {
        try {
            // Clear specific menu cache
            cache()->forget('menu_' . $menu->location);

            // Clear general menu caches
            $locations = ['header', 'footer', 'sidebar'];
            foreach ($locations as $location) {
                cache()->forget('menu_' . $location);
            }
        } catch (\Exception $e) {
            // Silently fail
        }
    }
}
