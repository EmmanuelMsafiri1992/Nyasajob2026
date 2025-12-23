<?php

namespace App\Notifications;

use App\Helpers\UrlGen;
use App\Models\JobMatch;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AutoAppliedNotification extends Notification implements ShouldQueue
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
        $matchesUrl = url('/account/job-matches?filter=auto_applied');

        // Get user's urgency level
        $urgencyConfig = $notifiable->jobPreference->getUrgencyConfig();

        $message = (new MailMessage)
            ->subject('✅ Auto-Applied: ' . $post->title . ' (' . $matchPercentage . '% Match)')
            ->greeting('👋 Hello ' . ($notifiable->name ?? 'Job Seeker') . '!')
            ->line('**Great news! We automatically applied to a job on your behalf:**')
            ->line('---')
            ->line('**📋 Position:** ' . $post->title)
            ->line('**🏢 Company:** ' . ($post->company_name ?? 'Not specified'))
            ->line('**📍 Location:** ' . ($post->city->name ?? 'Not specified'))
            ->line('**💰 Salary:** ' . $this->formatSalary($post))
            ->line('**🎯 Match Score:** ' . $matchPercentage . '% (' . $matchQuality . ' Match)')
            ->line('---');

        // Application details
        $message->line('**Application Details:**')
            ->line('✓ **Resume Submitted:** ' . ($this->match->resume->name ?? 'Your default resume'))
            ->line('✓ **Cover Letter:** Sent with personalized template')
            ->line('✓ **Applied On:** ' . $this->match->applied_at->format('M d, Y \a\t H:i'))
            ->line('✓ **Application Method:** Auto-Applied (Urgency: ' . $urgencyConfig['label'] . ')');

        // Why it was auto-applied
        $message->line('---')
            ->line('**Why did we apply?**')
            ->line('This job scored ' . $matchPercentage . '% which exceeds your auto-apply threshold of ' .
                $urgencyConfig['auto_apply_threshold'] . '% for your current urgency level.');

        // Match details
        $matchDetails = $this->match->match_details;
        if (isset($matchDetails['skills']['matched_skills']) && !empty($matchDetails['skills']['matched_skills'])) {
            $matchedSkills = $matchDetails['skills']['matched_skills'];
            $message->line('**Matched Your Skills:** ' . implode(', ', array_slice($matchedSkills, 0, 5)));
        }

        $message->action('👉 View Full Job Details', $postUrl)
            ->line('**What happens next?**')
            ->line('• The employer has received your application')
            ->line('• They will review your resume and cover letter')
            ->line('• You\'ll be notified if they respond')
            ->line('• Track this application in your dashboard')
            ->line('---')
            ->line('**Track your applications:** [View all auto-applied jobs](' . $matchesUrl . ')')
            ->line('**Want to adjust settings?** Visit your [Job Preferences](/account/job-preferences) to change urgency level or daily limits.');

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
            'auto_applied' => true,
        ];
    }
}
