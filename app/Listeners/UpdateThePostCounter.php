<?php
/*
 * JobClass - Job Board Web Application
 * Copyright (c) BeDigit. All Rights Reserved
 *
 * Website: https://laraclassifier.com/jobclass
 * Author: BeDigit | https://bedigit.com
 *
 * LICENSE
 * -------
 * This software is furnished under a license and may be used and copied
 * only in accordance with the terms of such license and with the inclusion
 * of the above copyright notice. If you Purchased from CodeCanyon,
 * Please read the full License from here - https://codecanyon.net/licenses/standard
 */

namespace App\Listeners;

use App\Events\PostWasVisited;

class UpdateThePostCounter
{
	/**
	 * Create the event listener.
	 */
	public function __construct()
	{
		//
	}
	
	/**
	 * Handle the event.
	 *
	 * @param \App\Events\PostWasVisited $event
	 * @return bool
	 */
	public function handle(PostWasVisited $event)
	{
		$isFromApi = isFromApi();

		// Don't count the self-visits
		$guard = $isFromApi ? 'sanctum' : null;
		if (auth($guard)->check()) {
			if (auth($guard)->user()->id == $event->post->user_id) {
				return false;
			}
		}

		// Generate a unique visitor identifier based on IP and User Agent
		$visitorHash = md5(request()->ip() . request()->userAgent());
		$cacheKey = 'post_visit_' . $event->post->id . '_' . $visitorHash;

		// Check if this visitor has already viewed this post recently (within 1 hour)
		if (cache()->has($cacheKey)) {
			return false;
		}

		// Also check session and header for additional protection
		if ($isFromApi) {
			if (
				request()->hasHeader('X-VISITED-BY-SAME-SESSION')
				&& request()->header('X-VISITED-BY-SAME-SESSION') == $event->post->id
			) {
				return false;
			}
		} else {
			if (
				session()->has('postIsVisited')
				&& session('postIsVisited') == $event->post->id
			) {
				return false;
			}
		}

		// Update the counter
		$this->updateCounter($event->post);

		// Cache the visit for 1 hour to prevent duplicate counting
		cache()->put($cacheKey, true, 3600);

		// Also set session for web requests
		if (!$isFromApi) {
			session()->put('postIsVisited', $event->post->id);
		}

		return true;
	}
	
	/**
	 * @param $post
	 * @return void
	 */
	public function updateCounter($post): void
	{
		try {
			// Remove|unset the 'pictures' attribute (added to limit pictures number related to a selected package)
			$attributes = $post->getAttributes();
			if (isset($attributes['pictures'])) {
				unset($attributes['pictures']);
				$post->setRawAttributes($attributes, true);
			}
			
			$post->visits = $post->visits + 1;
			$post->save();
		} catch (\Throwable $e) {
		}
	}
}
