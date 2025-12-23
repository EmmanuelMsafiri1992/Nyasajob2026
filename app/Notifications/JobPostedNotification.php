<?php

namespace App\Notifications;

use App\Helpers\Date;
use App\Helpers\UrlGen;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Carbon;

class JobPostedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $post;

    public function __construct($post)
    {
        $this->post = $post;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $postUrl = UrlGen::post($this->post);
        $unsubscribeUrl = url('/account/notifications/unsubscribe/' . base64_encode($notifiable->email));

        $companyName = $this->post->company_name ?? 'A company';
        $jobTitle = $this->post->title ?? 'New Job Opportunity';
        $location = '';

        if ($this->post->city) {
            $location = $this->post->city->name ?? '';
        }
        if (empty($location) && $this->post->country) {
            $location = $this->post->country->name ?? '';
        }

        $salaryInfo = '';
        if ($this->post->salary_min || $this->post->salary_max) {
            $currency = $this->post->currency_code ?? '';
            $min = $this->post->salary_min ? number_format($this->post->salary_min) : '';
            $max = $this->post->salary_max ? number_format($this->post->salary_max) : '';

            if ($min && $max) {
                $salaryInfo = "{$currency} {$min} - {$max}";
            } elseif ($min) {
                $salaryInfo = "From {$currency} {$min}";
            } elseif ($max) {
                $salaryInfo = "Up to {$currency} {$max}";
            }
        }

        $message = (new MailMessage)
            ->subject('🔔 New Job Posted in Your Country: ' . $jobTitle)
            ->greeting('👋 Hello ' . ($notifiable->name ?? 'Job Seeker') . '!')
            ->line('A new job opportunity has been posted in your country that might interest you:')
            ->line('')
            ->line('**📋 Position:** ' . $jobTitle)
            ->line('**🏢 Company:** ' . $companyName);

        if ($location) {
            $message->line('**📍 Location:** ' . $location);
        }

        if ($salaryInfo) {
            $message->line('**💰 Salary:** ' . $salaryInfo);
        }

        if ($this->post->category) {
            $message->line('**📁 Category:** ' . $this->post->category->name);
        }

        $message->line('**⏰ Posted:** ' . Date::format(Carbon::now(Date::getAppTimeZone())))
            ->line('')
            ->action('👉 View Job Details', $postUrl)
            ->line('Apply now before this opportunity closes!')
            ->line('')
            ->line('---')
            ->line('💡 **Tip:** Update your profile to get better job matches!')
            ->line('')
            ->line('---')
            ->line('*Don\'t want to receive these notifications?*')
            ->line('You can [unsubscribe here](' . $unsubscribeUrl . ') or manage your notification preferences in your account settings.')
            ->salutation('Best regards, ' . config('app.name') . ' Team');

        return $message;
    }
}
