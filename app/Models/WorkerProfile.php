<?php

namespace App\Models;

use App\Helpers\Files\Storage\StorageDisk;
use App\Models\Scopes\LocalizedScope;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Http\Controllers\Web\Admin\Panel\Library\Traits\Models\Crud;

#[ScopedBy([LocalizedScope::class])]
class WorkerProfile extends BaseModel
{
    use Crud, HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'worker_profiles';

    /**
     * The attributes that are appended to the model.
     *
     * @var array<int, string>
     */
    protected $appends = ['photo_url', 'skills_list'];

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
        'user_id',
        'country_code',
        'city_id',
        'district',
        'title',
        'bio',
        'custom_skills',
        'availability_status',
        'is_public',
        'photo',
        'experience_years',
        'hourly_rate',
        'currency_code',
        'gender',
        'date_of_birth',
        'phone',
        'email',
        'whatsapp',
        'views',
        'featured_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'phone',
        'email',
        'whatsapp',
    ];

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_public' => 'boolean',
            'experience_years' => 'integer',
            'hourly_rate' => 'decimal:2',
            'views' => 'integer',
            'date_of_birth' => 'date',
            'featured_at' => 'datetime',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    /**
     * Get the user that owns this profile.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the city for this profile.
     */
    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class, 'city_id');
    }

    /**
     * Get the country for this profile.
     */
    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class, 'country_code', 'code');
    }

    /**
     * Get the currency for this profile.
     */
    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class, 'currency_code', 'code');
    }

    /**
     * Get the skills for this profile.
     */
    public function skills(): BelongsToMany
    {
        return $this->belongsToMany(
            WorkerSkill::class,
            'worker_profile_skills',
            'worker_profile_id',
            'worker_skill_id'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    /**
     * Scope to get only public profiles.
     */
    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    /**
     * Scope to get only available workers.
     */
    public function scopeAvailable($query)
    {
        return $query->where('availability_status', 'available');
    }

    /**
     * Scope to get workers in a specific country.
     */
    public function scopeInCountry($query, $countryCode = null)
    {
        $countryCode = $countryCode ?? config('country.code');

        return $query->where('country_code', $countryCode);
    }

    /**
     * Scope to get featured workers.
     */
    public function scopeFeatured($query)
    {
        return $query->whereNotNull('featured_at');
    }

    /*
    |--------------------------------------------------------------------------
    | ACCESSORS
    |--------------------------------------------------------------------------
    */

    /**
     * Get the photo URL.
     */
    protected function photoUrl(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (empty($this->photo)) {
                    return $this->getDefaultPhotoUrl();
                }

                $disk = StorageDisk::getDisk();
                if (!$disk->exists($this->photo)) {
                    return $this->getDefaultPhotoUrl();
                }

                return imgUrl($this->photo, 'medium');
            },
        );
    }

    /**
     * Get the skills list as comma-separated string.
     */
    protected function skillsList(): Attribute
    {
        return Attribute::make(
            get: function () {
                $skillNames = $this->skills->pluck('name')->toArray();

                if (!empty($this->custom_skills)) {
                    $customSkills = array_map('trim', explode(',', $this->custom_skills));
                    $skillNames = array_merge($skillNames, $customSkills);
                }

                return implode(', ', $skillNames);
            },
        );
    }

    /**
     * Get the availability status formatted.
     */
    protected function availabilityStatusFormatted(): Attribute
    {
        return Attribute::make(
            get: function () {
                return match ($this->availability_status) {
                    'available' => t('Available'),
                    'busy' => t('Busy'),
                    'not_available' => t('Not Available'),
                    default => $this->availability_status,
                };
            },
        );
    }

    /**
     * Get the availability badge color class.
     */
    protected function availabilityBadgeClass(): Attribute
    {
        return Attribute::make(
            get: function () {
                return match ($this->availability_status) {
                    'available' => 'bg-success',
                    'busy' => 'bg-warning',
                    'not_available' => 'bg-secondary',
                    default => 'bg-secondary',
                };
            },
        );
    }

    /*
    |--------------------------------------------------------------------------
    | OTHER METHODS
    |--------------------------------------------------------------------------
    */

    /**
     * Get the default photo URL.
     */
    private function getDefaultPhotoUrl(): string
    {
        $defaultGender = ($this->gender === 'female') ? 'female' : 'male';
        return url('images/user-' . $defaultGender . '.png');
    }

    /**
     * Check if contact details should be visible to the given user.
     */
    public function canShowContactDetailsTo($user): bool
    {
        if (empty($user)) {
            return false;
        }

        // Owner can always see their own details
        if ($user->id === $this->user_id) {
            return true;
        }

        // Admin can see all details
        if ($user->is_admin) {
            return true;
        }

        // Check if user is a verified employer with active subscription
        return $this->isVerifiedEmployer($user);
    }

    /**
     * Check if user is a verified employer with active subscription.
     */
    private function isVerifiedEmployer($user): bool
    {
        // Must be an employer (user_type_id = 1)
        if ($user->user_type_id != 1) {
            return false;
        }

        // Must have email verified
        if (empty($user->email_verified_at)) {
            return false;
        }

        // Must have phone verified
        if (empty($user->phone_verified_at)) {
            return false;
        }

        // Must have active subscription
        $hasActiveSubscription = $user->subscriptions()
            ->where('status', 'active')
            ->where('end_date', '>', now())
            ->exists();

        return $hasActiveSubscription;
    }

    /**
     * Get contact details for authorized users.
     */
    public function getContactDetailsFor($user): array
    {
        if (!$this->canShowContactDetailsTo($user)) {
            return [
                'phone' => null,
                'email' => null,
                'whatsapp' => null,
                'can_view' => false,
            ];
        }

        return [
            'phone' => $this->phone,
            'email' => $this->email,
            'whatsapp' => $this->whatsapp,
            'can_view' => true,
        ];
    }

    /**
     * Increment the view count.
     */
    public function incrementViews(): void
    {
        $this->increment('views');
    }
}
