<?php
namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class LocaleOfCountryRule implements Rule
{
	public $countryCode = null;
	
	public function __construct($countryCode)
	{
		$this->countryCode = $countryCode;
	}
	
	/**
	 * Determine if the validation rule passes.
	 * Check the Locale related to the Country Code.
	 *
	 * @param  string  $attribute
	 * @param  mixed  $value
	 * @return bool
	 */
	public function passes($attribute, $value)
	{
		$countryCode = $this->countryCode;
		$locales = (array)config('locales');
		
		$filtered = collect($locales)->filter(function ($item, $key) use ($countryCode) {
			return str_ends_with($key, '_' . $countryCode);
		});
		
		if ($filtered->isNotEmpty()) {
			return str_ends_with($value, '_' . $countryCode);
		}
		
		return isset($locales[$value]);
	}
	
	/**
	 * Get the validation error message.
	 *
	 * @return string
	 */
	public function message()
	{
		return trans('validation.locale_of_country_rule');
	}
}
