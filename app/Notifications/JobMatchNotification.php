<?php

namespace App\Notifications;

use App\Helpers\UrlGen;
use App\Models\JobMatch;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class JobMatchNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $match;

    /**
     * Create a new notification instance.
     *
     * @param JobMatch $match
     * @return void
     */
    public function __construct(JobMatch $match)
    {
        $this->match = $match;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $post = $this->match->post;
        $matchPercentage = $this->match->match_percentage;
        $matchQuality = $this->match->match_quality;

        $postUrl = UrlGen::post($post);
        $matchesUrl = url('/account/job-matches');

        // Get matched skills if available
        $matchDetails = $this->match->match_details;
        $matchedSkills = $matchDetails['skills']['matched_skills'] ?? [];

        $message = (new MailMessage)
            ->subject('🎯 ' . $matchQuality . ' Job Match: ' . $post->title)
            ->greeting('👋 Hello ' . ($notifiable->name ?? 'Job Seeker') . '!')
            ->line('Great news! We found a job that matches your profile:')
            ->line('**📋 Position:** ' . $post->title)
            ->line('**🏢 Company:** ' . ($post->company_name ?? 'Not specified'))
            ->line('**📍 Location:** ' . ($post->city->name ?? 'Not specified'))
            ->line('**💰 Salary:** ' . $this->formatSalary($post))
            ->line('**🎯 Match Score:** ' . $matchPercentage . '% (' . $matchQuality . ' Match)');

        // Add matched skills if any
        if (!empty($matchedSkills)) {
            $message->line('**✅ Matched Skills:** ' . implode(', ', array_slice($matchedSkills, 0, 5)));
        }

        // Add match breakdown
        if (isset($matchDetails['category']['matched']) && $matchDetails['category']['matched']) {
            $message->line('✓ Category matches your preferences');
        }

        if (isset($matchDetails['salary']['matched']) && $matchDetails['salary']['matched']) {
            $message->line('✓ Salary meets your expectations');
        }

        $message->line('---')
            ->line('**Why this match?**');

        // Add reasons
        if (isset($matchDetails['skills']['reason'])) {
            $message->line('• ' . $matchDetails['skills']['reason']);
        }
        if (isset($matchDetails['category']['reason'])) {
            $message->line('• ' . $matchDetails['category']['reason']);
        }

        $message->action('👉 Review Match & Apply', $matchesUrl)
            ->line('This job opportunity is waiting for your review. Visit your Job Matches dashboard to see full details and apply.')
            ->line('**Quick Actions:**')
            ->line('• Review the full job description')
            ->line('• Apply with your resume')
            ->line('• Save for later or pass');

        return $message;
    }

    /**
     * Format salary for display
     */
    private function formatSalary($post)
    {
        if (empty($post->salary_min) && empty($post->salary_max)) {
            return 'Negotiable';
        }

        if (!empty($post->salary_min) && !empty($post->salary_max)) {
            return number_format($post->salary_min) . ' - ' . number_format($post->salary_max);
        }

        return number_format($post->salary_min ?? $post->salary_max);
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'match_id' => $this->match->id,
            'post_id' => $this->match->post_id,
            'match_percentage' => $this->match->match_percentage,
        ];
    }
}
