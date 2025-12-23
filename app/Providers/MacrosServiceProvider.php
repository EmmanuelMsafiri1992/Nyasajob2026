<?php
namespace App\Providers;

use Illuminate\Support\Collection;
use Illuminate\Support\ServiceProvider;

class MacrosServiceProvider extends ServiceProvider
{
	/**
	 * Register services.
	 *
	 * @return void
	 */
	public function register()
	{
		//
	}
	
	/**
	 * Bootstrap services.
	 *
	 * @return void
	 */
	public function boot()
	{
		$macroFiles = glob(__DIR__ . '/../Macros/*.php');
		
		$macroFiles = Collection::make($macroFiles)->mapWithKeys(function ($path) {
			return [$path => pathinfo($path, PATHINFO_FILENAME)];
		})->reject(function ($macro) {
			return Collection::hasMacro($macro);
		});
		
		$macroFiles->each(function ($macro, $path) {
			require_once $path;
		});
	}
}
