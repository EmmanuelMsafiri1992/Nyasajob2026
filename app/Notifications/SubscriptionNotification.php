<?php
namespace App\Notifications;

use App\Models\Package;
use App\Models\PaymentMethod;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SubscriptionNotification extends Notification
{
	use Queueable;
	
	protected $payment;
	protected $user;
	protected Package $package;
	protected PaymentMethod $paymentMethod;
	
	public function __construct($payment, $user)
	{
		$this->payment = $payment;
		$this->user = $user;
		$this->package = Package::find($payment->package_id);
		$this->paymentMethod = PaymentMethod::find($payment->payment_method_id);
	}
	
	public function via($notifiable)
	{
		return ['mail'];
	}
	
	public function toMail($notifiable)
	{
		return (new MailMessage)
			->subject(trans('mail.subscription_notification_title'))
			->greeting(trans('mail.subscription_notification_content_1'))
			->line(trans('mail.subscription_notification_content_2', [
				'userName'    => $this->user->name,
				'packageName' => !empty($this->package->short_name)
					? $this->package->short_name
					: $this->package->name,
			]))
			->line('<br>')
			->line(trans('mail.subscription_notification_content_3', [
				'packageName'       => !empty($this->package->short_name)
					? $this->package->short_name
					: $this->package->name,
				'userName'          => $this->user->name,
				'userId'            => $this->user->id,
				'amount'            => $this->package->price,
				'currency'          => $this->package->currency_code,
				'paymentMethodName' => $this->paymentMethod->display_name,
			]))
			->salutation(trans('mail.footer_salutation', ['appName' => config('app.name')]));
	}
}
