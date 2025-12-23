<?php

namespace App\Notifications;

use App\Helpers\UrlGen;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;

class DailyJobDigest extends Notification implements ShouldQueue
{
    use Queueable;

    protected $jobs;
    protected $countryCode;

    public function __construct($jobs, $countryCode)
    {
        $this->jobs = $jobs;
        $this->countryCode = $countryCode;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $unsubscribeUrl = url('/account/notifications/unsubscribe/' . base64_encode($notifiable->email));
        $jobCount = $this->jobs->count();

        $countryName = $this->jobs->first()->country->name ?? $this->countryCode;

        $message = (new MailMessage)
            ->subject("Daily Job Digest: {$jobCount} New Jobs in {$countryName}")
            ->greeting('Hello ' . ($notifiable->name ?? 'Job Seeker') . '!')
            ->line("Here are **{$jobCount} new job opportunities** posted in your country in the last 24 hours:")
            ->line('');

        // Add each job
        foreach ($this->jobs as $index => $job) {
            $jobNum = $index + 1;
            $postUrl = UrlGen::post($job);
            $companyName = $job->company_name ?? 'Company';
            $location = '';

            if ($job->city) {
                $location = $job->city->name ?? '';
            }
            if (empty($location) && $job->country) {
                $location = $job->country->name ?? '';
            }

            $message->line("**{$jobNum}. {$job->title}**");
            $message->line("Company: {$companyName}" . ($location ? " | Location: {$location}" : ""));

            // Add salary if available
            if ($job->salary_min || $job->salary_max) {
                $currency = $job->currency_code ?? '';
                $min = $job->salary_min ? number_format($job->salary_min) : '';
                $max = $job->salary_max ? number_format($job->salary_max) : '';

                if ($min && $max) {
                    $message->line("Salary: {$currency} {$min} - {$max}");
                } elseif ($min) {
                    $message->line("Salary: From {$currency} {$min}");
                } elseif ($max) {
                    $message->line("Salary: Up to {$currency} {$max}");
                }
            }

            $message->line("[View Job Details]({$postUrl})");
            $message->line('');
        }

        $message->line('---')
            ->line('**Want more relevant job matches?**')
            ->line('Set up your [job preferences](' . url('/account/preferences') . ') to get personalized job recommendations!')
            ->line('')
            ->line('---')
            ->line('*Receiving too many emails?*')
            ->line('[Unsubscribe here](' . $unsubscribeUrl . ') or [manage your notification settings](' . url('/account/settings') . ').')
            ->salutation('Best regards, ' . config('app.name') . ' Team');

        return $message;
    }
}
