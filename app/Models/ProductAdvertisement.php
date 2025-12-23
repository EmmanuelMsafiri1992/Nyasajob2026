<?php

namespace App\Models;

use App\Models\Scopes\ActiveScope;
use App\Http\Controllers\Admin\Panel\Library\Traits\Models\Crud;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Carbon;

class ProductAdvertisement extends BaseModel
{
	use Crud, HasFactory;

	/**
	 * The table associated with the model.
	 *
	 * @var string
	 */
	protected $table = 'product_advertisements';

	/**
	 * The primary key for the model.
	 *
	 * @var string
	 */
	// protected $primaryKey = 'id';

	/**
	 * Indicates if the model should be timestamped.
	 *
	 * @var boolean
	 */
	// public $timestamps = false;

	/**
	 * The attributes that aren't mass assignable.
	 *
	 * @var array
	 */
	protected $guarded = ['id'];

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		'user_id',
		'ad_subscription_id',
		'title',
		'description',
		'image_path',
		'url',
		'status',
		'impressions',
		'clicks',
		'starts_at',
		'expires_at',
		'active',
	];

	/**
	 * The attributes that should be hidden for arrays
	 *
	 * @var array
	 */
	// protected $hidden = [];

	/**
	 * The attributes that should be cast.
	 *
	 * @var array
	 */
	protected $casts = [
		'starts_at' => 'datetime',
		'expires_at' => 'datetime',
	];

	/*
	|--------------------------------------------------------------------------
	| FUNCTIONS
	|--------------------------------------------------------------------------
	*/
	protected static function boot()
	{
		parent::boot();

		static::addGlobalScope(new ActiveScope());
	}

	/**
	 * Check if ad is currently active and within date range
	 */
	public function isActive(): bool
	{
		if ($this->status !== 'active' || !$this->active) {
			return false;
		}

		$now = Carbon::now();

		if ($this->starts_at && $now->lt($this->starts_at)) {
			return false;
		}

		if ($this->expires_at && $now->gt($this->expires_at)) {
			return false;
		}

		return true;
	}

	/**
	 * Check if ad has reached its limits
	 */
	public function hasReachedLimits(): bool
	{
		if (!$this->subscription || !$this->subscription->package) {
			return false;
		}

		$package = $this->subscription->package;

		if ($package->impressions_limit && $this->impressions >= $package->impressions_limit) {
			return true;
		}

		if ($package->clicks_limit && $this->clicks >= $package->clicks_limit) {
			return true;
		}

		return false;
	}

	/**
	 * Increment impressions count
	 */
	public function recordImpression(): void
	{
		$this->increment('impressions');
	}

	/**
	 * Increment clicks count
	 */
	public function recordClick(): void
	{
		$this->increment('clicks');
	}

	/*
	|--------------------------------------------------------------------------
	| RELATIONS
	|--------------------------------------------------------------------------
	*/
	public function user()
	{
		return $this->belongsTo(User::class, 'user_id');
	}

	public function subscription()
	{
		return $this->belongsTo(AdSubscription::class, 'ad_subscription_id');
	}

	public function targeting()
	{
		return $this->hasMany(AdTargeting::class, 'product_advertisement_id');
	}

	/*
	|--------------------------------------------------------------------------
	| SCOPES
	|--------------------------------------------------------------------------
	*/
	public function scopeActiveAds($query)
	{
		return $query->where('status', 'active')
			->where('active', 1)
			->where(function ($q) {
				$q->whereNull('starts_at')
					->orWhere('starts_at', '<=', Carbon::now());
			})
			->where(function ($q) {
				$q->whereNull('expires_at')
					->orWhere('expires_at', '>=', Carbon::now());
			});
	}

	public function scopeForCountry($query, $countryCode)
	{
		return $query->whereHas('targeting', function ($q) use ($countryCode) {
			$q->where('target_type', 'country')
				->where('target_code', $countryCode);
		});
	}

	public function scopeForState($query, $stateCode)
	{
		return $query->whereHas('targeting', function ($q) use ($stateCode) {
			$q->where('target_type', 'state')
				->where('target_code', $stateCode);
		});
	}

	public function scopeForCity($query, $cityId)
	{
		return $query->whereHas('targeting', function ($q) use ($cityId) {
			$q->where('target_type', 'city')
				->where('target_code', $cityId);
		});
	}

	public function scopeFirstPosition($query)
	{
		return $query->whereHas('subscription.package', function ($q) {
			$q->where('first_position', 1);
		});
	}

	/*
	|--------------------------------------------------------------------------
	| ACCESSORS | MUTATORS
	|--------------------------------------------------------------------------
	*/
	protected function imagePath(): Attribute
	{
		return Attribute::make(
			get: function ($value) {
				if (empty($value)) {
					return null;
				}

				if (str_starts_with($value, 'http://') || str_starts_with($value, 'https://')) {
					return $value;
				}

				return url('storage/' . $value);
			},
		);
	}

	/*
	|--------------------------------------------------------------------------
	| OTHER PRIVATE METHODS
	|--------------------------------------------------------------------------
	*/
}
