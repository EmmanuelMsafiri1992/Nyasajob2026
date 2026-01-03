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
			// ADZUNA API SOURCES (16 countries)
			// Get API key at: https://developer.adzuna.com/
			// ===========================================
			['name' => 'Adzuna UK', 'feed_url' => 'api://adzuna/gb', 'country_code' => 'GB', 'category_id' => null, 'post_type_id' => 1, 'feed_format' => 'api_adzuna', 'status' => 'testing', 'priority' => 8, 'fetch_interval_minutes' => 360, 'max_items_per_fetch' => 50, 'rate_limit_delay_ms' => 3000, 'auto_approve' => true, 'notes' => 'Adzuna UK - requires API key'],
			['name' => 'Adzuna US', 'feed_url' => 'api://adzuna/us', 'country_code' => 'US', 'category_id' => null, 'post_type_id' => 1, 'feed_format' => 'api_adzuna', 'status' => 'testing', 'priority' => 8, 'fetch_interval_minutes' => 360, 'max_items_per_fetch' => 50, 'rate_limit_delay_ms' => 3000, 'auto_approve' => true, 'notes' => 'Adzuna US - requires API key'],
			['name' => 'Adzuna Australia', 'feed_url' => 'api://adzuna/au', 'country_code' => 'AU', 'category_id' => null, 'post_type_id' => 1, 'feed_format' => 'api_adzuna', 'status' => 'testing', 'priority' => 7, 'fetch_interval_minutes' => 360, 'max_items_per_fetch' => 50, 'rate_limit_delay_ms' => 3000, 'auto_approve' => true, 'notes' => 'Adzuna Australia - requires API key'],
			['name' => 'Adzuna Germany', 'feed_url' => 'api://adzuna/de', 'country_code' => 'DE', 'category_id' => null, 'post_type_id' => 1, 'feed_format' => 'api_adzuna', 'status' => 'testing', 'priority' => 7, 'fetch_interval_minutes' => 360, 'max_items_per_fetch' => 50, 'rate_limit_delay_ms' => 3000, 'auto_approve' => true, 'notes' => 'Adzuna Germany - requires API key'],
			['name' => 'Adzuna France', 'feed_url' => 'api://adzuna/fr', 'country_code' => 'FR', 'category_id' => null, 'post_type_id' => 1, 'feed_format' => 'api_adzuna', 'status' => 'testing', 'priority' => 7, 'fetch_interval_minutes' => 360, 'max_items_per_fetch' => 50, 'rate_limit_delay_ms' => 3000, 'auto_approve' => true, 'notes' => 'Adzuna France - requires API key'],
			['name' => 'Adzuna India', 'feed_url' => 'api://adzuna/in', 'country_code' => 'IN', 'category_id' => null, 'post_type_id' => 1, 'feed_format' => 'api_adzuna', 'status' => 'testing', 'priority' => 7, 'fetch_interval_minutes' => 360, 'max_items_per_fetch' => 50, 'rate_limit_delay_ms' => 3000, 'auto_approve' => true, 'notes' => 'Adzuna India - requires API key'],
			['name' => 'Adzuna South Africa', 'feed_url' => 'api://adzuna/za', 'country_code' => 'ZA', 'category_id' => null, 'post_type_id' => 1, 'feed_format' => 'api_adzuna', 'status' => 'testing', 'priority' => 8, 'fetch_interval_minutes' => 360, 'max_items_per_fetch' => 50, 'rate_limit_delay_ms' => 3000, 'auto_approve' => true, 'notes' => 'Adzuna South Africa - requires API key'],
			['name' => 'Adzuna Canada', 'feed_url' => 'api://adzuna/ca', 'country_code' => 'CA', 'category_id' => null, 'post_type_id' => 1, 'feed_format' => 'api_adzuna', 'status' => 'testing', 'priority' => 7, 'fetch_interval_minutes' => 360, 'max_items_per_fetch' => 50, 'rate_limit_delay_ms' => 3000, 'auto_approve' => true, 'notes' => 'Adzuna Canada - requires API key'],
			['name' => 'Adzuna Netherlands', 'feed_url' => 'api://adzuna/nl', 'country_code' => 'NL', 'category_id' => null, 'post_type_id' => 1, 'feed_format' => 'api_adzuna', 'status' => 'testing', 'priority' => 6, 'fetch_interval_minutes' => 480, 'max_items_per_fetch' => 50, 'rate_limit_delay_ms' => 3000, 'auto_approve' => true, 'notes' => 'Adzuna Netherlands - requires API key'],
			['name' => 'Adzuna Brazil', 'feed_url' => 'api://adzuna/br', 'country_code' => 'BR', 'category_id' => null, 'post_type_id' => 1, 'feed_format' => 'api_adzuna', 'status' => 'testing', 'priority' => 6, 'fetch_interval_minutes' => 480, 'max_items_per_fetch' => 50, 'rate_limit_delay_ms' => 3000, 'auto_approve' => true, 'notes' => 'Adzuna Brazil - requires API key'],
			['name' => 'Adzuna Poland', 'feed_url' => 'api://adzuna/pl', 'country_code' => 'PL', 'category_id' => null, 'post_type_id' => 1, 'feed_format' => 'api_adzuna', 'status' => 'testing', 'priority' => 6, 'fetch_interval_minutes' => 480, 'max_items_per_fetch' => 50, 'rate_limit_delay_ms' => 3000, 'auto_approve' => true, 'notes' => 'Adzuna Poland - requires API key'],
			['name' => 'Adzuna Singapore', 'feed_url' => 'api://adzuna/sg', 'country_code' => 'SG', 'category_id' => null, 'post_type_id' => 1, 'feed_format' => 'api_adzuna', 'status' => 'testing', 'priority' => 6, 'fetch_interval_minutes' => 480, 'max_items_per_fetch' => 50, 'rate_limit_delay_ms' => 3000, 'auto_approve' => true, 'notes' => 'Adzuna Singapore - requires API key'],

			// ===========================================
			// JOOBLE API SOURCES (71 countries)
			// Get API key at: https://jooble.org/api/about
			// ===========================================
			['name' => 'Jooble Nigeria', 'feed_url' => 'api://jooble/ng', 'country_code' => 'NG', 'category_id' => null, 'post_type_id' => 1, 'feed_format' => 'api_jooble', 'status' => 'testing', 'priority' => 8, 'fetch_interval_minutes' => 360, 'max_items_per_fetch' => 30, 'rate_limit_delay_ms' => 5000, 'auto_approve' => true, 'notes' => 'Jooble Nigeria - requires API key'],
			['name' => 'Jooble Kenya', 'feed_url' => 'api://jooble/ke', 'country_code' => 'KE', 'category_id' => null, 'post_type_id' => 1, 'feed_format' => 'api_jooble', 'status' => 'testing', 'priority' => 8, 'fetch_interval_minutes' => 360, 'max_items_per_fetch' => 30, 'rate_limit_delay_ms' => 5000, 'auto_approve' => true, 'notes' => 'Jooble Kenya - requires API key'],
			['name' => 'Jooble Ghana', 'feed_url' => 'api://jooble/gh', 'country_code' => 'GH', 'category_id' => null, 'post_type_id' => 1, 'feed_format' => 'api_jooble', 'status' => 'testing', 'priority' => 8, 'fetch_interval_minutes' => 360, 'max_items_per_fetch' => 30, 'rate_limit_delay_ms' => 5000, 'auto_approve' => true, 'notes' => 'Jooble Ghana - requires API key'],
			['name' => 'Jooble Egypt', 'feed_url' => 'api://jooble/eg', 'country_code' => 'EG', 'category_id' => null, 'post_type_id' => 1, 'feed_format' => 'api_jooble', 'status' => 'testing', 'priority' => 7, 'fetch_interval_minutes' => 360, 'max_items_per_fetch' => 30, 'rate_limit_delay_ms' => 5000, 'auto_approve' => true, 'notes' => 'Jooble Egypt - requires API key'],
			['name' => 'Jooble Tanzania', 'feed_url' => 'api://jooble/tz', 'country_code' => 'TZ', 'category_id' => null, 'post_type_id' => 1, 'feed_format' => 'api_jooble', 'status' => 'testing', 'priority' => 7, 'fetch_interval_minutes' => 480, 'max_items_per_fetch' => 30, 'rate_limit_delay_ms' => 5000, 'auto_approve' => true, 'notes' => 'Jooble Tanzania - requires API key'],
			['name' => 'Jooble Uganda', 'feed_url' => 'api://jooble/ug', 'country_code' => 'UG', 'category_id' => null, 'post_type_id' => 1, 'feed_format' => 'api_jooble', 'status' => 'testing', 'priority' => 7, 'fetch_interval_minutes' => 480, 'max_items_per_fetch' => 30, 'rate_limit_delay_ms' => 5000, 'auto_approve' => true, 'notes' => 'Jooble Uganda - requires API key'],
			['name' => 'Jooble Zambia', 'feed_url' => 'api://jooble/zm', 'country_code' => 'ZM', 'category_id' => null, 'post_type_id' => 1, 'feed_format' => 'api_jooble', 'status' => 'testing', 'priority' => 7, 'fetch_interval_minutes' => 480, 'max_items_per_fetch' => 30, 'rate_limit_delay_ms' => 5000, 'auto_approve' => true, 'notes' => 'Jooble Zambia - requires API key'],
			['name' => 'Jooble Zimbabwe', 'feed_url' => 'api://jooble/zw', 'country_code' => 'ZW', 'category_id' => null, 'post_type_id' => 1, 'feed_format' => 'api_jooble', 'status' => 'testing', 'priority' => 7, 'fetch_interval_minutes' => 480, 'max_items_per_fetch' => 30, 'rate_limit_delay_ms' => 5000, 'auto_approve' => true, 'notes' => 'Jooble Zimbabwe - requires API key'],
			['name' => 'Jooble Malawi', 'feed_url' => 'api://jooble/mw', 'country_code' => 'MW', 'category_id' => null, 'post_type_id' => 1, 'feed_format' => 'api_jooble', 'status' => 'testing', 'priority' => 9, 'fetch_interval_minutes' => 360, 'max_items_per_fetch' => 30, 'rate_limit_delay_ms' => 5000, 'auto_approve' => true, 'notes' => 'Jooble Malawi - HIGH PRIORITY - requires API key'],
			['name' => 'Jooble Rwanda', 'feed_url' => 'api://jooble/rw', 'country_code' => 'RW', 'category_id' => null, 'post_type_id' => 1, 'feed_format' => 'api_jooble', 'status' => 'testing', 'priority' => 7, 'fetch_interval_minutes' => 480, 'max_items_per_fetch' => 30, 'rate_limit_delay_ms' => 5000, 'auto_approve' => true, 'notes' => 'Jooble Rwanda - requires API key'],
			['name' => 'Jooble UAE', 'feed_url' => 'api://jooble/ae', 'country_code' => 'AE', 'category_id' => null, 'post_type_id' => 1, 'feed_format' => 'api_jooble', 'status' => 'testing', 'priority' => 7, 'fetch_interval_minutes' => 360, 'max_items_per_fetch' => 30, 'rate_limit_delay_ms' => 5000, 'auto_approve' => true, 'notes' => 'Jooble UAE - requires API key'],
			['name' => 'Jooble Pakistan', 'feed_url' => 'api://jooble/pk', 'country_code' => 'PK', 'category_id' => null, 'post_type_id' => 1, 'feed_format' => 'api_jooble', 'status' => 'testing', 'priority' => 7, 'fetch_interval_minutes' => 360, 'max_items_per_fetch' => 30, 'rate_limit_delay_ms' => 5000, 'auto_approve' => true, 'notes' => 'Jooble Pakistan - requires API key'],
			['name' => 'Jooble Philippines', 'feed_url' => 'api://jooble/ph', 'country_code' => 'PH', 'category_id' => null, 'post_type_id' => 1, 'feed_format' => 'api_jooble', 'status' => 'testing', 'priority' => 7, 'fetch_interval_minutes' => 360, 'max_items_per_fetch' => 30, 'rate_limit_delay_ms' => 5000, 'auto_approve' => true, 'notes' => 'Jooble Philippines - requires API key'],
			['name' => 'Jooble Indonesia', 'feed_url' => 'api://jooble/id', 'country_code' => 'ID', 'category_id' => null, 'post_type_id' => 1, 'feed_format' => 'api_jooble', 'status' => 'testing', 'priority' => 7, 'fetch_interval_minutes' => 360, 'max_items_per_fetch' => 30, 'rate_limit_delay_ms' => 5000, 'auto_approve' => true, 'notes' => 'Jooble Indonesia - requires API key'],

			// ===========================================
			// CAREERJET API SOURCES (90+ countries)
			// Get affiliate ID at: https://www.careerjet.com/partners/
			// ===========================================
			['name' => 'Careerjet South Africa', 'feed_url' => 'api://careerjet/za', 'country_code' => 'ZA', 'category_id' => null, 'post_type_id' => 1, 'feed_format' => 'api_careerjet', 'status' => 'testing', 'priority' => 7, 'fetch_interval_minutes' => 480, 'max_items_per_fetch' => 30, 'rate_limit_delay_ms' => 5000, 'auto_approve' => true, 'notes' => 'Careerjet South Africa - requires affiliate ID'],
			['name' => 'Careerjet Nigeria', 'feed_url' => 'api://careerjet/ng', 'country_code' => 'NG', 'category_id' => null, 'post_type_id' => 1, 'feed_format' => 'api_careerjet', 'status' => 'testing', 'priority' => 7, 'fetch_interval_minutes' => 480, 'max_items_per_fetch' => 30, 'rate_limit_delay_ms' => 5000, 'auto_approve' => true, 'notes' => 'Careerjet Nigeria - requires affiliate ID'],
			['name' => 'Careerjet Kenya', 'feed_url' => 'api://careerjet/ke', 'country_code' => 'KE', 'category_id' => null, 'post_type_id' => 1, 'feed_format' => 'api_careerjet', 'status' => 'testing', 'priority' => 7, 'fetch_interval_minutes' => 480, 'max_items_per_fetch' => 30, 'rate_limit_delay_ms' => 5000, 'auto_approve' => true, 'notes' => 'Careerjet Kenya - requires affiliate ID'],
			['name' => 'Careerjet Egypt', 'feed_url' => 'api://careerjet/eg', 'country_code' => 'EG', 'category_id' => null, 'post_type_id' => 1, 'feed_format' => 'api_careerjet', 'status' => 'testing', 'priority' => 6, 'fetch_interval_minutes' => 480, 'max_items_per_fetch' => 30, 'rate_limit_delay_ms' => 5000, 'auto_approve' => true, 'notes' => 'Careerjet Egypt - requires affiliate ID'],
			['name' => 'Careerjet Morocco', 'feed_url' => 'api://careerjet/ma', 'country_code' => 'MA', 'category_id' => null, 'post_type_id' => 1, 'feed_format' => 'api_careerjet', 'status' => 'testing', 'priority' => 6, 'fetch_interval_minutes' => 480, 'max_items_per_fetch' => 30, 'rate_limit_delay_ms' => 5000, 'auto_approve' => true, 'notes' => 'Careerjet Morocco - requires affiliate ID'],
			['name' => 'Careerjet UK', 'feed_url' => 'api://careerjet/gb', 'country_code' => 'GB', 'category_id' => null, 'post_type_id' => 1, 'feed_format' => 'api_careerjet', 'status' => 'testing', 'priority' => 6, 'fetch_interval_minutes' => 480, 'max_items_per_fetch' => 30, 'rate_limit_delay_ms' => 5000, 'auto_approve' => true, 'notes' => 'Careerjet UK - requires affiliate ID'],
			['name' => 'Careerjet India', 'feed_url' => 'api://careerjet/in', 'country_code' => 'IN', 'category_id' => null, 'post_type_id' => 1, 'feed_format' => 'api_careerjet', 'status' => 'testing', 'priority' => 6, 'fetch_interval_minutes' => 480, 'max_items_per_fetch' => 30, 'rate_limit_delay_ms' => 5000, 'auto_approve' => true, 'notes' => 'Careerjet India - requires affiliate ID'],
			['name' => 'Careerjet Pakistan', 'feed_url' => 'api://careerjet/pk', 'country_code' => 'PK', 'category_id' => null, 'post_type_id' => 1, 'feed_format' => 'api_careerjet', 'status' => 'testing', 'priority' => 6, 'fetch_interval_minutes' => 480, 'max_items_per_fetch' => 30, 'rate_limit_delay_ms' => 5000, 'auto_approve' => true, 'notes' => 'Careerjet Pakistan - requires affiliate ID'],
			['name' => 'Careerjet Bangladesh', 'feed_url' => 'api://careerjet/bd', 'country_code' => 'BD', 'category_id' => null, 'post_type_id' => 1, 'feed_format' => 'api_careerjet', 'status' => 'testing', 'priority' => 6, 'fetch_interval_minutes' => 480, 'max_items_per_fetch' => 30, 'rate_limit_delay_ms' => 5000, 'auto_approve' => true, 'notes' => 'Careerjet Bangladesh - requires affiliate ID'],
			['name' => 'Careerjet UAE', 'feed_url' => 'api://careerjet/ae', 'country_code' => 'AE', 'category_id' => null, 'post_type_id' => 1, 'feed_format' => 'api_careerjet', 'status' => 'testing', 'priority' => 6, 'fetch_interval_minutes' => 480, 'max_items_per_fetch' => 30, 'rate_limit_delay_ms' => 5000, 'auto_approve' => true, 'notes' => 'Careerjet UAE - requires affiliate ID'],

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
