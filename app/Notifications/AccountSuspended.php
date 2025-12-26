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

class AccountSuspended extends Notification
{
	use Queueable;

	protected User $user;
	protected string $reason;
	protected string $type; // 'suspended' or 'banned'

	public function __construct(User $user, string $type = 'suspended', string $reason = '')
	{
		$this->user = $user;
		$this->type = $type;
		$this->reason = $reason;
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
		$title = $this->type === 'banned'
			? trans('mail.account_banned_title', ['appName' => config('app.name')])
			: trans('mail.account_suspended_title', ['appName' => config('app.name')]);

		$message = (new MailMessage)
			->subject($title)
			->greeting(trans('mail.hello_user', ['userName' => $this->user->name]));

		if ($this->type === 'banned') {
			$message->line(trans('mail.account_banned_content_1', ['appName' => config('app.name')]));
			$message->line(trans('mail.account_banned_content_2'));
		} else {
			$message->line(trans('mail.account_suspended_content_1', ['appName' => config('app.name')]));
			$message->line(trans('mail.account_suspended_content_2'));
		}

		if (!empty($this->reason)) {
			$message->line(trans('mail.suspension_reason', ['reason' => $this->reason]));
		}

		$message->line(trans('mail.account_suspension_contact'));
		$message->salutation(trans('mail.footer_salutation', ['appName' => config('app.name')]));

		return $message;
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
		if ($this->type === 'banned') {
			return trans('sms.account_banned_content', ['appName' => config('app.name')]);
		}
		return trans('sms.account_suspended_content', ['appName' => config('app.name')]);
	}
}
