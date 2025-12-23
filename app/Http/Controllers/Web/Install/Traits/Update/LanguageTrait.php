<?php
namespace App\Http\Controllers\Web\Install\Traits\Update;

use App\Helpers\Lang\LangManager;
use App\Models\Language;

trait LanguageTrait
{
	/**
	 * (Try to) Fill the missing lines in all languages files
	 */
	private function syncLanguageFilesLines()
	{
		// Don't sync. languages files lines for dev environment
		if (isDevEnv(url()->current())) {
			return;
		}
		
		// Get the current Default Language
		$defaultLang = Language::where('default', 1)->first();
		if (empty($defaultLang)) {
			return;
		}
		
		// Init. the language manager
		$manager = new LangManager();
		
		// SYNC. THE LANGUAGES FILES LINES
		// Get all the others languages (from DB)
		$languages = Language::where('abbr', '!=', $defaultLang->abbr)->get();
		if ($languages->count() > 0) {
			foreach ($languages as $language) {
				$manager->syncLines($defaultLang->abbr, $language->abbr);
			}
		}
	}
}
