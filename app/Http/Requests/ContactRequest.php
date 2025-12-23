<?php
namespace App\Http\Requests;

use App\Rules\BetweenRule;

class ContactRequest extends Request
{
	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules()
	{
		$rules = [
			'first_name' => ['required', 'string', new BetweenRule(2, 100)],
			'last_name'  => ['required', 'string', new BetweenRule(2, 100)],
			'email'      => ['required'],
			'message'    => ['required', 'string', new BetweenRule(5, 500)],
		];
		
		$rules = $this->validEmailRules('email', $rules);
		
		if (isFromApi()) {
			$rules['country_code'] = ['required'];
			$rules['country_name'] = ['required'];
		}
		
		return $this->captchaRules($rules);
	}
}
