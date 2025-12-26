<?php

namespace App\Models;

use App\Http\Controllers\Web\Admin\Panel\Library\Traits\Models\Crud;
use App\Models\Scopes\ActiveScope;
use App\Observers\HomepagePresetObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;

#[ObservedBy([HomepagePresetObserver::class])]
#[ScopedBy([ActiveScope::class])]
class HomepagePreset extends BaseModel
{
    use Crud, HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'homepage_presets';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'slug',
        'description',
        'thumbnail',
        'sections_config',
        'navbar_config',
        'is_default',
        'active',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'sections_config' => 'array',
            'navbar_config' => 'array',
            'is_default' => 'boolean',
            'active' => 'boolean',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | FUNCTIONS
    |--------------------------------------------------------------------------
    */

    /**
     * Get the default preset
     */
    public static function getDefault(): ?self
    {
        return self::where('is_default', true)->where('active', true)->first();
    }

    /**
     * Apply this preset to the homepage sections
     */
    public function applyPreset(): bool
    {
        if (empty($this->sections_config)) {
            return false;
        }

        $sectionsConfig = $this->sections_config['sections'] ?? [];
        
        foreach ($sectionsConfig as $config) {
            $section = HomeSection::where('method', $config['method'])->first();
            if ($section) {
                $section->update([
                    'active' => $config['active'] ?? true,
                    'lft' => $config['lft'] ?? $section->lft,
                    'rgt' => $config['rgt'] ?? $section->rgt,
                ]);
            }
        }

        // Clear cache after applying preset
        cache()->flush();

        return true;
    }

    /**
     * Set this preset as default (unset others)
     */
    public function setAsDefault(): void
    {
        // Unset other defaults
        self::where('id', '!=', $this->id)->update(['is_default' => false]);
        
        // Set this as default
        $this->update(['is_default' => true]);
    }

    /**
     * Get active status as HTML
     */
    public function getActiveHtml(): string
    {
        if ($this->active) {
            return '<span class="badge bg-success">' . trans('admin.active') . '</span>';
        }
        return '<span class="badge bg-secondary">' . trans('admin.inactive') . '</span>';
    }

    /**
     * Get default status as HTML
     */
    public function getDefaultHtml(): string
    {
        if ($this->is_default) {
            return '<span class="badge bg-primary">' . trans('admin.default') . '</span>';
        }
        return '<span class="badge bg-light text-dark">-</span>';
    }

    /**
     * Apply preset button for admin panel
     */
    public function applyPresetButton(): string
    {
        $url = admin_url('homepage-presets/' . $this->id . '/apply');
        return '<a class="btn btn-xs btn-warning" href="' . $url . '" 
                   onclick="return confirm(\'' . trans('admin.confirm_apply_preset') . '\')">
                   <i class="fa-solid fa-magic"></i> ' . trans('admin.apply') . '
               </a>';
    }

    /*
    |--------------------------------------------------------------------------
    | ACCESSORS & MUTATORS
    |--------------------------------------------------------------------------
    */

    protected function thumbnailUrl(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (!empty($this->thumbnail)) {
                    return url('storage/' . $this->thumbnail);
                }
                return url('images/default-preset.png');
            }
        );
    }

    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    public function scopeActive($query)
    {
        return $query->where('active', true);
    }
}
