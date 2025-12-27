<?php

namespace App\Models;

use App\Http\Controllers\Web\Admin\Panel\Library\Traits\Models\Crud;
use App\Models\Scopes\ActiveScope;
use App\Observers\MenuObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

#[ObservedBy([MenuObserver::class])]
#[ScopedBy([ActiveScope::class])]
class Menu extends BaseModel
{
    use Crud, HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'menus';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'slug',
        'location',
        'description',
        'settings',
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
            'settings' => 'array',
            'active' => 'boolean',
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
     * Get all items for this menu
     */
    public function items(): HasMany
    {
        return $this->hasMany(MenuItem::class)->orderBy('lft');
    }

    /**
     * Get only root items (no parent)
     */
    public function rootItems(): HasMany
    {
        return $this->hasMany(MenuItem::class)
            ->whereNull('parent_id')
            ->orderBy('lft');
    }

    /*
    |--------------------------------------------------------------------------
    | FUNCTIONS
    |--------------------------------------------------------------------------
    */

    /**
     * Get menu by location
     */
    public static function getByLocation(string $location): ?self
    {
        return cache()->remember(
            'menu_' . $location,
            config('settings.optimization.cache_expiration', 86400),
            fn () => self::where('location', $location)
                ->where('active', true)
                ->with(['items' => fn ($q) => $q->where('active', true)->orderBy('lft')])
                ->first()
        );
    }

    /**
     * Get visible items for current user
     */
    public function getVisibleItems(): Collection
    {
        return $this->items
            ->filter(fn ($item) => $item->isVisibleToCurrentUser())
            ->values();
    }

    /**
     * Get visible root items with their visible children
     */
    public function getVisibleTree(): Collection
    {
        $items = $this->items->filter(fn ($item) => $item->isVisibleToCurrentUser());

        // Build tree structure
        $rootItems = $items->whereNull('parent_id')->values();

        return $rootItems->map(function ($item) use ($items) {
            $item->children = $items->where('parent_id', $item->id)->values();
            return $item;
        });
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
     * Get location badge HTML
     */
    public function getLocationHtml(): string
    {
        $colors = [
            'header' => 'primary',
            'footer' => 'info',
            'sidebar' => 'warning',
        ];
        $color = $colors[$this->location] ?? 'secondary';
        return '<span class="badge bg-' . $color . '">' . ucfirst($this->location) . '</span>';
    }

    /**
     * Get items count HTML
     */
    public function getItemsCountHtml(): string
    {
        // Use eager-loaded items_count if available, otherwise query
        $count = $this->items_count ?? $this->items()->count();
        return '<span class="badge bg-light text-dark">' . $count . '</span>';
    }

    /**
     * Manage items button
     */
    public function manageItemsButton(): string
    {
        $url = admin_url('menus/' . $this->id . '/items');
        return '<a class="btn btn-xs btn-info" href="' . $url . '">
                   <i class="fa-solid fa-list"></i> ' . trans('admin.manage_items') . '
               </a>';
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

    public function scopeLocation($query, string $location)
    {
        return $query->where('location', $location);
    }
}
