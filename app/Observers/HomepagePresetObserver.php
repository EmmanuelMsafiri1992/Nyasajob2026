<?php

namespace App\Observers;

use App\Models\HomepagePreset;

class HomepagePresetObserver
{
    /**
     * Handle the HomepagePreset "created" event.
     */
    public function created(HomepagePreset $homepagePreset): void
    {
        $this->clearCache($homepagePreset);
    }

    /**
     * Handle the HomepagePreset "updated" event.
     */
    public function updated(HomepagePreset $homepagePreset): void
    {
        $this->clearCache($homepagePreset);
    }

    /**
     * Handle the HomepagePreset "deleted" event.
     */
    public function deleted(HomepagePreset $homepagePreset): void
    {
        $this->clearCache($homepagePreset);
    }

    /**
     * Clear related cache
     */
    private function clearCache(HomepagePreset $homepagePreset): void
    {
        try {
            $cacheKeys = [
                'homepage_presets_list',
                'homepage_preset_default',
            ];

            foreach ($cacheKeys as $key) {
                cache()->forget($key);
            }
        } catch (\Exception $e) {
            // Silently fail
        }
    }
}
