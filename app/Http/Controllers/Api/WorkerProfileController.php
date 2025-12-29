<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\EntityCollection;
use App\Http\Resources\WorkerProfileResource;
use App\Models\WorkerProfile;
use Illuminate\Http\Request;

/**
 * @group Worker Profiles
 */
class WorkerProfileController extends BaseController
{
    /**
     * List public worker profiles
     *
     * @queryParam q string Search by title or bio. Example: house maid
     * @queryParam skill_id int Filter by skill ID. Example: 1
     * @queryParam city_id int Filter by city ID. Example: 5
     * @queryParam availability string Filter by availability status. Example: available
     * @queryParam sort string Sorting parameter. Possible values: created_at, experience_years. Example: -created_at
     * @queryParam perPage int Items per page. Example: 12
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(): \Illuminate\Http\JsonResponse
    {
        $perPage = getNumberOfItemsPerPage('worker_profiles', request()->integer('perPage'), 12);

        $profiles = WorkerProfile::query()
            ->public()
            ->inCountry();

        // Eager load relationships
        $embed = explode(',', request()->input('embed', ''));
        $with = [];
        if (in_array('skills', $embed)) {
            $with[] = 'skills';
        }
        if (in_array('city', $embed)) {
            $with[] = 'city';
        }
        if (in_array('user', $embed)) {
            $with[] = 'user';
        }
        if (!empty($with)) {
            $profiles->with($with);
        }

        // Apply search filter
        if (request()->filled('q')) {
            $keywords = rawurldecode(request()->input('q'));
            $profiles->where(function ($query) use ($keywords) {
                $query->where('title', 'LIKE', '%' . $keywords . '%')
                    ->orWhere('bio', 'LIKE', '%' . $keywords . '%')
                    ->orWhere('custom_skills', 'LIKE', '%' . $keywords . '%');
            });
        }

        // Filter by skill
        if (request()->filled('skill_id')) {
            $skillId = request()->integer('skill_id');
            $profiles->whereHas('skills', function ($query) use ($skillId) {
                $query->where('worker_skills.id', $skillId);
            });
        }

        // Filter by city
        if (request()->filled('city_id')) {
            $profiles->where('city_id', request()->integer('city_id'));
        }

        // Filter by availability
        if (request()->filled('availability')) {
            $profiles->where('availability_status', request()->input('availability'));
        }

        // Sorting
        $profiles = $this->applySorting($profiles, ['created_at', 'experience_years', 'views']);

        $profiles = $profiles->paginate($perPage);

        // Set pagination base URL for web environment
        $profiles = setPaginationBaseUrl($profiles);

        $collection = new EntityCollection(class_basename($this), $profiles);

        $message = ($profiles->count() <= 0) ? t('no_worker_profiles_found') : null;

        return apiResponse()->withCollection($collection, $message);
    }

    /**
     * Get worker profile
     *
     * @queryParam embed string Comma-separated list of relationships. Possible values: skills, city, user. Example: skills,city
     *
     * @urlParam id int required The worker profile ID. Example: 1
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(int $id): \Illuminate\Http\JsonResponse
    {
        $embed = explode(',', request()->input('embed', ''));

        $profile = WorkerProfile::query()->where('id', $id);

        // Eager load relationships
        $with = [];
        if (in_array('skills', $embed)) {
            $with[] = 'skills';
        }
        if (in_array('city', $embed)) {
            $with[] = 'city';
        }
        if (in_array('user', $embed)) {
            $with[] = 'user';
        }
        if (!empty($with)) {
            $profile->with($with);
        }

        $profile = $profile->first();

        if (empty($profile)) {
            return apiResponse()->notFound(t('worker_profile_not_found'));
        }

        // Only public profiles can be viewed unless owner
        $authUser = auth('sanctum')->user();
        if (!$profile->is_public && (!$authUser || $authUser->id !== $profile->user_id)) {
            return apiResponse()->notFound(t('worker_profile_not_found'));
        }

        // Increment view count
        $profile->incrementViews();

        $resource = new WorkerProfileResource($profile);

        return apiResponse()->withResource($resource);
    }

    /**
     * Store worker profile
     *
     * @authenticated
     * @header Authorization Bearer {YOUR_AUTH_TOKEN}
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request): \Illuminate\Http\JsonResponse
    {
        $authUser = auth('sanctum')->user();
        if (!isset($authUser->id)) {
            return apiResponse()->notFound(t('user_not_found'));
        }

        // Check if user already has a profile
        $existingProfile = WorkerProfile::where('user_id', $authUser->id)->first();
        if ($existingProfile) {
            return apiResponse()->error(t('worker_profile_already_exists'));
        }

        $validated = $request->validate([
            'title' => 'required|string|max:200',
            'bio' => 'nullable|string|max:2000',
            'district' => 'nullable|string|max:150',
            'city_id' => 'nullable|integer|exists:cities,id',
            'custom_skills' => 'nullable|string|max:500',
            'availability_status' => 'required|in:available,busy,not_available',
            'is_public' => 'boolean',
            'experience_years' => 'nullable|integer|min:0|max:50',
            'hourly_rate' => 'nullable|numeric|min:0',
            'currency_code' => 'nullable|string|max:3',
            'gender' => 'nullable|in:male,female,other',
            'date_of_birth' => 'nullable|date',
            'phone' => 'nullable|string|max:60',
            'email' => 'nullable|email|max:100',
            'whatsapp' => 'nullable|string|max:60',
            'skills' => 'nullable|array',
            'skills.*' => 'integer|exists:worker_skills,id',
        ]);

        $profileData = array_merge($validated, [
            'user_id' => $authUser->id,
            'country_code' => config('country.code'),
        ]);

        // Remove skills from profile data as it's handled separately
        $skillIds = $profileData['skills'] ?? [];
        unset($profileData['skills']);

        $profile = WorkerProfile::create($profileData);

        // Attach skills
        if (!empty($skillIds)) {
            $profile->skills()->attach($skillIds);
        }

        $profile->load('skills');

        $data = [
            'success' => true,
            'message' => t('worker_profile_created_successfully'),
            'result' => (new WorkerProfileResource($profile))->toArray($request),
        ];

        return apiResponse()->json($data);
    }

    /**
     * Update worker profile
     *
     * @authenticated
     * @header Authorization Bearer {YOUR_AUTH_TOKEN}
     *
     * @urlParam id int required The worker profile ID. Example: 1
     *
     * @param int $id
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(int $id, Request $request): \Illuminate\Http\JsonResponse
    {
        $authUser = auth('sanctum')->user();
        if (!isset($authUser->id)) {
            return apiResponse()->notFound(t('user_not_found'));
        }

        $profile = WorkerProfile::where('user_id', $authUser->id)->where('id', $id)->first();

        if (empty($profile)) {
            return apiResponse()->notFound(t('worker_profile_not_found'));
        }

        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:200',
            'bio' => 'nullable|string|max:2000',
            'district' => 'nullable|string|max:150',
            'city_id' => 'nullable|integer|exists:cities,id',
            'custom_skills' => 'nullable|string|max:500',
            'availability_status' => 'sometimes|required|in:available,busy,not_available',
            'is_public' => 'boolean',
            'experience_years' => 'nullable|integer|min:0|max:50',
            'hourly_rate' => 'nullable|numeric|min:0',
            'currency_code' => 'nullable|string|max:3',
            'gender' => 'nullable|in:male,female,other',
            'date_of_birth' => 'nullable|date',
            'phone' => 'nullable|string|max:60',
            'email' => 'nullable|email|max:100',
            'whatsapp' => 'nullable|string|max:60',
            'skills' => 'nullable|array',
            'skills.*' => 'integer|exists:worker_skills,id',
        ]);

        // Handle skills separately
        $skillIds = $validated['skills'] ?? null;
        unset($validated['skills']);

        $profile->update($validated);

        // Update skills if provided
        if ($skillIds !== null) {
            $profile->skills()->sync($skillIds);
        }

        $profile->load('skills');

        $data = [
            'success' => true,
            'message' => t('worker_profile_updated_successfully'),
            'result' => (new WorkerProfileResource($profile))->toArray($request),
        ];

        return apiResponse()->json($data);
    }

    /**
     * Delete worker profile
     *
     * @authenticated
     * @header Authorization Bearer {YOUR_AUTH_TOKEN}
     *
     * @urlParam id int required The worker profile ID. Example: 1
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(int $id): \Illuminate\Http\JsonResponse
    {
        $authUser = auth('sanctum')->user();
        if (!isset($authUser->id)) {
            return apiResponse()->notFound(t('user_not_found'));
        }

        $profile = WorkerProfile::where('user_id', $authUser->id)->where('id', $id)->first();

        if (empty($profile)) {
            return apiResponse()->notFound(t('worker_profile_not_found'));
        }

        $profile->delete();

        $data = [
            'success' => true,
            'message' => t('worker_profile_deleted_successfully'),
            'result' => null,
        ];

        return apiResponse()->json($data);
    }

    /**
     * Toggle profile visibility
     *
     * @authenticated
     * @header Authorization Bearer {YOUR_AUTH_TOKEN}
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function toggleVisibility(Request $request): \Illuminate\Http\JsonResponse
    {
        $authUser = auth('sanctum')->user();
        if (!isset($authUser->id)) {
            return apiResponse()->notFound(t('user_not_found'));
        }

        $profile = WorkerProfile::where('user_id', $authUser->id)->first();

        if (empty($profile)) {
            return apiResponse()->notFound(t('worker_profile_not_found'));
        }

        $profile->is_public = !$profile->is_public;
        $profile->save();

        $message = $profile->is_public
            ? t('worker_profile_now_public')
            : t('worker_profile_now_private');

        $data = [
            'success' => true,
            'message' => $message,
            'result' => [
                'is_public' => $profile->is_public,
            ],
        ];

        return apiResponse()->json($data);
    }
}
