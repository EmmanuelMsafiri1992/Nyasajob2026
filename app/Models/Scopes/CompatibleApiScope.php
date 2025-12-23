<?php
namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Scope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class CompatibleApiScope implements Scope
{
	/**
	 * Apply the scope to a given Eloquent query builder.
	 *
	 * @param \Illuminate\Database\Eloquent\Builder $builder
	 * @param \Illuminate\Database\Eloquent\Model $model
	 * @return \Illuminate\Database\Eloquent\Builder
	 */
	public function apply(Builder $builder, Model $model)
	{
		// Load only the API Compatible entries for API call
		if (isFromApi()) {
			if (!isFromTheAppsWebEnvironment()) {
				$builder->where('is_compatible_api', 1);
			}
		}
		
		// Load all entries for Web call
		return $builder;
	}
}
