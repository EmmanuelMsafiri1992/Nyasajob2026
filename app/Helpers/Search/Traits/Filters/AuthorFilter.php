<?php
namespace App\Helpers\Search\Traits\Filters;

trait AuthorFilter
{
	protected function applyAuthorFilter()
	{
		if (!isset($this->posts)) {
			return;
		}
		
		$userId = null;
		$username = null;
		if (request()->filled('userId')) {
			$userId = request()->get('userId');
		}
		if (request()->filled('username')) {
			$username = request()->get('username');
		}
		
		$userId = (is_numeric($userId)) ? $userId : null;
		$username = (is_string($username)) ? $username : null;
		
		if (empty($userId) && empty($username)) {
			return;
		}
		
		if (!empty($userId)) {
			$this->posts->where('user_id', $userId);
		}
		
		if (!empty($username)) {
			$this->posts->whereHas('user', function ($query) use($username) {
				$query->where('username', $username);
			});
		}
	}
}
