<?php
namespace App\Exceptions\Traits;

use App\Notifications\ExceptionOccurred;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Route;
use Throwable;

trait NotificationTrait
{
	/**
	 * @param \Throwable $e
	 * @return void
	 */
	public function sendNotification(Throwable $e)
	{
		if (str_contains(Route::currentRouteAction(), 'InstallController')) {
			return;
		}
		
		if (!config('larapen.core.sendNotificationOnError')) {
			return;
		}
		
		try {
			
			$content = [];
			// The request
			$content['method'] = request()->getMethod();
			$content['url'] = request()->fullUrl();
			$content['ip'] = request()->ip();
			$content['userAgent'] = request()->server('HTTP_USER_AGENT');
			$content['referer'] = request()->server('HTTP_REFERER');
			$content['body'] = request()->all();
			
			// The error
			$content['message'] = $e->getMessage();
			$content['file'] = $e->getFile();
			$content['line'] = $e->getLine();
			$content['trace'] = $e->getTrace();
			
			// Send Notification
			Notification::route('mail', config('settings.app.email'))->notify(new ExceptionOccurred($content));
			
		} catch (Throwable $e) {
			// dd($e); // Debug
		}
	}
}
