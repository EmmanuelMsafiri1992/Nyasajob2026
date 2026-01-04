<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class QuizQuestion extends Model
{
    protected $fillable = [
        'question',
        'options',
        'category',
        'order',
        'active',
    ];

    protected $casts = [
        'options' => 'array',
        'active' => 'boolean',
    ];

    public const CATEGORIES = [
        'personality' => 'Personality Type',
        'skills' => 'Skills & Abilities',
        'interests' => 'Interests',
        'work-style' => 'Work Style',
    ];

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('active', true);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('order')->orderBy('id');
    }
}
