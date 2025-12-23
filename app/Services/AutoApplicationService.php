<?php

namespace App\Services;

use App\Models\JobMatch;
use App\Models\User;
use App\Models\UserJobPreference;
use App\Notifications\JobMatchNotification;
use App\Notifications\AutoAppliedNotification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class AutoApplicationService
{
    /**
     * Process a job match and determine if it should be auto-applied
     *
     * @param JobMatch $match
     * @return bool Whether the job was auto-applied
     */
    public function processMatch(JobMatch $match)
    {
        $user = $match->user;
        $preference = $user->jobPreference;

        if (!$preference) {
            return false;
        }

        // Check if auto-apply is enabled
        if (!$preference->auto_apply_enabled) {
            $this->notifyUserOfMatch($match);
            return false;
        }

        // Check daily application limit
        if (!$preference->canApplyToday()) {
            Log::info('Daily application limit reached', [
                'user_id' => $user->id,
                'match_id' => $match->id
            ]);
            $this->notifyUserOfMatch($match);
            return false;
        }

        // Get urgency configuration
        $urgencyConfig = $preference->getUrgencyConfig();

        // Determine if match meets auto-apply threshold
        if ($match->match_percentage >= $urgencyConfig['auto_apply_threshold']) {
            return $this->autoApplyToJob($match);
        } else {
            // Notify user for review
            $this->notifyUserOfMatch($match);
            return false;
        }
    }

    /**
     * Automatically apply to a job on behalf of user
     *
     * @param JobMatch $match
     * @return bool Success status
     */
    public function autoApplyToJob(JobMatch $match)
    {
        try {
            $user = $match->user;
            $preference = $user->jobPreference;
            $post = $match->post;

            // Get user's default resume or most recent one
            $resume = $preference->defaultResume ?? $user->resumes()->where('active', 1)->latest()->first();

            if (!$resume) {
                Log::warning('No resume available for auto-apply', [
                    'user_id' => $user->id,
                    'match_id' => $match->id
                ]);
                $this->notifyUserOfMatch($match);
                return false;
            }

            // Prepare cover letter
            $coverLetter = $this->generateCoverLetter($user, $post, $preference);

            // Send application via the messaging system
            $applicationSent = $this->sendApplication($user, $post, $resume, $coverLetter);

            if ($applicationSent) {
                // Mark as auto-applied
                $match->markAsApplied($resume->id, $coverLetter, true);

                // Update preference tracking
                $preference->increment('total_auto_applications');
                $preference->update(['last_application_at' => now()]);

                // Notify user about the auto-application
                $this->notifyUserOfAutoApply($match);

                Log::info('Successfully auto-applied to job', [
                    'user_id' => $user->id,
                    'post_id' => $post->id,
                    'match_percentage' => $match->match_percentage
                ]);

                return true;
            }

            return false;
        } catch (\Exception $e) {
            Log::error('Failed to auto-apply to job', [
                'match_id' => $match->id,
                'error' => $e->getMessage()
            ]);

            // On failure, notify user for manual application
            $this->notifyUserOfMatch($match);
            return false;
        }
    }

    /**
     * Send job application via messaging system
     *
     * @param User $user
     * @param Post $post
     * @param Resume $resume
     * @param string $coverLetter
     * @return bool Success status
     */
    private function sendApplication(User $user, $post, $resume, $coverLetter)
    {
        try {
            // Create a thread/message to the job poster
            // This integrates with the existing messaging system

            $thread = \App\Models\Thread::create([
                'subject' => 'Application for: ' . $post->title,
                'post_id' => $post->id,
            ]);

            // Add participants (user and post owner)
            $thread->participants()->create([
                'user_id' => $user->id,
                'thread_id' => $thread->id,
            ]);

            $thread->participants()->create([
                'user_id' => $post->user_id,
                'thread_id' => $thread->id,
            ]);

            // Create the application message
            $message = $thread->messages()->create([
                'user_id' => $user->id,
                'body' => $coverLetter,
                'filename' => $resume->filename, // Attach resume
            ]);

            // Notify the job poster
            $postOwner = $post->user;
            if ($postOwner) {
                $postOwner->notify(new \App\Notifications\UserNotification($message));
            }

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to send application message', [
                'user_id' => $user->id,
                'post_id' => $post->id,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Generate cover letter for application
     *
     * @param User $user
     * @param Post $post
     * @param UserJobPreference $preference
     * @return string
     */
    private function generateCoverLetter(User $user, $post, UserJobPreference $preference)
    {
        // Use user's template if available
        if (!empty($preference->cover_letter_template)) {
            $template = $preference->cover_letter_template;

            // Replace placeholders
            $replacements = [
                '{name}' => $user->name,
                '{job_title}' => $post->title,
                '{company}' => $post->company_name ?? 'your company',
                '{email}' => $user->email,
            ];

            return str_replace(array_keys($replacements), array_values($replacements), $template);
        }

        // Default cover letter
        return sprintf(
            "Dear Hiring Manager,\n\n" .
            "I am writing to express my interest in the %s position posted on Nyasajob.\n\n" .
            "I believe my skills and experience make me a strong candidate for this role. " .
            "I have attached my resume for your review and would welcome the opportunity to discuss " .
            "how I can contribute to your team.\n\n" .
            "Thank you for considering my application. I look forward to hearing from you.\n\n" .
            "Best regards,\n%s\n%s",
            $post->title,
            $user->name,
            $user->email
        );
    }

    /**
     * Notify user about a new job match (for review)
     *
     * @param JobMatch $match
     */
    private function notifyUserOfMatch(JobMatch $match)
    {
        try {
            if (!$match->notification_sent) {
                $match->user->notify(new JobMatchNotification($match));
                $match->update([
                    'notification_sent' => true,
                    'notification_sent_at' => now()
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Failed to send match notification', [
                'match_id' => $match->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Notify user that application was sent automatically
     *
     * @param JobMatch $match
     */
    private function notifyUserOfAutoApply(JobMatch $match)
    {
        try {
            $match->user->notify(new AutoAppliedNotification($match));
            $match->update([
                'notification_sent' => true,
                'notification_sent_at' => now()
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send auto-apply notification', [
                'match_id' => $match->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Process all pending matches for a user (called manually or via cron)
     *
     * @param User $user
     * @return array Statistics
     */
    public function processPendingMatches(User $user)
    {
        $stats = [
            'reviewed' => 0,
            'auto_applied' => 0,
            'notified' => 0,
        ];

        $pendingMatches = JobMatch::where('user_id', $user->id)
            ->where('status', 'pending_review')
            ->where('applied', false)
            ->orderBy('match_percentage', 'desc')
            ->get();

        foreach ($pendingMatches as $match) {
            $stats['reviewed']++;

            if ($this->processMatch($match)) {
                $stats['auto_applied']++;
            } else {
                $stats['notified']++;
            }
        }

        return $stats;
    }

    /**
     * Manually apply to a job (user clicked apply)
     *
     * @param JobMatch $match
     * @param int|null $resumeId
     * @param string|null $customCoverLetter
     * @return bool Success status
     */
    public function manualApply(JobMatch $match, $resumeId = null, $customCoverLetter = null)
    {
        $user = $match->user;
        $preference = $user->jobPreference;
        $post = $match->post;

        // Get resume
        $resume = $resumeId
            ? Resume::findOrFail($resumeId)
            : ($preference->defaultResume ?? $user->resumes()->where('active', 1)->latest()->first());

        if (!$resume) {
            return false;
        }

        // Use custom cover letter or generate one
        $coverLetter = $customCoverLetter ?? $this->generateCoverLetter($user, $post, $preference);

        // Send application
        if ($this->sendApplication($user, $post, $resume, $coverLetter)) {
            $match->markAsApplied($resume->id, $coverLetter, false);

            // Update tracking
            if ($preference) {
                $preference->increment('total_auto_applications');
                $preference->update(['last_application_at' => now()]);
            }

            return true;
        }

        return false;
    }
}
