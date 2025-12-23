<?php

namespace App\Models;

use App\Models\Scopes\ActiveScope;
use App\Http\Controllers\Admin\Panel\Library\Traits\Models\Crud;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Carbon;

class AdSubscription extends BaseModel
{
	use Crud, HasFactory;

	/**
	 * The table associated with the model.
	 *
	 * @var string
	 */
	protected $table = 'ad_subscriptions';

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
		'ad_package_id',
		'payment_method_id',
		'transaction_id',
		'amount',
		'currency_code',
		'status',
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

	public function getUserNameHtml(): string
	{
		$out = '--';

		if (!empty($this->user)) {
			$url = admin_url('users/' . $this->user_id . '/edit');
			$out = '<a href="' . $url . '">' . $this->user->name . '</a>';
		}

		return $out;
	}

	public function getPackageNameHtml(): string
	{
		$out = $this->ad_package_id;

		if (!empty($this->package)) {
			$packageUrl = admin_url('ad_packages/' . $this->ad_package_id . '/edit');

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

	public function getAmountHtml(): string
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

	/**
	 * Check if subscription is currently active
	 */
	public function isActive(): bool
	{
		if ($this->status !== 'active' || !$this->active) {
			return false;
		}

		if ($this->expires_at && Carbon::now()->gt($this->expires_at)) {
			return false;
		}

		return true;
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

	public function package()
	{
		return $this->belongsTo(AdPackage::class, 'ad_package_id');
	}

	public function paymentMethod()
	{
		return $this->belongsTo(PaymentMethod::class, 'payment_method_id');
	}

	public function advertisements()
	{
		return $this->hasMany(ProductAdvertisement::class, 'ad_subscription_id');
	}

	/*
	|--------------------------------------------------------------------------
	| SCOPES
	|--------------------------------------------------------------------------
	*/
	public function scopeActiveSubscriptions($query)
	{
		return $query->where('status', 'active')
			->where('active', 1)
			->where(function ($q) {
				$q->whereNull('expires_at')
					->orWhere('expires_at', '>=', Carbon::now());
			});
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

				if (!$createdAt) {
					return null;
				}

				$value = new Carbon($createdAt);

				return $value->format('Y-m-d H:i:s');
			},
		);
	}

	/*
	|--------------------------------------------------------------------------
	| OTHER PRIVATE METHODS
	|--------------------------------------------------------------------------
	*/
}
