<?php
/*
 * JobClass - Job Board Web Application
 * Copyright (c) BeDigit. All Rights Reserved
 *
 * Website: https://laraclassifier.com/jobclass
 * Author: BeDigit | https://bedigit.com
 *
 * LICENSE
 * -------
 * This software is furnished under a license and may be used and copied
 * only in accordance with the terms of such license and with the inclusion
 * of the above copyright notice. If you Purchased from CodeCanyon,
 * Please read the full License from here - https://codecanyon.net/licenses/standard
 */

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\VonageMessage;
use Illuminate\Notifications\Notification;
use NotificationChannels\Twilio\TwilioChannel;
use NotificationChannels\Twilio\TwilioSmsMessage;

class AccountLocked extends Notification
{
	use Queueable;

	protected User $user;
	protected int $lockoutMinutes;

	public function __construct(User $user, int $lockoutMinutes = 15)
	{
		$this->user = $user;
		$this->lockoutMinutes = $lockoutMinutes;
	}

	public function via($notifiable)
	{
		// Is email can be sent?
		$emailNotificationCanBeSent = (
			config('settings.mail.confirmation') == '1'
			&& !empty($this->user->email)
		);

		// Is SMS can be sent in addition?
		$smsNotificationCanBeSent = (
			config('settings.sms.enable_phone_as_auth_field') == '1'
			&& config('settings.sms.confirmation') == '1'
			&& $this->user->auth_field == 'phone'
			&& !empty($this->user->phone)
			&& !isDemoDomain()
		);

		if ($emailNotificationCanBeSent) {
			return ['mail'];
		}

		if ($smsNotificationCanBeSent) {
			if (config('settings.sms.driver') == 'twilio') {
				return [TwilioChannel::class];
			}

			return ['vonage'];
		}

		return [];
	}

	public function toMail($notifiable)
	{
		return (new MailMessage)
			->subject(trans('mail.account_locked_title', ['appName' => config('app.name')]))
			->greeting(trans('mail.hello_user', ['userName' => $this->user->name]))
			->line(trans('mail.account_locked_content_1', ['appName' => config('app.name')]))
			->line(trans('mail.account_locked_content_2', ['minutes' => $this->lockoutMinutes]))
			->line(trans('mail.account_locked_content_3'))
			->line(trans('mail.account_locked_content_4'))
			->salutation(trans('mail.footer_salutation', ['appName' => config('app.name')]));
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
		return trans('sms.account_locked_content', [
			'appName' => config('app.name'),
			'minutes' => $this->lockoutMinutes
		]);
	}
}
