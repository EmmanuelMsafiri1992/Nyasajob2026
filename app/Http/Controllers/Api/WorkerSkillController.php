<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\EntityCollection;
use App\Http\Resources\WorkerSkillResource;
use App\Models\WorkerSkill;

/**
 * @group Worker Skills
 */
class WorkerSkillController extends BaseController
{
    /**
     * List worker skills
     *
     * Get all available worker skills for profile creation.
     *
     * @queryParam q string Search skills by name. Example: cleaning
     * @queryParam category_id int Filter by category ID. Example: 1
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(): \Illuminate\Http\JsonResponse
    {
        $skills = WorkerSkill::query()->active()->orderBy('lft');

        // Search filter
        if (request()->filled('q')) {
            $keywords = rawurldecode(request()->input('q'));
            $skills->where('name', 'LIKE', '%' . $keywords . '%');
        }

        // Filter by category
        if (request()->filled('category_id')) {
            $skills->where('category_id', request()->integer('category_id'));
        }

        $skills = $skills->get();

        $collection = new EntityCollection(class_basename($this), $skills);

        return apiResponse()->withCollection($collection);
    }

    /**
     * Get worker skill
     *
     * @urlParam id int required The skill ID. Example: 1
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(int $id): \Illuminate\Http\JsonResponse
    {
        $skill = WorkerSkill::find($id);

        if (empty($skill)) {
            return apiResponse()->notFound(t('worker_skill_not_found'));
        }

        $resource = new WorkerSkillResource($skill);

        return apiResponse()->withResource($resource);
    }
}
