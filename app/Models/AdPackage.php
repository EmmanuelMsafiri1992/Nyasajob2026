<?php

namespace App\Models;

use App\Models\Scopes\ActiveScope;
use App\Http\Controllers\Admin\Panel\Library\Traits\Models\Crud;
use App\Http\Controllers\Admin\Panel\Library\Traits\Models\SpatieTranslatable\HasTranslations;
use Illuminate\Database\Eloquent\Casts\Attribute;

class AdPackage extends BaseModel
{
	use Crud, HasTranslations;

	/**
	 * The table associated with the model.
	 *
	 * @var string
	 */
	protected $table = 'ad_packages';

	/**
	 * The primary key for the model.
	 *
	 * @var string
	 */
	// protected $primaryKey = 'id';
	protected $appends = ['description_array', 'description_string'];

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
		'name',
		'short_name',
		'price',
		'currency_code',
		'duration_days',
		'first_position',
		'impressions_limit',
		'clicks_limit',
		'description',
		'recommended',
		'active',
		'lft',
		'rgt',
		'depth',
	];
	public $translatable = ['name', 'short_name', 'description'];

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

		static::addGlobalScope(new ActiveScope());
	}

	public function getNameHtml(): string
	{
		$currentUrl = preg_replace('#/(search)$#', '', url()->current());
		$url = $currentUrl . '/' . $this->id . '/edit';
		$badge = '';
		if (!empty($this->short_name)) {
			$badge = ' <span class="badge bg-primary float-right">' . $this->short_name . '</span>';
		}

		return '<a href="' . $url . '">' . $this->name . '</a>' . $badge;
	}

	/*
	|--------------------------------------------------------------------------
	| RELATIONS
	|--------------------------------------------------------------------------
	*/
	public function currency()
	{
		return $this->belongsTo(Currency::class, 'currency_code', 'code');
	}

	public function subscriptions()
	{
		return $this->hasMany(AdSubscription::class, 'ad_package_id');
	}

	/*
	|--------------------------------------------------------------------------
	| SCOPES
	|--------------------------------------------------------------------------
	*/
	public function scopeApplyCurrency($builder)
	{
		if (config('settings.geo_location.local_currency_packages_activation')) {
			$builder->where('currency_code', config('country.currency'));
		}

		return $builder;
	}

	/*
	|--------------------------------------------------------------------------
	| ACCESSORS | MUTATORS
	|--------------------------------------------------------------------------
	*/
	protected function descriptionArray(): Attribute
	{
		return Attribute::make(
			get: fn ($value) => $this->getDescriptionArray($value),
		);
	}

	protected function descriptionString(): Attribute
	{
		return Attribute::make(
			get: function ($value) {
				if (!isset($this->description_array)) {
					return null;
				}

				$description = '';

				$options = $this->description_array;
				if (is_array($options)) {
					$options = array_filter($options, function ($value) { return !is_null($value) && $value !== ''; });
					$options = array_unique($options);
					if (count($options) > 0) {
						$description .= implode(". \n", $options);
					}
				}

				return $description;
			},
		);
	}

	/*
	|--------------------------------------------------------------------------
	| OTHER PRIVATE METHODS
	|--------------------------------------------------------------------------
	*/
	private function getDescriptionArray($value)
	{
		$locale = app()->getLocale();

		$description = [];

		if (isset($this->duration_days) && $this->duration_days > 0) {
			$description[] = trans_choice('global.duration_of_ad_campaign',
				getPlural($this->duration_days),
				['number' => $this->duration_days], $locale);
		}

		if (isset($this->first_position) && $this->first_position == 1) {
			$description[] = t('ad_appears_first_position');
		}

		if (isset($this->impressions_limit) && $this->impressions_limit > 0) {
			$description[] = trans_choice('global.max_impressions_allowed',
				getPlural($this->impressions_limit),
				['number' => number_format($this->impressions_limit)], $locale);
		}

		if (isset($this->clicks_limit) && $this->clicks_limit > 0) {
			$description[] = trans_choice('global.max_clicks_allowed',
				getPlural($this->clicks_limit),
				['number' => number_format($this->clicks_limit)], $locale);
		}

		$otherOptions = [];
		if (isset($this->description)) {
			$otherOptions = preg_split('#[\n;\.]+#ui', $this->description);
			$otherOptions = array_filter($otherOptions, function ($value) { return !is_null($value) && $value !== ''; });
			$otherOptions = array_unique($otherOptions);
			if (is_array($otherOptions) && count($otherOptions) > 0) {
				foreach ($otherOptions as $option) {
					$description[] = $option;
				}
			}
		}

		return $description;
	}
}
