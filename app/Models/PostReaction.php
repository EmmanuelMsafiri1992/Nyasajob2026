<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PostReaction extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'post_reactions';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'post_id',
        'user_id',
        'ip_address',
        'reaction_type',
    ];

    /**
     * Available reaction types with their emoji representations
     */
    public const REACTION_TYPES = [
        'like' => ['emoji' => '👍', 'label' => 'Like'],
        'love' => ['emoji' => '❤️', 'label' => 'Love'],
        'celebrate' => ['emoji' => '🎉', 'label' => 'Celebrate'],
        'insightful' => ['emoji' => '💡', 'label' => 'Insightful'],
        'curious' => ['emoji' => '🤔', 'label' => 'Curious'],
    ];

    /**
     * Get the post that this reaction belongs to.
     */
    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class, 'post_id');
    }

    /**
     * Get the user that made this reaction.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get emoji for the reaction type
     */
    public function getEmojiAttribute(): string
    {
        return self::REACTION_TYPES[$this->reaction_type]['emoji'] ?? '👍';
    }

    /**
     * Get label for the reaction type
     */
    public function getLabelAttribute(): string
    {
        return self::REACTION_TYPES[$this->reaction_type]['label'] ?? 'Like';
    }

    /**
     * Scope to filter by reaction type
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('reaction_type', $type);
    }

    /**
     * Scope to filter by post
     */
    public function scopeForPost($query, int $postId)
    {
        return $query->where('post_id', $postId);
    }
}
