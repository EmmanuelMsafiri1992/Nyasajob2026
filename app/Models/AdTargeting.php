<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class AdTargeting extends BaseModel
{
	use HasFactory;

	/**
	 * The table associated with the model.
	 *
	 * @var string
	 */
	protected $table = 'ad_targeting';

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
		'product_advertisement_id',
		'target_type',
		'target_code',
	];

	/**
	 * The attributes that should be hidden for arrays
	 *
	 * @var array
	 */
	// protected $hidden = [];

	/*
	|--------------------------------------------------------------------------
	| FUNCTIONS
	|--------------------------------------------------------------------------
	*/

	/*
	|--------------------------------------------------------------------------
	| RELATIONS
	|--------------------------------------------------------------------------
	*/
	public function advertisement()
	{
		return $this->belongsTo(ProductAdvertisement::class, 'product_advertisement_id');
	}

	public function country()
	{
		return $this->belongsTo(Country::class, 'target_code', 'code')
			->where('target_type', 'country');
	}

	public function state()
	{
		return $this->belongsTo(SubAdmin1::class, 'target_code', 'code')
			->where('target_type', 'state');
	}

	public function city()
	{
		return $this->belongsTo(City::class, 'target_code', 'id')
			->where('target_type', 'city');
	}

	/*
	|--------------------------------------------------------------------------
	| SCOPES
	|--------------------------------------------------------------------------
	*/
	public function scopeCountries($query)
	{
		return $query->where('target_type', 'country');
	}

	public function scopeStates($query)
	{
		return $query->where('target_type', 'state');
	}

	public function scopeCities($query)
	{
		return $query->where('target_type', 'city');
	}

	/*
	|--------------------------------------------------------------------------
	| ACCESSORS | MUTATORS
	|--------------------------------------------------------------------------
	*/

	/*
	|--------------------------------------------------------------------------
	| OTHER PRIVATE METHODS
	|--------------------------------------------------------------------------
	*/
}
