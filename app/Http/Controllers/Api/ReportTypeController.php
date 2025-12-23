<?php
namespace App\Http\Controllers\Api;

use App\Http\Resources\EntityCollection;
use App\Http\Resources\ReportTypeResource;
use App\Models\ReportType;

/**
 * @group Posts
 */
class ReportTypeController extends BaseController
{
	/**
	 * List report types
	 *
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function index(): \Illuminate\Http\JsonResponse
	{
		$reportTypes = ReportType::query()->get();
		
		$resourceCollection = new EntityCollection(class_basename($this), $reportTypes);
		
		$message = ($reportTypes->count() <= 0) ? t('no_report_types_found') : null;
		
		return $this->respondWithCollection($resourceCollection, $message);
	}
	
	/**
	 * Get report type
	 *
	 * @urlParam id int required The report type's ID. Example: 1
	 *
	 * @param $id
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function show($id): \Illuminate\Http\JsonResponse
	{
		$reportType = ReportType::query()->where('id', $id);
		
		$reportType = $reportType->first();
		
		abort_if(empty($reportType), 404, t('report_type_not_found'));
		
		$resource = new ReportTypeResource($reportType);
		
		return $this->respondWithResource($resource);
	}
}
