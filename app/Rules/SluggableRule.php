<?php
namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class SluggableRule implements Rule
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
		$value = trim(strip_tags($value));
		$value = stripNonUtf($value);
		$value = slugify($value);
		
		if (!empty(trim($value))) {
			return true;
		} else {
			return false;
		}
	}
	
	/**
	 * Get the validation error message.
	 *
	 * @return string
	 */
	public function message()
	{
		return trans('validation.sluggable_rule');
	}
}
