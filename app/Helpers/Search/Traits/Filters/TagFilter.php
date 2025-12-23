<?php
namespace App\Helpers\Search\Traits\Filters;

use Illuminate\Support\Facades\Route;

trait TagFilter
{
	protected function applyTagFilter()
	{
		if (!isset($this->posts)) {
			return;
		}
		
		$tag = null;
		if (request()->filled('tag')) {
			$tag = request()->get('tag');
		}
		
		$tag = (is_string($tag)) ? $tag : null;
		
		if (empty(trim($tag))) {
			return;
		}
		
		$tag = rawurldecode($tag);
		$tag = mb_strtolower($tag);
		
		$this->posts->whereRaw('FIND_IN_SET(?, LOWER(tags)) > 0', [$tag]);
	}
}
