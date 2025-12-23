<?php
namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Scope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Route;

class StrictActiveScope implements Scope
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
		// Load all entries from some Admin panel Controllers:
		// - Admin\PaymentController
		// - Admin\AjaxController
		if (
			str_contains(Route::currentRouteAction(), 'Admin\PaymentController')
			|| str_contains(Route::currentRouteAction(), 'Admin\AjaxController')
			|| str_contains(Route::currentRouteAction(), 'Admin\InlineRequestController')
		) {
			return $builder;
		}
	
		// Load only activated entries for the rest of the website (Admin panel & Front)
        return $builder->where('active', 1);
    }
}
