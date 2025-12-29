<?php

namespace App\Models;

use App\Models\Scopes\ActiveScope;
use Cviebrock\EloquentSluggable\Sluggable;
use Cviebrock\EloquentSluggable\SluggableScopeHelpers;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Http\Controllers\Web\Admin\Panel\Library\Traits\Models\Crud;

#[ScopedBy([ActiveScope::class])]
class WorkerSkill extends BaseModel
{
    use Crud, HasFactory, Sluggable, SluggableScopeHelpers;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'worker_skills';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array<int, string>
     */
    protected $guarded = ['id'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'slug',
        'icon',
        'description',
        'category_id',
        'lft',
        'rgt',
        'depth',
        'active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'active' => 'boolean',
        ];
    }

    /**
     * Return the sluggable configuration array for this model.
     *
     * @return array
     */
    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'name',
            ],
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    /**
     * Get the category that this skill belongs to.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    /**
     * Get the worker profiles that have this skill.
     */
    public function workerProfiles(): BelongsToMany
    {
        return $this->belongsToMany(
            WorkerProfile::class,
            'worker_profile_skills',
            'worker_skill_id',
            'worker_profile_id'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    /**
     * Scope to get only active skills.
     */
    public function scopeActive($query)
    {
        return $query->where('active', true);
    }
}
