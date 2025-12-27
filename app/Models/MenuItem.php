<?php

namespace App\Models;

use App\Http\Controllers\Web\Admin\Panel\Library\Traits\Models\Crud;
use App\Models\Scopes\ActiveScope;
use App\Observers\MenuItemObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

#[ObservedBy([MenuItemObserver::class])]
#[ScopedBy([ActiveScope::class])]
class MenuItem extends BaseModel
{
    use Crud, HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'menu_items';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'menu_id',
        'parent_id',
        'type',
        'title',
        'url',
        'route_name',
        'route_params',
        'target',
        'icon',
        'css_class',
        'visibility_conditions',
        'attributes',
        'linkable_type',
        'linkable_id',
        'lft',
        'rgt',
        'depth',
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
            'route_params' => 'array',
            'visibility_conditions' => 'array',
            'attributes' => 'array',
            'active' => 'boolean',
            'lft' => 'integer',
            'rgt' => 'integer',
            'depth' => 'integer',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    /**
     * Get the menu this item belongs to
     */
    public function menu(): BelongsTo
    {
        return $this->belongsTo(Menu::class);
    }

    /**
     * Get the parent item
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(MenuItem::class, 'parent_id');
    }

    /**
     * Get the children items
     */
    public function children(): HasMany
    {
        return $this->hasMany(MenuItem::class, 'parent_id')->orderBy('lft');
    }

    /**
     * Get the linkable model (Page, Category, etc.)
     */
    public function linkable(): MorphTo
    {
        return $this->morphTo();
    }

    /*
    |--------------------------------------------------------------------------
    | FUNCTIONS
    |--------------------------------------------------------------------------
    */

    /**
     * Get the URL for this menu item
     */
    public function getUrl(): ?string
    {
        // Direct URL
        if (!empty($this->url)) {
            return $this->url;
        }

        // Route-based URL
        if (!empty($this->route_name)) {
            try {
                $params = $this->route_params ?? [];
                return route($this->route_name, $params);
            } catch (\Exception $e) {
                return '#';
            }
        }

        // Linkable model URL
        if ($this->linkable) {
            if (method_exists($this->linkable, 'getUrl')) {
                return $this->linkable->getUrl();
            }
            if (isset($this->linkable->url)) {
                return $this->linkable->url;
            }
        }

        // Type-based defaults
        return match ($this->type) {
            'divider', 'text' => null,
            'dropdown' => '#',
            default => '#',
        };
    }

    /**
     * Check if this item is visible to the current user
     */
    public function isVisibleToCurrentUser(): bool
    {
        if (!$this->active) {
            return false;
        }

        $conditions = $this->visibility_conditions ?? [];

        // Check authentication requirements
        if (!empty($conditions['auth_required']) && !auth()->check()) {
            return false;
        }

        if (!empty($conditions['guest_only']) && auth()->check()) {
            return false;
        }

        // Check role requirements
        if (!empty($conditions['roles']) && auth()->check()) {
            $user = auth()->user();
            if (method_exists($user, 'hasAnyRole')) {
                if (!$user->hasAnyRole($conditions['roles'])) {
                    return false;
                }
            }
        }

        // Check permission requirements
        if (!empty($conditions['permissions']) && auth()->check()) {
            $user = auth()->user();
            if (method_exists($user, 'hasAnyPermission')) {
                if (!$user->hasAnyPermission($conditions['permissions'])) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Check if this item has visible children
     */
    public function hasVisibleChildren(): bool
    {
        return $this->children->contains(fn ($child) => $child->isVisibleToCurrentUser());
    }

    /**
     * Get visible children
     */
    public function getVisibleChildren()
    {
        return $this->children->filter(fn ($child) => $child->isVisibleToCurrentUser());
    }

    /**
     * Check if this is a dropdown item
     */
    public function isDropdown(): bool
    {
        return $this->type === 'dropdown' || $this->children()->exists();
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
     * Get type badge HTML
     */
    public function getTypeHtml(): string
    {
        $colors = [
            'link' => 'primary',
            'button' => 'success',
            'divider' => 'secondary',
            'text' => 'info',
            'page' => 'warning',
            'category' => 'danger',
            'dropdown' => 'dark',
        ];
        $color = $colors[$this->type] ?? 'secondary';
        return '<span class="badge bg-' . $color . '">' . ucfirst($this->type) . '</span>';
    }

    /**
     * Get title with icon HTML
     */
    public function getTitleWithIconHtml(): string
    {
        $icon = $this->icon ? '<i class="' . e($this->icon) . ' me-1"></i>' : '';
        $indent = str_repeat('&nbsp;&nbsp;&nbsp;&nbsp;', $this->depth);
        return $indent . $icon . e($this->title);
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

    public function scopeRoot($query)
    {
        return $query->whereNull('parent_id');
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('lft');
    }
}
