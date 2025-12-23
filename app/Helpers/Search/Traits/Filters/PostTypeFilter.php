<?php
namespace App\Helpers\Search\Traits\Filters;

trait PostTypeFilter
{
	protected function applyPostTypeFilter()
	{
		if (!isset($this->posts)) {
			return;
		}
		
		$postTypeIds = [];
		if (request()->filled('type')) {
			$postTypeIds = request()->get('type');
		}
		
		if (empty($postTypeIds)) {
			return;
		}
		
		if (is_array($postTypeIds)) {
			$this->posts->whereIn('post_type_id', $postTypeIds);
		}
		
		// Optional
		if (is_numeric($postTypeIds)) {
			$this->posts->where('post_type_id', $postTypeIds);
		}
	}
}
