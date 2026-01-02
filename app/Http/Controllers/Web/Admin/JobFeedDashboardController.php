<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Web\Admin\Controller;
use App\Models\JobFeedSource;
use App\Models\JobFeedStagedItem;
use App\Models\JobFeedLog;
use Carbon\Carbon;

class JobFeedDashboardController extends Controller
{
    /**
     * Display the RSS Feed Dashboard
     */
    public function index()
    {
        // Summary stats
        $data = [
            'title' => 'RSS Feed Dashboard',

            // Source stats
            'totalSources' => JobFeedSource::count(),
            'activeSources' => JobFeedSource::where('status', 'active')->count(),
            'pausedSources' => JobFeedSource::where('status', 'paused')->count(),
            'failedSources' => JobFeedSource::where('status', 'failed')->count(),

            // Staged items stats
            'pendingItems' => JobFeedStagedItem::where('status', 'pending')->count(),
            'approvedItems' => JobFeedStagedItem::where('status', 'approved')->count(),
            'importedItems' => JobFeedStagedItem::where('status', 'imported')->count(),
            'importedToday' => JobFeedStagedItem::where('status', 'imported')
                ->whereDate('updated_at', today())->count(),

            // Recent logs
            'recentLogs' => JobFeedLog::with('feedSource')
                ->orderByDesc('created_at')
                ->limit(15)
                ->get(),

            // Top sources by imports
            'topSources' => JobFeedSource::orderByDesc('total_jobs_imported')
                ->limit(10)
                ->get(),

            // Sources by country
            'sourcesByCountry' => JobFeedSource::selectRaw('country_code, COUNT(*) as count')
                ->groupBy('country_code')
                ->orderByDesc('count')
                ->limit(10)
                ->get(),

            // Fetch activity (last 7 days)
            'fetchActivity' => $this->getFetchActivityData(),

            // Recent errors
            'recentErrors' => JobFeedLog::where('status', 'failed')
                ->with('feedSource')
                ->orderByDesc('created_at')
                ->limit(5)
                ->get(),
        ];

        return view('admin.job-feed.dashboard', $data);
    }

    /**
     * Get fetch activity data for the chart
     */
    protected function getFetchActivityData(): array
    {
        $data = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);

            $fetched = JobFeedLog::whereDate('created_at', $date)->sum('items_new');
            $imported = JobFeedStagedItem::where('status', 'imported')
                ->whereDate('updated_at', $date)->count();

            $data[] = [
                'date' => $date->format('M d'),
                'fetched' => $fetched,
                'imported' => $imported,
            ];
        }

        return $data;
    }
}
