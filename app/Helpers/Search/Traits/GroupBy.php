<?php
namespace App\Helpers\Search\Traits;

use Illuminate\Support\Facades\DB;

trait GroupBy
{
	protected function applyGroupBy()
	{
		if (!(isset($this->posts) && isset($this->groupBy))) {
			return;
		}
		
		if (is_array($this->groupBy) && count($this->groupBy) > 0) {
			// Get valid columns name
			$this->groupBy = collect($this->groupBy)->map(function ($value, $key) {
				if (str_contains($value, '.')) {
					$value = DB::getTablePrefix() . $value;
				}
				
				return $value;
			})->toArray();
			
			$this->posts->groupByRaw(implode(', ', $this->groupBy));
		}
	}
}
