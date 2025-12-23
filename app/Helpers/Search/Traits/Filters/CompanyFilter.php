<?php
namespace App\Helpers\Search\Traits\Filters;

trait CompanyFilter
{
	protected function applyCompanyFilter()
	{
		if (!isset($this->posts)) {
			return;
		}
		
		$companyId = null;
		if (request()->filled('companyId')) {
			$companyId = request()->get('companyId');
		}
		
		if (empty($companyId)) {
			return;
		}
		
		$this->posts->where('company_id', $companyId);
	}
}
