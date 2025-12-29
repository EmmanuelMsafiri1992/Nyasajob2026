<?php

namespace App\Http\Controllers\Web\Public\Account;

use App\Models\WorkerProfile;
use App\Models\WorkerSkill;
use App\Models\City;
use Illuminate\Http\Request;
use Larapen\LaravelMetaTags\Facades\MetaTag;

class WorkerProfileController extends AccountBaseController
{
    /**
     * Display the worker profile or redirect to create if not exists.
     *
     * @return \Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     */
    public function index()
    {
        $authUser = auth()->user();
        $profile = WorkerProfile::with(['skills', 'city'])
            ->where('user_id', $authUser->id)
            ->first();

        if (empty($profile)) {
            return redirect()->route('account.worker-profile.create');
        }

        $appName = config('settings.app.name', 'Site Name');
        $title = t('My Worker Profile') . ' - ' . $appName;

        // Meta Tags
        MetaTag::set('title', $title);
        MetaTag::set('description', t('Manage your worker profile'));

        return appView('account.worker-profile.index', compact('profile'));
    }

    /**
     * Show the form for creating a new worker profile.
     *
     * @return \Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     */
    public function create()
    {
        $authUser = auth()->user();

        // Check if user already has a profile
        $existingProfile = WorkerProfile::where('user_id', $authUser->id)->first();
        if ($existingProfile) {
            return redirect()->route('account.worker-profile.edit');
        }

        $skills = WorkerSkill::active()->orderBy('lft')->get();
        $cities = City::inCountry()->orderBy('name')->get();

        // Meta Tags
        MetaTag::set('title', t('Create Worker Profile'));
        MetaTag::set('description', t('Create your worker profile to be discovered by employers'));

        return appView('account.worker-profile.create', compact('skills', 'cities'));
    }

    /**
     * Store a newly created worker profile.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $authUser = auth()->user();

        // Check if user already has a profile
        $existingProfile = WorkerProfile::where('user_id', $authUser->id)->first();
        if ($existingProfile) {
            flash(t('You already have a worker profile'))->error();
            return redirect()->route('account.worker-profile');
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
            'gender' => 'nullable|in:male,female,other',
            'phone' => 'nullable|string|max:60',
            'email' => 'nullable|email|max:100',
            'whatsapp' => 'nullable|string|max:60',
            'skills' => 'nullable|array',
            'skills.*' => 'integer|exists:worker_skills,id',
            'photo' => 'nullable|image|max:2048',
        ]);

        // Handle photo upload
        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('worker-profiles', 'public');
        }

        $profileData = array_merge($validated, [
            'user_id' => $authUser->id,
            'country_code' => config('country.code'),
            'photo' => $photoPath,
            'is_public' => $request->boolean('is_public'),
        ]);

        // Remove skills from profile data
        $skillIds = $profileData['skills'] ?? [];
        unset($profileData['skills']);

        $profile = WorkerProfile::create($profileData);

        // Attach skills
        if (!empty($skillIds)) {
            $profile->skills()->attach($skillIds);
        }

        flash(t('Worker profile created successfully'))->success();

        return redirect()->route('account.worker-profile');
    }

    /**
     * Show the form for editing the worker profile.
     *
     * @return \Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     */
    public function edit()
    {
        $authUser = auth()->user();
        $profile = WorkerProfile::with('skills')
            ->where('user_id', $authUser->id)
            ->first();

        if (empty($profile)) {
            return redirect()->route('account.worker-profile.create');
        }

        $skills = WorkerSkill::active()->orderBy('lft')->get();
        $cities = City::inCountry()->orderBy('name')->get();
        $selectedSkillIds = $profile->skills->pluck('id')->toArray();

        // Meta Tags
        MetaTag::set('title', t('Edit Worker Profile'));
        MetaTag::set('description', t('Update your worker profile'));

        return appView('account.worker-profile.edit', compact('profile', 'skills', 'cities', 'selectedSkillIds'));
    }

    /**
     * Update the worker profile.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request)
    {
        $authUser = auth()->user();
        $profile = WorkerProfile::where('user_id', $authUser->id)->first();

        if (empty($profile)) {
            flash(t('Worker profile not found'))->error();
            return redirect()->route('account.worker-profile.create');
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
            'gender' => 'nullable|in:male,female,other',
            'phone' => 'nullable|string|max:60',
            'email' => 'nullable|email|max:100',
            'whatsapp' => 'nullable|string|max:60',
            'skills' => 'nullable|array',
            'skills.*' => 'integer|exists:worker_skills,id',
            'photo' => 'nullable|image|max:2048',
        ]);

        // Handle photo upload
        if ($request->hasFile('photo')) {
            // Delete old photo if exists
            if ($profile->photo && \Storage::disk('public')->exists($profile->photo)) {
                \Storage::disk('public')->delete($profile->photo);
            }
            $validated['photo'] = $request->file('photo')->store('worker-profiles', 'public');
        }

        $validated['is_public'] = $request->boolean('is_public');

        // Handle skills separately
        $skillIds = $validated['skills'] ?? [];
        unset($validated['skills']);

        $profile->update($validated);

        // Sync skills
        $profile->skills()->sync($skillIds);

        flash(t('Worker profile updated successfully'))->success();

        return redirect()->route('account.worker-profile');
    }

    /**
     * Toggle the profile visibility.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function toggleVisibility()
    {
        $authUser = auth()->user();
        $profile = WorkerProfile::where('user_id', $authUser->id)->first();

        if (empty($profile)) {
            flash(t('Worker profile not found'))->error();
            return redirect()->route('account.worker-profile.create');
        }

        $profile->is_public = !$profile->is_public;
        $profile->save();

        $message = $profile->is_public
            ? t('Your profile is now visible to employers')
            : t('Your profile is now hidden from employers');

        flash($message)->success();

        return redirect()->route('account.worker-profile');
    }

    /**
     * Remove the worker profile.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy()
    {
        $authUser = auth()->user();
        $profile = WorkerProfile::where('user_id', $authUser->id)->first();

        if (empty($profile)) {
            flash(t('Worker profile not found'))->error();
            return redirect()->route('account.worker-profile');
        }

        // Delete photo if exists
        if ($profile->photo && \Storage::disk('public')->exists($profile->photo)) {
            \Storage::disk('public')->delete($profile->photo);
        }

        $profile->delete();

        flash(t('Worker profile deleted successfully'))->success();

        return redirect()->to('account');
    }
}
