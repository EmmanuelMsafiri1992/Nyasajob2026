<?php

namespace App\Http\Controllers;

use App\Models\JobMatch;
use App\Models\Resume;
use App\Services\AutoApplicationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class JobMatchesController extends Controller
{
    protected $autoApplicationService;

    public function __construct(AutoApplicationService $autoApplicationService)
    {
        $this->autoApplicationService = $autoApplicationService;
    }

    /**
     * Display list of job matches for authenticated user
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        // Build query
        $query = JobMatch::where('user_id', $user->id)
            ->with(['post', 'post.city', 'post.category', 'resume'])
            ->orderBy('created_at', 'desc');

        // Filter by status
        if ($request->has('status') && $request->status != 'all') {
            $query->where('status', $request->status);
        }

        // Filter by match percentage
        if ($request->has('min_match') && is_numeric($request->min_match)) {
            $query->where('match_percentage', '>=', $request->min_match);
        }

        // Filter by applied status
        if ($request->has('applied')) {
            $query->where('applied', $request->applied == 'yes' ? 1 : 0);
        }

        // Paginate results
        $matches = $query->paginate(20);

        // Statistics
        $stats = [
            'total' => JobMatch::where('user_id', $user->id)->count(),
            'pending' => JobMatch::where('user_id', $user->id)->where('status', 'pending_review')->count(),
            'auto_applied' => JobMatch::where('user_id', $user->id)->where('status', 'auto_applied')->count(),
            'manually_applied' => JobMatch::where('user_id', $user->id)->where('status', 'manually_applied')->count(),
            'rejected' => JobMatch::where('user_id', $user->id)->where('status', 'rejected')->count(),
            'avg_match' => JobMatch::where('user_id', $user->id)->avg('match_percentage'),
        ];

        return view('account.job-matches.index', compact('matches', 'stats'));
    }

    /**
     * Show details of a specific job match
     */
    public function show(JobMatch $match)
    {
        $user = Auth::user();

        // Ensure match belongs to authenticated user
        if ($match->user_id !== $user->id) {
            abort(403, 'Unauthorized access to job match');
        }

        // Load relationships
        $match->load(['post', 'post.city', 'post.category', 'post.company', 'resume', 'user']);

        // Mark as viewed
        if (!$match->viewed_at) {
            $match->update(['viewed_at' => now()]);
        }

        // Get user's resumes for manual application
        $resumes = Resume::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get(['id', 'name', 'created_at']);

        return view('account.job-matches.show', compact('match', 'resumes'));
    }

    /**
     * Manually apply to a job match
     */
    public function apply(Request $request, JobMatch $match)
    {
        $user = Auth::user();

        // Ensure match belongs to authenticated user
        if ($match->user_id !== $user->id) {
            abort(403, 'Unauthorized access');
        }

        // Check if already applied
        if ($match->applied) {
            return redirect()->back()
                ->with('error', 'You have already applied to this job.');
        }

        // Validate input
        $validator = Validator::make($request->all(), [
            'resume_id' => 'required|exists:resumes,id',
            'cover_letter' => 'nullable|string|max:5000',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Verify resume belongs to user
        $resume = Resume::where('id', $request->resume_id)
            ->where('user_id', $user->id)
            ->first();

        if (!$resume) {
            return redirect()->back()
                ->with('error', 'Invalid resume selected.');
        }

        try {
            // Use auto-application service for manual application
            $result = $this->autoApplicationService->manualApply(
                $match,
                $request->resume_id,
                $request->cover_letter
            );

            if ($result) {
                return redirect()->route('job-matches.show', $match->id)
                    ->with('success', 'Your application has been submitted successfully!');
            } else {
                return redirect()->back()
                    ->with('error', 'Failed to submit application. Please try again.');
            }
        } catch (\Exception $e) {
            \Log::error('Manual application failed: ' . $e->getMessage(), [
                'user_id' => $user->id,
                'match_id' => $match->id,
                'error' => $e->getMessage()
            ]);

            return redirect()->back()
                ->with('error', 'An error occurred while submitting your application. Please try again later.');
        }
    }

    /**
     * Reject a job match
     */
    public function reject(JobMatch $match)
    {
        $user = Auth::user();

        // Ensure match belongs to authenticated user
        if ($match->user_id !== $user->id) {
            abort(403, 'Unauthorized access');
        }

        // Check if already applied
        if ($match->applied) {
            return redirect()->back()
                ->with('error', 'Cannot reject a job you have already applied to.');
        }

        // Update match status
        $match->update([
            'status' => 'rejected',
            'user_action_at' => now(),
        ]);

        return redirect()->route('job-matches.index')
            ->with('success', 'Job match has been rejected.');
    }

    /**
     * Approve a job match (marks for future application)
     */
    public function approve(JobMatch $match)
    {
        $user = Auth::user();

        // Ensure match belongs to authenticated user
        if ($match->user_id !== $user->id) {
            abort(403, 'Unauthorized access');
        }

        // Check if already applied
        if ($match->applied) {
            return redirect()->back()
                ->with('info', 'You have already applied to this job.');
        }

        // Update match status
        $match->update([
            'status' => 'approved',
            'user_action_at' => now(),
        ]);

        return redirect()->route('job-matches.show', $match->id)
            ->with('success', 'Job match has been approved. You can now apply to this job.');
    }
}
