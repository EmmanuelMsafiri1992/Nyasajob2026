<?php
namespace App\Observers;

use App\Models\Advertising;

class AdvertisingObserver
{
	/**
	 * Listen to the Entry saved event.
	 *
	 * @param Advertising $advertising
	 * @return void
	 */
	public function saved(Advertising $advertising)
	{
		// Removing Entries from the Cache
		$this->clearCache($advertising);
	}
	
	/**
	 * Listen to the Entry deleted event.
	 *
	 * @param Advertising $advertising
	 * @return void
	 */
	public function deleted(Advertising $advertising)
	{
		// Removing Entries from the Cache
		$this->clearCache($advertising);
	}
	
	/**
	 * Removing the Entity's Entries from the Cache
	 *
	 * @param $advertising
	 */
	private function clearCache($advertising)
	{
		try {
			cache()->forget('advertising.top');
			cache()->forget('advertising.bottom');
			cache()->forget('advertising.auto');
		} catch (\Exception $e) {}
	}
}
