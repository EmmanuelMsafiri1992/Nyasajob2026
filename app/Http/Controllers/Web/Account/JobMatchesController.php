<?php
/**
 * Nyasajob - Job Board Web Application
 * Job Matches Controller
 */

namespace App\Http\Controllers\Web\Account;

use App\Models\JobMatch;
use App\Models\Resume;
use App\Services\AutoApplicationService;
use Illuminate\Http\Request;
use Larapen\LaravelMetaTags\Facades\MetaTag;

class JobMatchesController extends AccountBaseController
{
    protected $autoApplicationService;

    public function __construct(AutoApplicationService $autoApplicationService)
    {
        parent::__construct();
        $this->autoApplicationService = $autoApplicationService;
    }

    /**
     * Display a listing of job matches
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\View
     */
    public function index(Request $request)
    {
        $user = auth()->user();

        // Build query
        $query = JobMatch::where('user_id', $user->id)
            ->with(['post.category', 'post.city', 'resume'])
            ->orderBy('created_at', 'desc');

        // Apply filters
        if ($request->filled('status') && $request->status != 'all') {
            $query->where('status', $request->status);
        }

        if ($request->filled('min_match')) {
            $query->where('match_percentage', '>=', $request->min_match);
        }

        if ($request->filled('applied')) {
            $applied = $request->applied == 'yes' ? 1 : 0;
            $query->where('applied', $applied);
        }

        // Paginate results
        $matches = $query->paginate(15)->appends($request->all());

        // Calculate statistics
        $stats = [
            'total' => JobMatch::where('user_id', $user->id)->count(),
            'pending' => JobMatch::where('user_id', $user->id)->where('status', 'pending_review')->count(),
            'auto_applied' => JobMatch::where('user_id', $user->id)->where('status', 'auto_applied')->count(),
            'manually_applied' => JobMatch::where('user_id', $user->id)->where('status', 'manually_applied')->count(),
            'avg_match' => JobMatch::where('user_id', $user->id)->avg('match_percentage'),
        ];

        // Meta Tags
        MetaTag::set('title', t('My Job Matches'));
        MetaTag::set('description', t('View your job matches on :appName', ['appName' => config('settings.app.name')]));

        return appView('account.job-matches.index', compact('matches', 'stats'));
    }

    /**
     * Display a specific job match
     *
     * @param int $id
     * @return \Illuminate\Contracts\View\View
     */
    public function show($id)
    {
        $match = JobMatch::where('user_id', auth()->id())
            ->with(['post.category', 'post.city', 'resume'])
            ->findOrFail($id);

        // Mark as viewed if not already
        if (!$match->viewed_at) {
            $match->update(['viewed_at' => now()]);
        }

        // Get user's resumes for application
        $resumes = Resume::where('user_id', auth()->id())->get();

        // Meta Tags
        MetaTag::set('title', $match->post->title . ' - Job Match');
        MetaTag::set('description', 'Job match details on ' . config('settings.app.name'));

        return appView('account.job-matches.show', compact('match', 'resumes'));
    }

    /**
     * Apply to a job manually
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function apply(Request $request, $id)
    {
        $match = JobMatch::where('user_id', auth()->id())->findOrFail($id);

        // Validate
        $validated = $request->validate([
            'resume_id' => 'required|exists:resumes,id',
            'cover_letter' => 'nullable|string|max:5000',
        ]);

        // Check if already applied
        if ($match->applied) {
            flash('You have already applied to this job.')->warning();
            return back();
        }

        try {
            // Get resume
            $resume = Resume::where('user_id', auth()->id())
                ->where('id', $validated['resume_id'])
                ->firstOrFail();

            // Apply using the service
            $result = $this->autoApplicationService->manualApply(
                $match,
                $resume->id,
                $validated['cover_letter'] ?? null
            );

            if ($result) {
                flash('Application submitted successfully! The employer has been notified.')->success();
            } else {
                flash('Failed to submit application. Please try again.')->error();
            }
        } catch (\Exception $e) {
            flash('Error submitting application: ' . $e->getMessage())->error();
        }

        return redirect()->route('job-matches.show', $match->id);
    }

    /**
     * Approve a match (save for later)
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function approve($id)
    {
        $match = JobMatch::where('user_id', auth()->id())->findOrFail($id);

        try {
            $match->update([
                'status' => 'approved',
                'approved_at' => now(),
            ]);

            flash('Job match saved for later. You can apply anytime from your matches list.')->success();
        } catch (\Exception $e) {
            flash('Error saving match: ' . $e->getMessage())->error();
        }

        return back();
    }

    /**
     * Reject a match
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function reject($id)
    {
        $match = JobMatch::where('user_id', auth()->id())->findOrFail($id);

        try {
            $match->update([
                'status' => 'rejected',
                'rejected_at' => now(),
            ]);

            flash('Job match rejected. We won\'t notify you about this job again.')->info();
        } catch (\Exception $e) {
            flash('Error rejecting match: ' . $e->getMessage())->error();
        }

        return redirect()->route('job-matches.index');
    }
}
