<?php
namespace App\Observers\Traits;

use App\Helpers\DBTool;
use App\Models\Language;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Admin\Panel\Library\Traits\Models\SpatieTranslatable\HasTranslations;

trait LanguageTrait
{
	/**
	 * UPDATING - Set default language (Call this method at last)
	 *
	 * @param $abbr
	 */
	public static function setDefaultLanguage($abbr)
	{
		// Unset the old default language
		Language::whereIn('active', [0, 1])->update(['default' => 0]);
		
		// Set the new default language
		Language::where('abbr', $abbr)->update(['default' => 1]);
		
		// Update the Default App Locale
		self::updateDefaultAppLocale($abbr);
	}
	
	// PRIVATE METHODS
	
	/**
	 * Update the Default App Locale
	 *
	 * @param $locale
	 */
	private static function updateDefaultAppLocale($locale)
	{
		setEnvValue('APP_LOCALE', $locale);
	}
	
	/**
	 * Forgetting all DB translations for a specific locale
	 *
	 * @param $locale
	 */
	protected function forgetAllTranslations($locale)
	{
		// JSON columns manipulation is only available in:
		// MySQL 5.7 or above & MariaDB 10.2.3 or above
		$jsonMethodsAreAvailable = (
			(!DBTool::isMariaDB() && DBTool::isMySqlMinVersion('5.7'))
			|| (DBTool::isMariaDB() && DBTool::isMySqlMinVersion('10.2.3'))
		);
		if (! $jsonMethodsAreAvailable) {
			return;
		}
		
		$modelFiles = DBTool::getAppModelsFiles();
		
		$namespace = 'App\\Models\\';
		if (is_array($modelFiles) && count($modelFiles) > 0) {
			foreach ($modelFiles as $filePath) {
				$filename = last(explode(DIRECTORY_SEPARATOR, $filePath));
				$modelName = head(explode('.', $filename));
				
				eval('$model = new ' . $namespace . $modelName . '();');
				
				if (isset($model) && $model instanceof Model) {
					$isTranslatableModel = (
						property_exists($model, 'translatable')
						&& (
							isset($model->translatable)
							&& is_array($model->translatable)
							&& !empty($model->translatable)
						)
						&& in_array(HasTranslations::class, class_uses($model))
					);
					
					if ($isTranslatableModel) {
						$tableName = $model->getTable();
						foreach ($model->translatable as $column) {
							$value = 'JSON_REMOVE(' . $column . ', \'$."' . $locale . '"\')';
							// $filter = $column . ' REGEXP \'.+"' . $locale . '":.+\'';
							$filter = $column . ' LIKE \'%"' . $locale . '":%\'';
							
							DB::table($tableName)
								->whereNotNull($column)
								->whereRaw($column . ' != ""')
								->whereRaw($filter)
								->update([
									$column => DB::raw($value),
								]);
						}
					}
				}
			}
		}
	}
}
