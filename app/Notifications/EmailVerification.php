<?php
namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;

class EmailVerification extends Notification implements ShouldQueue
{
	use Queueable;
	
	protected $entity;
	protected $entityRef;
	
	public function __construct($entity, $entityRef)
	{
		if (is_numeric($entity) || is_string($entity)) {
			if (isset($entityRef['namespace'], $entityRef['scopes'])) {
				$object = $entityRef['namespace']::withoutGlobalScopes($entityRef['scopes'])->find($entity);
				if (!empty($object)) {
					$entity = $object;
				}
			}
		}
		
		$this->entity = $entity;
		$this->entityRef = $entityRef;
	}
	
	public function via($notifiable)
	{
		if (!isset($this->entityRef['name'])) {
			return [];
		}
		
		$notificationCanBeSent = (empty($this->entity->email_verified_at) && !empty($this->entity->email_token));
		if (!$notificationCanBeSent) {
			return [];
		}
		
		return ['mail'];
	}
	
	public function toMail($notifiable)
	{
		$path = $this->entityRef['slug'] . '/verify/email/' . $this->entity->email_token;
		$verificationUrl = (config('plugins.domainmapping.installed'))
			? dmUrl($this->entity->country_code, $path)
			: url($path);
		
		return (new MailMessage)
			->subject(trans('mail.email_verification_title'))
			->greeting(trans('mail.email_verification_content_1', ['userName' => $this->entity->{$this->entityRef['name']},]))
			->line(trans('mail.email_verification_content_2'))
			->action(trans('mail.email_verification_action'), $verificationUrl)
			->line(trans('mail.email_verification_content_3', ['appName' => config('app.name')]))
			->salutation(trans('mail.footer_salutation', ['appName' => config('app.name')]));
	}
}
