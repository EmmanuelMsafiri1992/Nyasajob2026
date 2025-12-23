<?php
namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class BlacklistTitleRule implements Rule
{
	/**
	 * Determine if the validation rule passes.
	 *
	 * @param  string  $attribute
	 * @param  mixed  $value
	 * @return bool
	 */
	public function passes($attribute, $value)
	{
		$blacklistWord = new BlacklistWordRule();
		if (!$blacklistWord->passes($attribute, $value)) {
			return false;
		}
		
		// Banned all domain name from title
		$tlds = (array)config('tlds');
		if (is_array($tlds) && count($tlds) > 0) {
			foreach ($tlds as $tld => $label) {
				if (str_contains($value, '.' . strtolower($tld))) {
					return false;
				}
			}
		}
		
		return true;
	}
	
	/**
	 * Get the validation error message.
	 *
	 * @return string
	 */
	public function message()
	{
		return trans('validation.blacklist_title_rule');
	}
}
