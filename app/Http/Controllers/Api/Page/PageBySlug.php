<?php
namespace App\Http\Controllers\Api\Page;

use App\Models\Page;

trait PageBySlug
{
	/**
	 * Get Page by Slug
	 * NOTE: Slug must be unique
	 *
	 * @param $slug
	 * @param null $locale
	 * @return mixed
	 */
	private function getPageBySlug($slug, $locale = null)
	{
		if (empty($locale)) {
			$locale = config('app.locale');
		}
		
		$cacheId = 'page.slug.' . $slug . '.' . $locale;
		$page = cache()->remember($cacheId, $this->cacheExpiration, function () use ($slug, $locale) {
			$page = Page::query()->where('slug', $slug);
			
			$embed = explode(',', request()->get('embed'));
			
			if (in_array('parent', $embed)) {
				$page->with('parent');
			}
			
			return $page->first();
		});
		
		if (!empty($page)) {
			$page->setLocale($locale);
		}
		
		return $page;
	}
	
	/**
	 * Get Page by ID
	 *
	 * @param $pageId
	 * @param null $locale
	 * @return mixed
	 */
	public function getPageById($pageId, $locale = null)
	{
		if (empty($locale)) {
			$locale = config('app.locale');
		}
		
		$cacheId = 'page.' . $pageId . '.' . $locale;
		$page = cache()->remember($cacheId, $this->cacheExpiration, function () use ($pageId, $locale) {
			$page = Page::query()->where('id', $pageId);
			
			$embed = explode(',', request()->get('embed'));
			
			if (in_array('parent', $embed)) {
				$page->with('parent');
			}
			
			return $page->first();
		});
		
		if (!empty($page)) {
			$page->setLocale($locale);
		}
		
		return $page;
	}
}
