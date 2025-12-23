<?php
namespace App\Http\Middleware;

use App\Http\Middleware\InputRequest\CheckboxToDatetime;
use App\Http\Middleware\InputRequest\UploadFile;
use App\Http\Middleware\InputRequest\XssProtection;
use Closure;
use Illuminate\Http\Request;

class InputRequest
{
	use CheckboxToDatetime, UploadFile, XssProtection;
	
	/**
	 * Apply Global Inputs to the API Calls
	 *
	 * @param \Illuminate\Http\Request $request
	 * @param \Closure $next
	 * @return mixed
	 */
	public function handle(Request $request, Closure $next)
	{
		$request = $this->applyCheckboxToDatetime($request);
		$request = $this->convertBase64FileToUploadedFile($request);
		$request = $this->applyXssProtection($request);
		
		return $next($request);
	}
}
