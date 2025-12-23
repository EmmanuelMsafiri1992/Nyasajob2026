<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Resume;
use App\Models\UserJobPreference;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class JobPreferencesController extends Controller
{
    /**
     * Display the job preferences form
     */
    public function index()
    {
        $user = Auth::user();

        // Get user's existing preference or create new instance
        $preference = $user->jobPreference ?? new UserJobPreference();

        // Get all active categories for the user's country
        $categories = Category::where('active', 1)
            ->orderBy('name', 'asc')
            ->get(['id', 'name']);

        // Get user's resumes
        $resumes = Resume::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get(['id', 'name', 'created_at']);

        // Urgency level options (from model)
        $urgencyLevels = [
            'not_urgent' => [
                'label' => 'Not Urgent',
                'description' => 'I\'m casually looking. Show me matches, I\'ll review and apply manually.',
            ],
            'moderate' => [
                'label' => 'Moderately Urgent',
                'description' => 'I need a job soon. Auto-apply to great matches (70%+), show me the rest.',
            ],
            'very_urgent' => [
                'label' => 'Very Urgent',
                'description' => 'I need a job quickly. Auto-apply to good matches (50%+).',
            ],
            'desperate' => [
                'label' => 'Extremely Urgent',
                'description' => 'I need a job immediately. Auto-apply to all reasonable matches (40%+).',
            ],
        ];

        return view('account.job-preferences.index', compact(
            'preference',
            'categories',
            'resumes',
            'urgencyLevels'
        ));
    }

    /**
     * Store a new job preference
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        // Check if user already has preferences
        if ($user->jobPreference) {
            return redirect()->route('job-preferences.index')
                ->with('error', 'You already have job preferences set. Use the update form instead.');
        }

        // Validate input
        $validator = Validator::make($request->all(), [
            'preferred_categories' => 'nullable|array',
            'preferred_categories.*' => 'exists:categories,id',
            'skills' => 'nullable|string|max:1000',
            'qualifications' => 'nullable|string|max:2000',
            'min_salary' => 'nullable|numeric|min:0',
            'max_salary' => 'nullable|numeric|min:0|gte:min_salary',
            'employment_type' => 'nullable|string|max:50',
            'remote_work' => 'boolean',
            'auto_apply_enabled' => 'boolean',
            'urgency_level' => 'required|in:not_urgent,moderate,very_urgent,desperate',
            'max_applications_per_day' => 'nullable|integer|min:0|max:50',
            'min_match_percentage' => 'nullable|integer|min:40|max:100',
            'cover_letter_template' => 'nullable|string|max:5000',
            'default_resume_id' => 'nullable|exists:resumes,id',
        ]);

        if ($validator->fails()) {
            return redirect()->route('job-preferences.index')
                ->withErrors($validator)
                ->withInput();
        }

        // Create new preference
        $preference = new UserJobPreference();
        $preference->user_id = $user->id;
        $preference->preferred_categories = $request->input('preferred_categories', []);
        $preference->skills = $request->input('skills');
        $preference->qualifications = $request->input('qualifications');
        $preference->min_salary = $request->input('min_salary');
        $preference->max_salary = $request->input('max_salary');
        $preference->employment_type = $request->input('employment_type');
        $preference->remote_work = $request->boolean('remote_work');
        $preference->auto_apply_enabled = $request->boolean('auto_apply_enabled');
        $preference->urgency_level = $request->input('urgency_level', 'not_urgent');
        $preference->max_applications_per_day = $request->input('max_applications_per_day', 5);
        $preference->min_match_percentage = $request->input('min_match_percentage', 60);
        $preference->cover_letter_template = $request->input('cover_letter_template');
        $preference->default_resume_id = $request->input('default_resume_id');

        $preference->save();

        return redirect()->route('job-preferences.index')
            ->with('success', 'Your job preferences have been saved! We\'ll start matching you with relevant jobs.');
    }

    /**
     * Update existing job preference
     */
    public function update(Request $request)
    {
        $user = Auth::user();
        $preference = $user->jobPreference;

        // Check if preference exists
        if (!$preference) {
            return redirect()->route('job-preferences.index')
                ->with('error', 'No job preferences found. Please create one first.');
        }

        // Validate input
        $validator = Validator::make($request->all(), [
            'preferred_categories' => 'nullable|array',
            'preferred_categories.*' => 'exists:categories,id',
            'skills' => 'nullable|string|max:1000',
            'qualifications' => 'nullable|string|max:2000',
            'min_salary' => 'nullable|numeric|min:0',
            'max_salary' => 'nullable|numeric|min:0|gte:min_salary',
            'employment_type' => 'nullable|string|max:50',
            'remote_work' => 'boolean',
            'auto_apply_enabled' => 'boolean',
            'urgency_level' => 'required|in:not_urgent,moderate,very_urgent,desperate',
            'max_applications_per_day' => 'nullable|integer|min:0|max:50',
            'min_match_percentage' => 'nullable|integer|min:40|max:100',
            'cover_letter_template' => 'nullable|string|max:5000',
            'default_resume_id' => 'nullable|exists:resumes,id',
        ]);

        if ($validator->fails()) {
            return redirect()->route('job-preferences.index')
                ->withErrors($validator)
                ->withInput();
        }

        // Update preference
        $preference->preferred_categories = $request->input('preferred_categories', []);
        $preference->skills = $request->input('skills');
        $preference->qualifications = $request->input('qualifications');
        $preference->min_salary = $request->input('min_salary');
        $preference->max_salary = $request->input('max_salary');
        $preference->employment_type = $request->input('employment_type');
        $preference->remote_work = $request->boolean('remote_work');
        $preference->auto_apply_enabled = $request->boolean('auto_apply_enabled');
        $preference->urgency_level = $request->input('urgency_level', 'not_urgent');
        $preference->max_applications_per_day = $request->input('max_applications_per_day', 5);
        $preference->min_match_percentage = $request->input('min_match_percentage', 60);
        $preference->cover_letter_template = $request->input('cover_letter_template');
        $preference->default_resume_id = $request->input('default_resume_id');

        $preference->save();

        return redirect()->route('job-preferences.index')
            ->with('success', 'Your job preferences have been updated successfully!');
    }
}
