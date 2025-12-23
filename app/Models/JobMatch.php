<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobMatch extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'post_id',
        'match_percentage',
        'match_details',
        'status',
        'applied',
        'applied_at',
        'approved_at',
        'rejected_at',
        'resume_id',
        'cover_letter',
        'notification_sent',
        'notification_sent_at',
        'viewed_at',
        'user_action_at',
    ];

    protected $casts = [
        'match_details' => 'array',
        'applied' => 'boolean',
        'notification_sent' => 'boolean',
        'match_percentage' => 'integer',
        'applied_at' => 'datetime',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
        'notification_sent_at' => 'datetime',
        'viewed_at' => 'datetime',
        'user_action_at' => 'datetime',
    ];

    /**
     * Get the user that owns the match
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the post (job) that was matched
     */
    public function post()
    {
        return $this->belongsTo(Post::class);
    }

    /**
     * Get the resume used for application
     */
    public function resume()
    {
        return $this->belongsTo(Resume::class);
    }

    /**
     * Scope for pending matches
     */
    public function scopePendingReview($query)
    {
        return $query->where('status', 'pending_review');
    }

    /**
     * Scope for auto-applied matches
     */
    public function scopeAutoApplied($query)
    {
        return $query->where('status', 'auto_applied');
    }

    /**
     * Scope for high matches
     */
    public function scopeHighMatch($query, $threshold = 70)
    {
        return $query->where('match_percentage', '>=', $threshold);
    }

    /**
     * Mark as viewed
     */
    public function markAsViewed()
    {
        if (!$this->viewed_at) {
            $this->update(['viewed_at' => now()]);
        }
    }

    /**
     * Approve the match (user wants to apply)
     */
    public function approve()
    {
        $this->update([
            'status' => 'approved',
            'user_action_at' => now(),
        ]);
    }

    /**
     * Reject the match (user doesn't want this job)
     */
    public function reject()
    {
        $this->update([
            'status' => 'rejected',
            'user_action_at' => now(),
        ]);
    }

    /**
     * Mark as applied
     */
    public function markAsApplied($resumeId = null, $coverLetter = null, $isAuto = false)
    {
        $this->update([
            'applied' => true,
            'applied_at' => now(),
            'resume_id' => $resumeId ?? $this->resume_id,
            'cover_letter' => $coverLetter ?? $this->cover_letter,
            'status' => $isAuto ? 'auto_applied' : 'manually_applied',
        ]);
    }

    /**
     * Check if match is actionable
     */
    public function isActionable()
    {
        return in_array($this->status, ['pending_review', 'approved']) && !$this->applied;
    }

    /**
     * Get match quality label
     */
    public function getMatchQualityAttribute()
    {
        if ($this->match_percentage >= 80) return 'Excellent';
        if ($this->match_percentage >= 70) return 'Great';
        if ($this->match_percentage >= 60) return 'Good';
        if ($this->match_percentage >= 50) return 'Fair';
        return 'Moderate';
    }

    /**
     * Get match quality color
     */
    public function getMatchColorAttribute()
    {
        if ($this->match_percentage >= 80) return 'success';
        if ($this->match_percentage >= 70) return 'primary';
        if ($this->match_percentage >= 60) return 'info';
        if ($this->match_percentage >= 50) return 'warning';
        return 'secondary';
    }
}
