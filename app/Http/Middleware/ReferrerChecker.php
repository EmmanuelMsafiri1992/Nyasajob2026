<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ReferrerChecker
{
	
	private function accessForbidden(string $message = 'Unauthorized')
	{
		echo '<pre>';
		print_r($message);
		echo '</pre>';
		exit();
	}
}
