<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LessonExercise extends Model
{
    use HasFactory;

    protected $fillable = [
        'lesson_id',
        'title',
        'question',
        'code_template',
        'solution',
        'test_cases',
        'difficulty',
        'points'
    ];

    protected $casts = [
        'test_cases' => 'array'
    ];

    public function lesson()
    {
        return $this->belongsTo(CourseLesson::class, 'lesson_id');
    }
}
