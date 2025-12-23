<?php
namespace App\Models\Traits;

use App\Models\Country;

trait CountryTrait
{
    /*
    |--------------------------------------------------------------------------
    | FUNCTIONS
    |--------------------------------------------------------------------------
    */
	public function getCountryHtml()
	{
		$out = '';
		
		if (empty($this->country) && empty($this->country_code)) {
			return $out;
		}
		
		$countryCode = $this->country->code ?? $this->country_code;
		$countryName = $this->country->name ?? $countryCode;
		
		$iconPath = 'images/flags/16/' . strtolower($countryCode) . '.png';
		if (file_exists(public_path($iconPath))) {
			$out .= '<a href="' . dmUrl($countryCode, '/', true, true) . '" target="_blank">';
			$out .= '<img src="' . url($iconPath) . getPictureVersion() . '" data-bs-toggle="tooltip" title="' . $countryName . '">';
			$out .= '</a>';
		} else {
			$out .= $countryCode;
		}
		
		return $out;
	}
    
    
    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */
    public function country()
    {
        return $this->belongsTo(Country::class, 'country_code', 'code');
    }
    
    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */
    public function scopeCurrentCountry($builder)
    {
        return $builder->where('country_code', config('country.code'));
    }
    
    public function scopeCountryOf($builder, $countryCode)
    {
        return $builder->where('country_code', $countryCode);
    }
    
    
    /*
    |--------------------------------------------------------------------------
    | ACCESSORS
    |--------------------------------------------------------------------------
    */
    
    
    /*
    |--------------------------------------------------------------------------
    | MUTATORS
    |--------------------------------------------------------------------------
    */
}
