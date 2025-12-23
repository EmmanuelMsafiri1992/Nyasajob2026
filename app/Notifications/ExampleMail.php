<?php
namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;

class ExampleMail extends Notification implements ShouldQueue
{
	use Queueable;
	
	public function __construct() { }
	
	public function via($notifiable)
	{
		if (isDemoDomain()) {
			return [];
		}
		
		return ['mail'];
	}
	
	public function toMail($notifiable)
	{
		return (new MailMessage)
			->subject(trans('mail.email_example_title', ['appName' => config('app.name')]))
			->greeting(trans('mail.email_example_content_1'))
			->line(trans('mail.email_example_content_2', ['appName' => config('app.name')]))
			->salutation(trans('mail.footer_salutation', ['appName' => config('app.name')]));
	}
}
