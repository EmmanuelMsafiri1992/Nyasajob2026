<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserQuizResponse extends Model
{
    protected $fillable = [
        'user_id',
        'session_id',
        'answers',
        'result_key',
        'scores',
    ];

    protected $casts = [
        'answers' => 'array',
        'scores' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function quizResult(): ?QuizResult
    {
        return QuizResult::where('result_key', $this->result_key)->first();
    }
}
