<?php
namespace App\Http\Requests\Admin;

use App\Rules\BlacklistUniqueRule;

class BlacklistRequest extends Request
{
	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules()
	{
		return [
			'type'  => ['required'],
			'entry' => [new BlacklistUniqueRule($this->type)],
		];
	}
}
