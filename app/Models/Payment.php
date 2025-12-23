<?php
/**
 * Nyasajob - Job Board Web Application
 * Copyright (c) BeDigit. All Rights Reserved
 *
 * Website: https://laraclassifier.com/jobclass
 *
 * LICENSE
 * -------
 * This software is furnished under a license and may be used and copied
 * only in accordance with the terms of such license and with the inclusion
 * of the above copyright notice. If you Purchased from CodeCanyon,
 * Please read the full License from here - http://codecanyon.net/licenses/standard
 */

namespace App\Models;

use App\Helpers\Date;
use App\Helpers\UrlGen;
use App\Models\Scopes\LocalizedScope;
use App\Models\Scopes\StrictActiveScope;
use App\Observers\PaymentObserver;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Carbon;
use App\Http\Controllers\Admin\Panel\Library\Traits\Models\Crud;

class Payment extends BaseModel
{
	use Crud, HasFactory;
	
	/**
	 * The table associated with the model.
	 *
	 * @var string
	 */
	protected $table = 'payments';
	
	/**
	 * The primary key for the model.
	 *
	 * @var string
	 */
	// protected $primaryKey = 'id';
	protected $appends = [
		'created_at_formatted',
		'period_start_formatted',
		'period_end_formatted',
		'canceled_at_formatted',
		'refunded_at_formatted',
	];
	
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
		'payable_id',
		'payable_type',
		'post_id', // Keep for backward compatibility
		'package_id',
		'payment_method_id',
		'transaction_id',
		'amount',
		'currency_code',
		'period_start',
		'period_end',
		'canceled_at',
		'refunded_at',
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
			'period_start' => 'datetime',
			'period_end'   => 'datetime',
			'canceled_at'  => 'datetime',
			'refunded_at'  => 'datetime',
			'created_at'   => 'datetime',
			'updated_at'   => 'datetime',
		];
	}
	
	/**
	 * The attributes that should be hidden for arrays
	 *
	 * @var array
	 */
	// protected $hidden = [];
	
	/**
	 * The attributes that should be mutated to dates.
	 *
	 * @var array
	 */
	// protected $dates = [];
	
	/*
	|--------------------------------------------------------------------------
	| FUNCTIONS
	|--------------------------------------------------------------------------
	*/
	protected static function boot()
	{
		parent::boot();
		
		Payment::observe(PaymentObserver::class);
		
		static::addGlobalScope(new StrictActiveScope());
		static::addGlobalScope(new LocalizedScope());
	}
	
	public function getPostTitleHtml(): string
	{
		$out = '';
		
		$blankImage = '<img src="/images/blank.gif" style="width: 16px; height: 16px;" alt="">';
		
		if (empty($this->post_id)) {
			return $blankImage;
		}
		
		if (
			empty($this->post)
			|| (empty($this->post->country) && empty($this->post->country_code))
		) {
			$out .= $blankImage;
			$out .= ' ';
			$out .= '#' . $this->post_id;
			
			return $out;
		}
		
		$countryCode = $this->post->country->code ?? $this->post->country_code;
		$countryName = $this->post->country->name ?? $countryCode;
		
		// Post's Country
		$iconPath = 'images/flags/16/' . strtolower($countryCode) . '.png';
		if (file_exists(public_path($iconPath))) {
			$out .= '<a href="' . dmUrl($countryCode, '/', true, true) . '" target="_blank">';
			$out .= '<img src="' . url($iconPath) . getPictureVersion() . '" data-bs-toggle="tooltip" title="' . $countryName . '">';
			$out .= '</a>';
		} else {
			$out .= $blankImage;
		}
		$out .= ' ';
		
		// Post's ID
		$out .= '#' . $this->post_id;
		
		// Post's Title & Link
		// $postUrl = url(UrlGen::postUri($this->post));
		$postUrl = dmUrl($countryCode, UrlGen::postPath($this->post));
		$out .= ' - ';
		$out .= '<a href="' . $postUrl . '" target="_blank">' . $this->post->title . '</a>';
		
		if (config('settings.single.listings_review_activation')) {
			$outLeft = '<div class="float-left">' . $out . '</div>';
			$outRight = '<div class="float-right"></div>';
			
			if ($this->active != 1) {
				// Check if this listing has at least successful payment
				$countSuccessfulPayments = Payment::where('post_id', $this->post_id)->where('active', 1)->count();
				if ($countSuccessfulPayments <= 0) {
					$msg = trans('admin.payment_listing_delete_btn_tooltip');
					$tooltip = ' data-bs-toggle="tooltip" title="' . $msg . '"';
					
					$outRight = '<div class="float-right">';
					$outRight .= '<a href="' . admin_url('posts/' . $this->post_id) . '" class="btn btn-xs btn-danger" data-button-type="delete"' . $tooltip . '>';
					$outRight .= '<i class="fa fa-trash"></i> ';
					$outRight .= trans('admin.Delete');
					$outRight .= '</a>';
					$outRight .= '</div>';
				}
			}
			
			$out = $outLeft . $outRight;
		}
		
		return $out;
	}
	
	public function getPackageNameHtml()
	{
		$out = $this->package_id;
		
		if (!empty($this->package)) {
			$packageUrl = admin_url('packages/' . $this->package_id . '/edit');
			
			$out = '<a href="' . $packageUrl . '">';
			$out .= $this->package->name;
			$out .= '</a>';
			$out .= ' (' . $this->package->price . ' ' . $this->package->currency_code . ')';
		}
		
		return $out;
	}
	
	public function getPaymentMethodNameHtml(): string
	{
		$out = '--';
		
		if (!empty($this->paymentMethod)) {
			$paymentMethodUrl = admin_url('payment_methods/' . $this->payment_method_id . '/edit');
			
			$out = '<a href="' . $paymentMethodUrl . '">';
			if ($this->paymentMethod->name == 'offlinepayment') {
				$out .= trans('offlinepayment::messages.Offline Payment');
			} else {
				$out .= $this->paymentMethod->display_name;
			}
			$out .= '</a>';
		}
		
		return $out;
	}
	
	public function getAmountHtml()
	{
		$out = $this->amount;
		
		if (isset($this->currency_code) && !empty($this->currency_code)) {
			$out .= ' ' . $this->currency_code;
		} else {
			if (!empty($this->package)) {
				$out .= ' ' . $this->package->currency_code;
			}
		}
		
		return $out;
	}
	
	/*
	|--------------------------------------------------------------------------
	| RELATIONS
	|--------------------------------------------------------------------------
	*/
	/**
	 * Get the parent payable model (Post|User).
	 */
	public function payable(): MorphTo
	{
		return $this->morphTo('payable', 'payable_type', 'payable_id');
	}

	/**
	 * Keep for backward compatibility
	 */
	public function post(): BelongsTo
	{
		return $this->belongsTo(Post::class, 'post_id');
	}

	public function package(): BelongsTo
	{
		return $this->belongsTo(Package::class, 'package_id');
	}

	public function paymentMethod(): BelongsTo
	{
		return $this->belongsTo(PaymentMethod::class, 'payment_method_id');
	}

	/**
	 * For subscriptions
	 * Get all the listings for the payment
	 */
	public function posts(): HasMany
	{
		return $this->hasMany(Post::class, 'payment_id')->orderByDesc('id');
	}
	
	/*
	|--------------------------------------------------------------------------
	| SCOPES
	|--------------------------------------------------------------------------
	*/
	public function scopePromotion(Builder $builder): Builder
	{
		return $builder->where('payable_type', Post::class);
	}

	public function scopeSubscription(Builder $builder): Builder
	{
		return $builder->where('payable_type', User::class);
	}

	/**
	 * On hold payment(s) (The validity period will be started in the future)
	 */
	public function scopeOnHold(Builder $builder): Builder
	{
		$today = Carbon::now(Date::getAppTimeZone());

		return $builder->where(function (Builder $query) use ($today) {
			$query->where('period_start', '>', $today)->where('period_end', '>', $today);
		})
			->notCanceled()
			->notRefunded();
	}

	/**
	 * Valid payment(s) (Covers the validity period)
	 */
	public function scopeValid(Builder $builder): Builder
	{
		$today = Carbon::now(Date::getAppTimeZone());

		return $builder->where(function (Builder $query) use ($today) {
			$query->where('period_start', '<=', $today)->where('period_end', '>=', $today);
		})
			->notCanceled()
			->notRefunded();
	}

	/**
	 * Not valid payment(s) (Does not cover the validity period)
	 */
	public function scopeNotValid(Builder $builder): Builder
	{
		$today = Carbon::now(Date::getAppTimeZone());

		return $builder->where(function (Builder $query) use ($today) {
			$query->where('period_end', '<', $today);
		})
			->orWhere(fn ($query) => $query->canceled())
			->orWhere(fn ($query) => $query->refunded());
	}

	/**
	 * Canceled payment(s)
	 */
	public function scopeCanceled(Builder $builder): Builder
	{
		return $builder->whereNotNull('canceled_at');
	}

	/**
	 * Not canceled payment(s)
	 */
	public function scopeNotCanceled(Builder $builder): Builder
	{
		return $builder->whereNull('canceled_at');
	}

	/**
	 * Refunded payment(s)
	 */
	public function scopeRefunded(Builder $builder): Builder
	{
		return $builder->whereNotNull('refunded_at');
	}

	/**
	 * Not refunded payment(s)
	 */
	public function scopeNotRefunded(Builder $builder): Builder
	{
		return $builder->whereNull('refunded_at');
	}

	/**
	 * Active payment(s)
	 */
	public function scopeActive(Builder $builder): Builder
	{
		return $builder->where('active', 1);
	}

	/*
	|--------------------------------------------------------------------------
	| ACCESSORS | MUTATORS
	|--------------------------------------------------------------------------
	*/
	protected function createdAtFormatted(): Attribute
	{
		return Attribute::make(
			get: function ($value) {
				$createdAt = $this->attributes['created_at'] ?? $this->created_at ?? null;

				$value = new Carbon($createdAt);
				$value->timezone(Date::getAppTimeZone());

				return Date::formatFormNow($value);
			},
		);
	}

	protected function periodStartFormatted(): Attribute
	{
		return Attribute::make(
			get: function ($value) {
				if (empty($this->period_start)) return null;

				$value = new Carbon($this->period_start);
				$value->timezone(Date::getAppTimeZone());

				return Date::formatFormNow($value);
			},
		);
	}

	protected function periodEndFormatted(): Attribute
	{
		return Attribute::make(
			get: function ($value) {
				if (empty($this->period_end)) return null;

				$value = new Carbon($this->period_end);
				$value->timezone(Date::getAppTimeZone());

				return Date::formatFormNow($value);
			},
		);
	}

	protected function canceledAtFormatted(): Attribute
	{
		return Attribute::make(
			get: function ($value) {
				if (empty($this->canceled_at)) return null;

				$value = new Carbon($this->canceled_at);
				$value->timezone(Date::getAppTimeZone());

				return Date::formatFormNow($value);
			},
		);
	}

	protected function refundedAtFormatted(): Attribute
	{
		return Attribute::make(
			get: function ($value) {
				if (empty($this->refunded_at)) return null;

				$value = new Carbon($this->refunded_at);
				$value->timezone(Date::getAppTimeZone());

				return Date::formatFormNow($value);
			},
		);
	}

	/*
	|--------------------------------------------------------------------------
	| OTHER PRIVATE METHODS
	|--------------------------------------------------------------------------
	*/
}
