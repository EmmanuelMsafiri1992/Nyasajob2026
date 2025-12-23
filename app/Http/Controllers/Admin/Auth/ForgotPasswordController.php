<?php
namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Admin\Controller;

class ForgotPasswordController extends Controller
{
	/**
	 * PasswordController constructor.
	 */
	public function __construct()
	{
		$this->middleware('guest');
		
		parent::__construct();
	}
	
	// -------------------------------------------------------
	// Laravel overwrites for loading admin views
	// -------------------------------------------------------
	
	/**
	 * Display the form to request a password reset link.
	 * NOTE: Not used with this admin theme.
	 *
	 * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
	 */
	public function showLinkRequestForm()
	{
		return view('admin.auth.passwords.email', ['title' => trans('admin.reset_password')]);
	}
}
