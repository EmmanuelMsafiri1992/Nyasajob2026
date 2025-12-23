<?php
namespace App\Http\Requests\Admin;

class AdPackageRequest extends Request
{
	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules()
	{
		return [
			'name'              => ['required', 'min:2', 'max:100'],
			'short_name'        => ['nullable', 'max:100'],
			'price'             => ['required', 'numeric', 'min:0'],
			'currency_code'     => ['required', 'max:3'],
			'duration_days'     => ['nullable', 'integer', 'min:1'],
			'first_position'    => ['nullable', 'boolean'],
			'impressions_limit' => ['nullable', 'integer', 'min:0'],
			'clicks_limit'      => ['nullable', 'integer', 'min:0'],
			'description'       => ['nullable'],
			'recommended'       => ['nullable', 'boolean'],
			'active'            => ['nullable', 'boolean'],
		];
	}
}
