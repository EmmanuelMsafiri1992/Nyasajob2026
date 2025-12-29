<?php

namespace App\Http\Controllers\Web\Public;

use App\Models\WorkerProfile;
use App\Models\WorkerSkill;
use App\Models\City;
use Larapen\LaravelMetaTags\Facades\MetaTag;

class WorkerProfileController extends FrontController
{
    /**
     * Display a listing of public worker profiles.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function index()
    {
        $perPage = 12;

        $profiles = WorkerProfile::query()
            ->with(['skills', 'city', 'user'])
            ->public()
            ->inCountry();

        // Apply filters
        if (request()->filled('q')) {
            $keywords = request()->input('q');
            $profiles->where(function ($query) use ($keywords) {
                $query->where('title', 'LIKE', '%' . $keywords . '%')
                    ->orWhere('bio', 'LIKE', '%' . $keywords . '%')
                    ->orWhere('custom_skills', 'LIKE', '%' . $keywords . '%');
            });
        }

        if (request()->filled('skill_id')) {
            $skillId = request()->integer('skill_id');
            $profiles->whereHas('skills', function ($query) use ($skillId) {
                $query->where('worker_skills.id', $skillId);
            });
        }

        if (request()->filled('city_id')) {
            $profiles->where('city_id', request()->integer('city_id'));
        }

        if (request()->filled('availability')) {
            $profiles->where('availability_status', request()->input('availability'));
        }

        // Sorting
        $orderBy = request()->input('orderBy', 'created_at');
        if ($orderBy === 'experience') {
            $profiles->orderByDesc('experience_years');
        } else {
            $profiles->orderByDesc('created_at');
        }

        $profiles = $profiles->paginate($perPage);

        // Get filter data
        $skills = WorkerSkill::active()->orderBy('lft')->get();
        $cities = City::inCountry()->orderBy('name')->get();

        $appName = config('settings.app.name', 'Site Name');
        $title = t('Available Workers') . ' - ' . $appName;

        // Meta Tags
        MetaTag::set('title', $title);
        MetaTag::set('description', t('Browse available workers for hire'));

        return appView('worker-profile.index', compact('profiles', 'skills', 'cities'));
    }

    /**
     * Display a single worker profile.
     *
     * @param int $id
     * @return \Illuminate\Contracts\View\View
     */
    public function show(int $id)
    {
        $profile = WorkerProfile::query()
            ->with(['skills', 'city', 'user', 'country'])
            ->where('id', $id)
            ->first();

        if (empty($profile)) {
            abort(404, t('Worker profile not found'));
        }

        // Check if profile is public or user is owner
        $authUser = auth()->user();
        $isOwner = $authUser && $authUser->id === $profile->user_id;

        if (!$profile->is_public && !$isOwner) {
            abort(404, t('Worker profile not found'));
        }

        // Increment view count (only if not owner)
        if (!$isOwner) {
            $profile->incrementViews();
        }

        // Check if current user can view contact details
        $canViewContact = $profile->canShowContactDetailsTo($authUser);
        $contactDetails = $profile->getContactDetailsFor($authUser);

        // Get similar profiles (same skills)
        $similarProfiles = WorkerProfile::query()
            ->with(['skills', 'city'])
            ->public()
            ->inCountry()
            ->where('id', '!=', $profile->id)
            ->whereHas('skills', function ($query) use ($profile) {
                $query->whereIn('worker_skills.id', $profile->skills->pluck('id'));
            })
            ->limit(4)
            ->get();

        $appName = config('settings.app.name', 'Site Name');
        $title = ($profile->title ?? $profile->user->name ?? 'Worker Profile') . ' - ' . $appName;

        // Meta Tags
        MetaTag::set('title', $title);
        MetaTag::set('description', str($profile->bio)->limit(160)->toString());

        return appView('worker-profile.show', compact(
            'profile',
            'canViewContact',
            'contactDetails',
            'similarProfiles',
            'isOwner'
        ));
    }
}
