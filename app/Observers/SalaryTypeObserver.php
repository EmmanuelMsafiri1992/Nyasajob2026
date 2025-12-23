<?php
namespace App\Observers;

use App\Models\SalaryType;

class SalaryTypeObserver
{
	/**
	 * Listen to the Entry saved event.
	 *
	 * @param SalaryType $salaryType
	 * @return void
	 */
	public function saved(SalaryType $salaryType)
	{
		// Removing Entries from the Cache
		$this->clearCache($salaryType);
	}
	
	/**
	 * Listen to the Entry deleted event.
	 *
	 * @param SalaryType $salaryType
	 * @return void
	 */
	public function deleted(SalaryType $salaryType)
	{
		// Removing Entries from the Cache
		$this->clearCache($salaryType);
	}
	
	/**
	 * Removing the Entity's Entries from the Cache
	 *
	 * @param $salaryType
	 */
	private function clearCache($salaryType)
	{
		try {
			cache()->flush();
		} catch (\Exception $e) {
		}
	}
}
