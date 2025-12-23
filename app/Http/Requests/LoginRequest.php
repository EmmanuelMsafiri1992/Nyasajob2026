<?php
namespace App\Http\Requests;

class LoginRequest extends AuthRequest
{
	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules()
	{
		if (!isFromApi()) {
			// If previous page is not the Login page...
			if (!str_contains(url()->previous(), config('routes.login', 'login'))) {
				// Save the previous URL to retrieve it after success or failed login.
				session()->put('url.intended', url()->previous());
			}
		}
		
		$rules = parent::rules();
		
		$rules['password'] = ['required'];
		
		return $rules;
	}
}
