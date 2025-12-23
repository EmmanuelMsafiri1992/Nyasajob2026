<?php
namespace App\Http\Requests;

class ResetPasswordRequest extends AuthRequest
{
	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules()
	{
		$rules = parent::rules();
		
		$rules['token'] = ['required'];
		$rules['password'] = ['required', 'confirmed'];
		
		$rules = $this->validEmailRules('email', $rules);
		$rules = $this->validPhoneNumberRules('phone', $rules);
		
		$rules['phone_country'] = ['required_with:phone'];
		
		return $this->validPasswordRules('password', $rules);
	}
}
