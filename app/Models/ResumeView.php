<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ResumeView extends Model
{
    protected $fillable = [
        'employer_id',
        'worker_profile_id',
        'resume_credit_id',
        'contact_unlocked',
        'viewed_at',
        'contact_unlocked_at',
    ];

    protected $casts = [
        'contact_unlocked' => 'boolean',
        'viewed_at' => 'datetime',
        'contact_unlocked_at' => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    public function employer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'employer_id');
    }

    public function workerProfile(): BelongsTo
    {
        return $this->belongsTo(WorkerProfile::class);
    }

    public function resumeCredit(): BelongsTo
    {
        return $this->belongsTo(ResumeCredit::class);
    }

    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    public function scopeForEmployer($query, $employerId)
    {
        return $query->where('employer_id', $employerId);
    }

    public function scopeUnlocked($query)
    {
        return $query->where('contact_unlocked', true);
    }

    /*
    |--------------------------------------------------------------------------
    | STATIC METHODS
    |--------------------------------------------------------------------------
    */

    /**
     * Check if employer has already unlocked this profile
     */
    public static function hasUnlocked(int $employerId, int $workerProfileId): bool
    {
        return static::where('employer_id', $employerId)
            ->where('worker_profile_id', $workerProfileId)
            ->where('contact_unlocked', true)
            ->exists();
    }

    /**
     * Record a profile view (doesn't use credits, just tracks views)
     */
    public static function recordView(int $employerId, int $workerProfileId): self
    {
        return static::updateOrCreate(
            [
                'employer_id' => $employerId,
                'worker_profile_id' => $workerProfileId,
            ],
            [
                'viewed_at' => now(),
            ]
        );
    }

    /**
     * Unlock contact details (uses a credit)
     */
    public static function unlockContact(int $employerId, int $workerProfileId): ?self
    {
        // Check if already unlocked
        if (static::hasUnlocked($employerId, $workerProfileId)) {
            return static::where('employer_id', $employerId)
                ->where('worker_profile_id', $workerProfileId)
                ->first();
        }

        // Use a credit
        $credit = ResumeCredit::useCreditForUser($employerId);
        if (!$credit) {
            return null; // No credits available
        }

        // Record the unlock
        return static::updateOrCreate(
            [
                'employer_id' => $employerId,
                'worker_profile_id' => $workerProfileId,
            ],
            [
                'resume_credit_id' => $credit->id,
                'contact_unlocked' => true,
                'contact_unlocked_at' => now(),
                'viewed_at' => now(),
            ]
        );
    }

    /**
     * Get unlocked profiles for an employer
     */
    public static function getUnlockedProfiles(int $employerId)
    {
        return static::with('workerProfile.user', 'workerProfile.city', 'workerProfile.skills')
            ->forEmployer($employerId)
            ->unlocked()
            ->orderByDesc('contact_unlocked_at')
            ->get();
    }
}
