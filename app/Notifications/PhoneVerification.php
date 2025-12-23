<?php
namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\VonageMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use NotificationChannels\Twilio\TwilioChannel;
use NotificationChannels\Twilio\TwilioSmsMessage;

class PhoneVerification extends Notification implements ShouldQueue
{
	use Queueable;
	
	protected $entity;
	protected $entityRef;
	
	public function __construct($entity, $entityRef)
	{
		$this->entity = $entity;
		$this->entityRef = $entityRef;
	}
	
	public function via($notifiable)
	{
		if (!isset($this->entityRef['name'])) {
			return [];
		}
		
		$notificationCanBeSent = (empty($this->entity->phone_verified_at) && !empty($this->entity->phone_token));
		if (!$notificationCanBeSent) {
			return [];
		}
		
		if (config('settings.sms.driver') == 'twilio') {
			return [TwilioChannel::class];
		}
		
		return ['vonage'];
	}
	
	public function toVonage($notifiable)
	{
		return (new VonageMessage())->content($this->smsMessage())->unicode();
	}
	
	public function toTwilio($notifiable)
	{
		return (new TwilioSmsMessage())->content($this->smsMessage());
	}
	
	protected function smsMessage()
	{
		$token = $this->entity->phone_token;
		
		$path = $this->entityRef['slug'] . '/verify/phone/' . $token;
		$tokenUrl = (config('plugins.domainmapping.installed'))
			? dmUrl($this->entity->country_code, $path)
			: url($path);
		
		return trans('sms.phone_verification_content', [
			'appName'  => config('app.name'),
			'token'    => $token,
			'tokenUrl' => $tokenUrl,
		]);
	}
}
