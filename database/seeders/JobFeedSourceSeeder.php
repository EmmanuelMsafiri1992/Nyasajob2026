<?php

namespace Database\Seeders;

use App\Models\JobFeedSource;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class JobFeedSourceSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 * Seeds starter RSS feed sources for job aggregation.
	 * All sources are set to auto_approve = true for full auto import.
	 */
	public function run()
	{
		$entries = [
			// Remote/Global Jobs
			[
				'name' => 'RemoteOK',
				'feed_url' => 'https://remoteok.com/remote-jobs.rss',
				'country_code' => null,
				'category_id' => null,
				'post_type_id' => 1,
				'feed_format' => 'rss',
				'status' => 'active',
				'priority' => 8,
				'fetch_interval_minutes' => 360,
				'max_items_per_fetch' => 50,
				'rate_limit_delay_ms' => 2000,
				'auto_approve' => true,
				'notes' => 'Popular remote job board with global jobs',
			],
			[
				'name' => 'WeWorkRemotely',
				'feed_url' => 'https://weworkremotely.com/remote-jobs.rss',
				'country_code' => null,
				'category_id' => null,
				'post_type_id' => 1,
				'feed_format' => 'rss',
				'status' => 'active',
				'priority' => 8,
				'fetch_interval_minutes' => 360,
				'max_items_per_fetch' => 50,
				'rate_limit_delay_ms' => 2000,
				'auto_approve' => true,
				'notes' => 'Premium remote job board',
			],
			[
				'name' => 'Jobicy',
				'feed_url' => 'https://jobicy.com/feed/newjobs',
				'country_code' => null,
				'category_id' => null,
				'post_type_id' => 1,
				'feed_format' => 'rss',
				'status' => 'active',
				'priority' => 7,
				'fetch_interval_minutes' => 360,
				'max_items_per_fetch' => 50,
				'rate_limit_delay_ms' => 2000,
				'auto_approve' => true,
				'notes' => 'Remote job aggregator',
			],
			[
				'name' => 'Remote.co',
				'feed_url' => 'https://remote.co/remote-jobs/feed/',
				'country_code' => null,
				'category_id' => null,
				'post_type_id' => 1,
				'feed_format' => 'rss',
				'status' => 'active',
				'priority' => 7,
				'fetch_interval_minutes' => 360,
				'max_items_per_fetch' => 50,
				'rate_limit_delay_ms' => 2000,
				'auto_approve' => true,
				'notes' => 'Curated remote job listings',
			],
			[
				'name' => 'Working Nomads',
				'feed_url' => 'https://www.workingnomads.co/jobs.rss',
				'country_code' => null,
				'category_id' => null,
				'post_type_id' => 1,
				'feed_format' => 'rss',
				'status' => 'active',
				'priority' => 7,
				'fetch_interval_minutes' => 360,
				'max_items_per_fetch' => 50,
				'rate_limit_delay_ms' => 2000,
				'auto_approve' => true,
				'notes' => 'Jobs for digital nomads',
			],
			[
				'name' => 'Remotive',
				'feed_url' => 'https://remotive.com/remote-jobs/feed',
				'country_code' => null,
				'category_id' => null,
				'post_type_id' => 1,
				'feed_format' => 'rss',
				'status' => 'active',
				'priority' => 7,
				'fetch_interval_minutes' => 360,
				'max_items_per_fetch' => 50,
				'rate_limit_delay_ms' => 2000,
				'auto_approve' => true,
				'notes' => 'Remote jobs community',
			],

			// Tech Jobs
			[
				'name' => 'Authentic Jobs',
				'feed_url' => 'https://authenticjobs.com/rss/index.xml',
				'country_code' => null,
				'category_id' => null,
				'post_type_id' => 1,
				'feed_format' => 'rss',
				'status' => 'active',
				'priority' => 6,
				'fetch_interval_minutes' => 480,
				'max_items_per_fetch' => 30,
				'rate_limit_delay_ms' => 2000,
				'auto_approve' => true,
				'notes' => 'Design and tech jobs',
			],
			[
				'name' => 'Dribbble Jobs',
				'feed_url' => 'https://dribbble.com/jobs.rss',
				'country_code' => null,
				'category_id' => null,
				'post_type_id' => 1,
				'feed_format' => 'rss',
				'status' => 'active',
				'priority' => 6,
				'fetch_interval_minutes' => 480,
				'max_items_per_fetch' => 30,
				'rate_limit_delay_ms' => 2000,
				'auto_approve' => true,
				'notes' => 'Design jobs from Dribbble',
			],

			// Government/Public Portals
			[
				'name' => 'USAJobs',
				'feed_url' => 'https://www.usajobs.gov/Rss/?PostingChannel=2',
				'country_code' => 'US',
				'category_id' => null,
				'post_type_id' => 1,
				'feed_format' => 'rss',
				'status' => 'paused',
				'priority' => 5,
				'fetch_interval_minutes' => 720,
				'max_items_per_fetch' => 50,
				'rate_limit_delay_ms' => 3000,
				'auto_approve' => true,
				'notes' => 'US Government jobs - may require additional setup',
			],

			// Developer/Tech Specific
			[
				'name' => 'GitHub Jobs (Archive)',
				'feed_url' => 'https://github-job-archive.vercel.app/feed.xml',
				'country_code' => null,
				'category_id' => null,
				'post_type_id' => 1,
				'feed_format' => 'rss',
				'status' => 'testing',
				'priority' => 5,
				'fetch_interval_minutes' => 720,
				'max_items_per_fetch' => 30,
				'rate_limit_delay_ms' => 2000,
				'auto_approve' => true,
				'notes' => 'Archive of GitHub jobs - test before activating',
			],

			// Startup Jobs
			[
				'name' => 'Startup Jobs',
				'feed_url' => 'https://startup.jobs/feed',
				'country_code' => null,
				'category_id' => null,
				'post_type_id' => 1,
				'feed_format' => 'rss',
				'status' => 'testing',
				'priority' => 6,
				'fetch_interval_minutes' => 480,
				'max_items_per_fetch' => 40,
				'rate_limit_delay_ms' => 2000,
				'auto_approve' => true,
				'notes' => 'Jobs at startups worldwide',
			],

			// Africa-specific (for Malawi regional focus)
			[
				'name' => 'Careers24 SA',
				'feed_url' => 'https://www.careers24.com/rss/jobs/',
				'country_code' => 'ZA',
				'category_id' => null,
				'post_type_id' => 1,
				'feed_format' => 'rss',
				'status' => 'testing',
				'priority' => 7,
				'fetch_interval_minutes' => 360,
				'max_items_per_fetch' => 50,
				'rate_limit_delay_ms' => 2000,
				'auto_approve' => true,
				'notes' => 'South African job portal - test feed availability',
			],
		];

		$tableName = (new JobFeedSource())->getTable();

		foreach ($entries as $entry) {
			// Check if source already exists by feed_url
			$exists = DB::table($tableName)
				->where('feed_url', $entry['feed_url'])
				->exists();

			if (!$exists) {
				$entry['created_at'] = now();
				$entry['updated_at'] = now();
				DB::table($tableName)->insert($entry);
			}
		}

		$this->command->info('Job feed sources seeded successfully!');
	}
}
