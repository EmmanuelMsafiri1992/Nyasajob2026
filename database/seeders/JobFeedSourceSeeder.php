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
			// ===========================================
			// WORKING GLOBAL REMOTE JOB BOARDS
			// These are the primary sources - verified working
			// ===========================================
			[
				'name' => 'RemoteOK',
				'feed_url' => 'https://remoteok.com/remote-jobs.rss',
				'country_code' => null,
				'category_id' => null,
				'post_type_id' => 1,
				'feed_format' => 'rss',
				'status' => 'active',
				'priority' => 10,
				'fetch_interval_minutes' => 180,
				'max_items_per_fetch' => 50,
				'rate_limit_delay_ms' => 2000,
				'auto_approve' => true,
				'notes' => 'Popular remote job board - VERIFIED WORKING',
			],
			[
				'name' => 'WeWorkRemotely',
				'feed_url' => 'https://weworkremotely.com/remote-jobs.rss',
				'country_code' => null,
				'category_id' => null,
				'post_type_id' => 1,
				'feed_format' => 'rss',
				'status' => 'active',
				'priority' => 10,
				'fetch_interval_minutes' => 180,
				'max_items_per_fetch' => 50,
				'rate_limit_delay_ms' => 2000,
				'auto_approve' => true,
				'notes' => 'Premium remote job board - VERIFIED WORKING',
			],
			[
				'name' => 'Remotive',
				'feed_url' => 'https://remotive.com/remote-jobs/feed',
				'country_code' => null,
				'category_id' => null,
				'post_type_id' => 1,
				'feed_format' => 'rss',
				'status' => 'active',
				'priority' => 9,
				'fetch_interval_minutes' => 240,
				'max_items_per_fetch' => 50,
				'rate_limit_delay_ms' => 2000,
				'auto_approve' => true,
				'notes' => 'Remote jobs community - VERIFIED WORKING',
			],
			[
				'name' => 'Jobicy',
				'feed_url' => 'https://jobicy.com/feed/newjobs',
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
				'notes' => 'Remote job aggregator - VERIFIED WORKING',
			],

			// ===========================================
			// TESTING - May work, needs verification
			// ===========================================
			[
				'name' => 'Dribbble Jobs',
				'feed_url' => 'https://dribbble.com/jobs.rss',
				'country_code' => null,
				'category_id' => null,
				'post_type_id' => 1,
				'feed_format' => 'rss',
				'status' => 'testing',
				'priority' => 6,
				'fetch_interval_minutes' => 480,
				'max_items_per_fetch' => 30,
				'rate_limit_delay_ms' => 2000,
				'auto_approve' => true,
				'notes' => 'Design jobs - test before activating',
			],

			// ===========================================
			// PAUSED - Known issues, don't activate
			// ===========================================
			[
				'name' => 'Remote.co',
				'feed_url' => 'https://remote.co/remote-jobs/feed/',
				'country_code' => null,
				'category_id' => null,
				'post_type_id' => 1,
				'feed_format' => 'rss',
				'status' => 'paused',
				'priority' => 5,
				'fetch_interval_minutes' => 720,
				'max_items_per_fetch' => 50,
				'rate_limit_delay_ms' => 2000,
				'auto_approve' => true,
				'notes' => 'PAUSED - Connection timeouts',
			],
			[
				'name' => 'Working Nomads',
				'feed_url' => 'https://www.workingnomads.co/jobs.rss',
				'country_code' => null,
				'category_id' => null,
				'post_type_id' => 1,
				'feed_format' => 'rss',
				'status' => 'paused',
				'priority' => 5,
				'fetch_interval_minutes' => 720,
				'max_items_per_fetch' => 50,
				'rate_limit_delay_ms' => 2000,
				'auto_approve' => true,
				'notes' => 'PAUSED - Returns 404',
			],
			[
				'name' => 'Authentic Jobs',
				'feed_url' => 'https://authenticjobs.com/rss/index.xml',
				'country_code' => null,
				'category_id' => null,
				'post_type_id' => 1,
				'feed_format' => 'rss',
				'status' => 'paused',
				'priority' => 5,
				'fetch_interval_minutes' => 720,
				'max_items_per_fetch' => 30,
				'rate_limit_delay_ms' => 2000,
				'auto_approve' => true,
				'notes' => 'PAUSED - Returns 404',
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
