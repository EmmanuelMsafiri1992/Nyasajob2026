<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseModule extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'title',
        'description',
        'order'
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function lessons()
    {
        return $this->hasMany(CourseLesson::class, 'module_id')->orderBy('order');
    }

    /**
     * Admin panel: Lessons count badge
     */
    public function getLessonsCountBadge(): string
    {
        $count = $this->lessons()->count();
        return '<span class="badge bg-info">' . $count . ' lessons</span>';
    }

    /**
     * Admin panel: Lessons button
     */
    public function lessonsBtn($xPanel = false): string
    {
        $url = admin_url('modules/' . $this->id . '/lessons');
        $count = $this->lessons()->count();

        return '<a class="btn btn-xs btn-info" href="' . $url . '">
            <i class="fa fa-book"></i> Lessons (' . $count . ')
        </a>';
    }

    /**
     * Admin panel: Bulk deletion button
     */
    public function bulkDeletionBtn($xPanel = false): string
    {
        $url = admin_url('courses/' . $this->course_id . '/modules/bulk_actions');
        return '<a href="' . $url . '" class="btn btn-danger shadow bulk-action" data-action="deletion">
            <i class="fa fa-trash"></i> Delete
        </a>';
    }
}
