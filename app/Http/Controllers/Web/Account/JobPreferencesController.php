<?php
/**
 * Nyasajob - Job Board Web Application
 * Job Matching Preferences Controller
 */

namespace App\Http\Controllers\Web\Account;

use App\Models\Category;
use App\Models\Resume;
use App\Models\UserJobPreference;
use Illuminate\Http\Request;
use Larapen\LaravelMetaTags\Facades\MetaTag;

class JobPreferencesController extends AccountBaseController
{
    /**
     * Display and edit job preferences
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function index()
    {
        // Get or create user's job preference
        $preference = auth()->user()->jobPreference ?? new UserJobPreference();

        // Get categories for selection
        $categories = Category::orderBy('name', 'asc')->get();

        // Get user's resumes
        $resumes = Resume::where('user_id', auth()->id())->get();

        // Define urgency levels
        $urgencyLevels = [
            'not_urgent' => [
                'label' => 'Not Urgent',
                'description' => 'I have a job, just casually looking. Manual review for all matches.'
            ],
            'moderate' => [
                'label' => 'Moderate',
                'description' => 'Need a job within 1-2 months. Auto-apply to great matches (70%+).'
            ],
            'very_urgent' => [
                'label' => 'Very Urgent',
                'description' => 'Need a job within 2-4 weeks. Auto-apply to good matches (50%+).'
            ],
            'desperate' => [
                'label' => 'Extremely Urgent',
                'description' => 'Need a job immediately! Auto-apply to all reasonable matches (40%+).'
            ],
        ];

        // Meta Tags
        MetaTag::set('title', t('Job Matching Preferences'));
        MetaTag::set('description', t('Set your job matching preferences on :appName', ['appName' => config('settings.app.name')]));

        return appView('account.job-preferences.index', compact('preference', 'categories', 'resumes', 'urgencyLevels'));
    }

    /**
     * Store or update job preferences
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // Validation
        $validated = $request->validate([
            'preferred_categories' => 'required|array|min:1',
            'preferred_categories.*' => 'exists:categories,id',
            'skills' => 'nullable|string|max:1000',
            'qualifications' => 'nullable|string|max:1000',
            'min_salary' => 'nullable|numeric|min:0',
            'max_salary' => 'nullable|numeric|min:0',
            'remote_work' => 'nullable|boolean',
            'auto_apply_enabled' => 'nullable|boolean',
            'urgency_level' => 'required|in:not_urgent,moderate,very_urgent,desperate',
            'max_applications_per_day' => 'nullable|integer|min:0|max:50',
            'min_match_percentage' => 'nullable|integer|min:40|max:100',
            'default_resume_id' => 'nullable|exists:resumes,id',
            'cover_letter_template' => 'nullable|string|max:5000',
        ]);

        // Prepare data
        $data = [
            'user_id' => auth()->id(),
            'preferred_categories' => $validated['preferred_categories'],
            'skills' => $validated['skills'] ?? null,
            'qualifications' => $validated['qualifications'] ?? null,
            'min_salary' => $validated['min_salary'] ?? null,
            'max_salary' => $validated['max_salary'] ?? null,
            'remote_work' => $request->has('remote_work'),
            'auto_apply_enabled' => $request->has('auto_apply_enabled'),
            'urgency_level' => $validated['urgency_level'],
            'max_applications_per_day' => $validated['max_applications_per_day'] ?? 5,
            'min_match_percentage' => $validated['min_match_percentage'] ?? 60,
            'default_resume_id' => $validated['default_resume_id'] ?? null,
            'cover_letter_template' => $validated['cover_letter_template'] ?? null,
        ];

        try {
            // Update or create preference
            $preference = UserJobPreference::updateOrCreate(
                ['user_id' => auth()->id()],
                $data
            );

            flash('Job preferences saved successfully! You will now receive job matches based on your criteria.')->success();

            return redirect()->route('job-preferences.index');
        } catch (\Exception $e) {
            flash('Error saving preferences: ' . $e->getMessage())->error();
            return back()->withInput();
        }
    }

    /**
     * Update existing preferences
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request)
    {
        return $this->store($request);
    }
}
