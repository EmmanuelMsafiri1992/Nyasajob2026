<?php

namespace App\Services;

use App\Models\Post;
use App\Models\User;
use App\Models\UserJobPreference;
use App\Models\JobMatch;
use Illuminate\Support\Facades\Log;

class JobMatchingService
{
    /**
     * Find and create matches for a newly posted job
     *
     * @param Post $post
     * @return int Number of matches created
     */
    public function findMatchesForJob(Post $post)
    {
        // Get users in same country with job preferences set and notifications enabled
        $users = User::where('country_code', $post->country_code)
            ->where('job_notification_enabled', true)
            ->where('is_admin', 0)
            ->whereNotNull('email')
            ->whereNotNull('email_verified_at')
            ->whereHas('jobPreference') // Only users who have set up preferences
            ->with('jobPreference')
            ->get();

        $matchesCreated = 0;

        foreach ($users as $user) {
            try {
                $matchData = $this->calculateMatch($user, $post);

                // Only create match if it meets minimum threshold
                if ($matchData['percentage'] >= $user->jobPreference->min_match_percentage) {
                    $this->createJobMatch($user, $post, $matchData);
                    $matchesCreated++;
                }
            } catch (\Exception $e) {
                Log::error('Failed to create job match', [
                    'user_id' => $user->id,
                    'post_id' => $post->id,
                    'error' => $e->getMessage()
                ]);
            }
        }

        return $matchesCreated;
    }

    /**
     * Calculate match percentage and details between user and job
     *
     * @param User $user
     * @param Post $post
     * @return array ['percentage' => int, 'details' => array]
     */
    public function calculateMatch(User $user, Post $post)
    {
        $preference = $user->jobPreference;
        $score = 0;
        $maxScore = 0;
        $details = [];

        // 1. Category Match (30 points)
        $maxScore += 30;
        if (!empty($preference->preferred_categories) && in_array($post->category_id, $preference->preferred_categories)) {
            $score += 30;
            $details['category'] = [
                'matched' => true,
                'score' => 30,
                'reason' => 'Job category matches your preferences'
            ];
        } else {
            $details['category'] = [
                'matched' => false,
                'score' => 0,
                'reason' => 'Job category not in your preferred list'
            ];
        }

        // 2. Skills Match (40 points)
        $maxScore += 40;
        $skillsMatch = $this->calculateSkillsMatch($preference, $post);
        $score += $skillsMatch['score'];
        $details['skills'] = $skillsMatch;

        // 3. Salary Match (20 points)
        $maxScore += 20;
        $salaryMatch = $this->calculateSalaryMatch($preference, $post);
        $score += $salaryMatch['score'];
        $details['salary'] = $salaryMatch;

        // 4. Location/Remote Match (10 points)
        $maxScore += 10;
        if ($preference->remote_work) {
            // Check if job mentions "remote" in description or title
            $isRemote = stripos($post->title, 'remote') !== false ||
                        stripos($post->description, 'remote') !== false;

            if ($isRemote) {
                $score += 10;
                $details['location'] = [
                    'matched' => true,
                    'score' => 10,
                    'reason' => 'Remote work available'
                ];
            } else {
                $details['location'] = [
                    'matched' => false,
                    'score' => 0,
                    'reason' => 'Remote work not mentioned'
                ];
            }
        } else {
            $score += 5; // Neutral score
            $details['location'] = [
                'matched' => 'neutral',
                'score' => 5,
                'reason' => 'Location not a factor'
            ];
        }

        // Calculate final percentage
        $percentage = $maxScore > 0 ? round(($score / $maxScore) * 100) : 0;

        return [
            'percentage' => $percentage,
            'details' => $details,
            'raw_score' => $score,
            'max_score' => $maxScore
        ];
    }

    /**
     * Calculate skills match
     */
    private function calculateSkillsMatch(UserJobPreference $preference, Post $post)
    {
        $userSkills = $preference->getSkillsArray();
        $maxScore = 40;

        if (empty($userSkills)) {
            return [
                'matched' => false,
                'score' => 0,
                'reason' => 'No skills set in preferences',
                'matched_skills' => []
            ];
        }

        // Combine title, description, and tags to search for skills
        $jobText = strtolower($post->title . ' ' . $post->description . ' ' . $post->tags);

        $matchedSkills = [];
        foreach ($userSkills as $skill) {
            if (stripos($jobText, strtolower($skill)) !== false) {
                $matchedSkills[] = $skill;
            }
        }

        // Calculate score based on percentage of skills matched
        $matchPercentage = count($userSkills) > 0 ? (count($matchedSkills) / count($userSkills)) : 0;
        $score = round($maxScore * $matchPercentage);

        return [
            'matched' => count($matchedSkills) > 0,
            'score' => $score,
            'reason' => count($matchedSkills) > 0
                ? sprintf('%d of %d skills matched', count($matchedSkills), count($userSkills))
                : 'No matching skills found',
            'matched_skills' => $matchedSkills,
            'total_skills' => count($userSkills)
        ];
    }

    /**
     * Calculate salary match
     */
    private function calculateSalaryMatch(UserJobPreference $preference, Post $post)
    {
        $maxScore = 20;

        // If user hasn't set salary preferences, give neutral score
        if (empty($preference->min_salary) && empty($preference->max_salary)) {
            return [
                'matched' => 'neutral',
                'score' => 10,
                'reason' => 'Salary not a factor'
            ];
        }

        // If job doesn't have salary info
        if (empty($post->salary_min) && empty($post->salary_max)) {
            return [
                'matched' => 'unknown',
                'score' => 5,
                'reason' => 'Job salary not specified'
            ];
        }

        // Check if salaries overlap
        $userMin = $preference->min_salary ?? 0;
        $userMax = $preference->max_salary ?? PHP_INT_MAX;
        $jobMin = $post->salary_min ?? 0;
        $jobMax = $post->salary_max ?? $jobMin;

        // Check for overlap
        if ($jobMax >= $userMin && $jobMin <= $userMax) {
            // Calculate how good the match is
            if ($jobMin >= $userMin && $jobMax <= $userMax) {
                // Perfect fit
                return [
                    'matched' => true,
                    'score' => $maxScore,
                    'reason' => 'Salary perfectly matches your expectations'
                ];
            } else if ($jobMin >= $userMin) {
                // Job pays at or above minimum
                return [
                    'matched' => true,
                    'score' => round($maxScore * 0.8),
                    'reason' => 'Salary meets or exceeds your minimum'
                ];
            } else {
                // Partial overlap
                return [
                    'matched' => 'partial',
                    'score' => round($maxScore * 0.5),
                    'reason' => 'Salary partially matches expectations'
                ];
            }
        }

        return [
            'matched' => false,
            'score' => 0,
            'reason' => 'Salary below your expectations'
        ];
    }

    /**
     * Create a job match record
     */
    private function createJobMatch(User $user, Post $post, array $matchData)
    {
        // Check if match already exists
        $existingMatch = JobMatch::where('user_id', $user->id)
            ->where('post_id', $post->id)
            ->first();

        if ($existingMatch) {
            return $existingMatch;
        }

        return JobMatch::create([
            'user_id' => $user->id,
            'post_id' => $post->id,
            'match_percentage' => $matchData['percentage'],
            'match_details' => $matchData['details'],
            'status' => 'pending_review',
        ]);
    }

    /**
     * Get pending matches for a user
     */
    public function getPendingMatches(User $user, $limit = 10)
    {
        return JobMatch::where('user_id', $user->id)
            ->where('status', 'pending_review')
            ->with(['post', 'post.category', 'post.city'])
            ->orderBy('match_percentage', 'desc')
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get all matches for a user (with filters)
     */
    public function getUserMatches(User $user, array $filters = [])
    {
        $query = JobMatch::where('user_id', $user->id)
            ->with(['post', 'post.category', 'post.city']);

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['applied'])) {
            $query->where('applied', $filters['applied']);
        }

        if (isset($filters['min_match'])) {
            $query->where('match_percentage', '>=', $filters['min_match']);
        }

        return $query->orderBy('created_at', 'desc')->paginate(20);
    }
}
