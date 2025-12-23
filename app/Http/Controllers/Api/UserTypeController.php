<?php
namespace App\Http\Controllers\Api;

use App\Http\Resources\EntityCollection;
use App\Http\Resources\UserTypeResource;
use App\Models\UserType;

/**
 * @group Users
 */
class UserTypeController extends BaseController
{
	/**
	 * List user types
	 *
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function index(): \Illuminate\Http\JsonResponse
	{
		$userTypes = UserType::query()->get();
		
		$resourceCollection = new EntityCollection(class_basename($this), $userTypes);
		
		$message = ($userTypes->count() <= 0) ? t('no_user_types_found') : null;
		
		return $this->respondWithCollection($resourceCollection, $message);
	}
	
	/**
	 * Get user type
	 *
	 * @urlParam id int required The user type's ID. Example: 1
	 *
	 * @param $id
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function show($id): \Illuminate\Http\JsonResponse
	{
		$userType = UserType::query()->where('id', $id);
		
		$userType = $userType->first();
		
		abort_if(empty($userType), 404, t('user_type_not_found'));
		
		$resource = new UserTypeResource($userType);
		
		return $this->respondWithResource($resource);
	}
}
