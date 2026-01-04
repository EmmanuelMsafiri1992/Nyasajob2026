<?php

namespace App\Console\Commands;

use App\Models\JobFeedSource;
use Illuminate\Console\Command;

class SeedJobFeedSources extends Command
{
    protected $signature = 'jobs:seed-sources
                            {--fresh : Delete existing sources before seeding}';

    protected $description = 'Seed comprehensive job feed sources for global coverage';

    public function handle(): int
    {
        if ($this->option('fresh')) {
            $this->warn('Deleting existing feed sources...');
            JobFeedSource::query()->delete();
        }

        $this->info('Seeding job feed sources...');

        $sources = $this->getSourceDefinitions();
        $created = 0;
        $skipped = 0;

        foreach ($sources as $source) {
            $exists = JobFeedSource::where('feed_url', $source['feed_url'])->exists();

            if ($exists) {
                $skipped++;
                continue;
            }

            JobFeedSource::create($source);
            $created++;
            $this->line("  + {$source['name']}");
        }

        $this->newLine();
        $this->info("Created: {$created} sources, Skipped: {$skipped} (already exist)");

        return Command::SUCCESS;
    }

    protected function getSourceDefinitions(): array
    {
        $sources = [];

        // ===========================================
        // GLOBAL REMOTE JOB RSS FEEDS (Free)
        // ===========================================

        $sources[] = [
            'name' => 'RemoteOK',
            'feed_url' => 'https://remoteok.com/remote-jobs.rss',
            'country_code' => null, // Global/Remote
            'feed_format' => 'rss',
            'status' => 'active',
            'priority' => 100,
            'max_items_per_fetch' => 50,
            'fetch_interval_minutes' => 360,
            'auto_approve' => true,
            'notes' => 'High quality remote jobs from RemoteOK',
        ];

        $sources[] = [
            'name' => 'WeWorkRemotely',
            'feed_url' => 'https://weworkremotely.com/remote-jobs.rss',
            'country_code' => null,
            'feed_format' => 'rss',
            'status' => 'active',
            'priority' => 95,
            'max_items_per_fetch' => 50,
            'fetch_interval_minutes' => 360,
            'auto_approve' => true,
            'notes' => 'Premium remote job board',
        ];

        $sources[] = [
            'name' => 'Remotive',
            'feed_url' => 'https://remotive.com/remote-jobs/feed',
            'country_code' => null,
            'feed_format' => 'rss',
            'status' => 'active',
            'priority' => 90,
            'max_items_per_fetch' => 50,
            'fetch_interval_minutes' => 360,
            'auto_approve' => true,
            'notes' => 'Curated remote job listings',
        ];

        $sources[] = [
            'name' => 'Jobicy',
            'feed_url' => 'https://jobicy.com/feed',
            'country_code' => null,
            'feed_format' => 'rss',
            'status' => 'active',
            'priority' => 85,
            'max_items_per_fetch' => 50,
            'fetch_interval_minutes' => 360,
            'auto_approve' => true,
            'notes' => 'Remote jobs worldwide',
        ];

        $sources[] = [
            'name' => 'Himalayas',
            'feed_url' => 'https://himalayas.app/jobs/rss',
            'country_code' => null,
            'feed_format' => 'rss',
            'status' => 'active',
            'priority' => 80,
            'max_items_per_fetch' => 50,
            'fetch_interval_minutes' => 360,
            'auto_approve' => true,
            'notes' => 'Remote jobs with company info',
        ];

        $sources[] = [
            'name' => 'EuroRemote',
            'feed_url' => 'https://euroremote.com/feed/',
            'country_code' => null,
            'feed_format' => 'rss',
            'status' => 'active',
            'priority' => 75,
            'max_items_per_fetch' => 30,
            'fetch_interval_minutes' => 720,
            'auto_approve' => true,
            'notes' => 'European remote jobs',
        ];

        $sources[] = [
            'name' => 'Crypto Jobs List',
            'feed_url' => 'https://cryptojobslist.com/rss',
            'country_code' => null,
            'feed_format' => 'rss',
            'status' => 'active',
            'priority' => 60,
            'max_items_per_fetch' => 30,
            'fetch_interval_minutes' => 720,
            'auto_approve' => false,
            'notes' => 'Blockchain and crypto jobs',
        ];

        $sources[] = [
            'name' => 'AI Jobs',
            'feed_url' => 'https://aijobs.net/feed/',
            'country_code' => null,
            'feed_format' => 'rss',
            'status' => 'active',
            'priority' => 70,
            'max_items_per_fetch' => 30,
            'fetch_interval_minutes' => 720,
            'auto_approve' => true,
            'notes' => 'AI and ML jobs',
        ];

        $sources[] = [
            'name' => 'FlexJobs Remote',
            'feed_url' => 'https://www.flexjobs.com/rss/jobs',
            'country_code' => null,
            'feed_format' => 'rss',
            'status' => 'active',
            'priority' => 80,
            'max_items_per_fetch' => 50,
            'fetch_interval_minutes' => 360,
            'auto_approve' => true,
            'notes' => 'Flexible and remote jobs',
        ];

        $sources[] = [
            'name' => 'JustRemote',
            'feed_url' => 'https://justremote.co/remote-jobs.rss',
            'country_code' => null,
            'feed_format' => 'rss',
            'status' => 'active',
            'priority' => 75,
            'max_items_per_fetch' => 40,
            'fetch_interval_minutes' => 480,
            'auto_approve' => true,
            'notes' => 'Remote jobs only',
        ];

        $sources[] = [
            'name' => 'Remote3',
            'feed_url' => 'https://remote3.co/remote-jobs.rss',
            'country_code' => null,
            'feed_format' => 'rss',
            'status' => 'active',
            'priority' => 65,
            'max_items_per_fetch' => 30,
            'fetch_interval_minutes' => 720,
            'auto_approve' => true,
            'notes' => 'Web3 and crypto remote jobs',
        ];

        $sources[] = [
            'name' => 'Nodesk',
            'feed_url' => 'https://nodesk.co/remote-jobs/rss/',
            'country_code' => null,
            'feed_format' => 'rss',
            'status' => 'active',
            'priority' => 70,
            'max_items_per_fetch' => 30,
            'fetch_interval_minutes' => 720,
            'auto_approve' => true,
            'notes' => 'Curated remote jobs',
        ];

        $sources[] = [
            'name' => 'Remote Leaf',
            'feed_url' => 'https://remoteleaf.com/rss',
            'country_code' => null,
            'feed_format' => 'rss',
            'status' => 'active',
            'priority' => 68,
            'max_items_per_fetch' => 30,
            'fetch_interval_minutes' => 720,
            'auto_approve' => true,
            'notes' => 'Hand-picked remote jobs',
        ];

        $sources[] = [
            'name' => 'Dynamite Jobs',
            'feed_url' => 'https://dynamitejobs.com/feed',
            'country_code' => null,
            'feed_format' => 'rss',
            'status' => 'active',
            'priority' => 72,
            'max_items_per_fetch' => 40,
            'fetch_interval_minutes' => 480,
            'auto_approve' => true,
            'notes' => 'Remote jobs for digital nomads',
        ];

        $sources[] = [
            'name' => 'Working Nomads',
            'feed_url' => 'https://www.workingnomads.com/jobs.rss',
            'country_code' => null,
            'feed_format' => 'rss',
            'status' => 'active',
            'priority' => 70,
            'max_items_per_fetch' => 30,
            'fetch_interval_minutes' => 720,
            'auto_approve' => true,
            'notes' => 'Jobs for digital nomads',
        ];

        $sources[] = [
            'name' => 'DailyRemote',
            'feed_url' => 'https://dailyremote.com/remote-jobs.rss',
            'country_code' => null,
            'feed_format' => 'rss',
            'status' => 'active',
            'priority' => 65,
            'max_items_per_fetch' => 30,
            'fetch_interval_minutes' => 720,
            'auto_approve' => true,
            'notes' => 'Daily remote job updates',
        ];

        $sources[] = [
            'name' => 'Dribbble Jobs',
            'feed_url' => 'https://dribbble.com/jobs.rss',
            'country_code' => null,
            'feed_format' => 'rss',
            'status' => 'active',
            'priority' => 60,
            'max_items_per_fetch' => 20,
            'fetch_interval_minutes' => 1440,
            'auto_approve' => true,
            'notes' => 'Design jobs from Dribbble',
        ];

        $sources[] = [
            'name' => 'Behance Jobs',
            'feed_url' => 'https://www.behance.net/joblist.rss',
            'country_code' => null,
            'feed_format' => 'rss',
            'status' => 'active',
            'priority' => 60,
            'max_items_per_fetch' => 20,
            'fetch_interval_minutes' => 1440,
            'auto_approve' => true,
            'notes' => 'Creative jobs from Behance',
        ];

        $sources[] = [
            'name' => 'AngelList',
            'feed_url' => 'https://angel.co/jobs/feed.rss',
            'country_code' => null,
            'feed_format' => 'rss',
            'status' => 'active',
            'priority' => 75,
            'max_items_per_fetch' => 50,
            'fetch_interval_minutes' => 480,
            'auto_approve' => true,
            'notes' => 'Startup jobs from AngelList',
        ];

        // ===========================================
        // ADZUNA API FEEDS (by Country)
        // ===========================================
        $adzunaCountries = [
            'ZA' => 'South Africa',
            'US' => 'United States',
            'GB' => 'United Kingdom',
            'AU' => 'Australia',
            'CA' => 'Canada',
            'DE' => 'Germany',
            'FR' => 'France',
            'IN' => 'India',
            'NL' => 'Netherlands',
            'NZ' => 'New Zealand',
            'PL' => 'Poland',
            'SG' => 'Singapore',
            'BR' => 'Brazil',
            'AT' => 'Austria',
            'IT' => 'Italy',
        ];

        foreach ($adzunaCountries as $code => $name) {
            $sources[] = [
                'name' => "Adzuna {$name}",
                'feed_url' => "https://api.adzuna.com/v1/api/jobs/{$code}/search",
                'country_code' => $code,
                'feed_format' => 'api_adzuna',
                'status' => 'active',
                'priority' => 80,
                'max_items_per_fetch' => 100,
                'fetch_interval_minutes' => 240,
                'auto_approve' => true,
                'notes' => "Adzuna jobs for {$name}",
            ];
        }

        // ===========================================
        // JOOBLE API FEEDS (by Country)
        // ===========================================
        $joobleCountries = [
            'ZA' => 'South Africa',
            'NG' => 'Nigeria',
            'KE' => 'Kenya',
            'GH' => 'Ghana',
            'EG' => 'Egypt',
            'MA' => 'Morocco',
            'TZ' => 'Tanzania',
            'UG' => 'Uganda',
            'ZM' => 'Zambia',
            'ZW' => 'Zimbabwe',
            'BW' => 'Botswana',
            'NA' => 'Namibia',
            'MZ' => 'Mozambique',
            'SN' => 'Senegal',
            'CI' => 'Ivory Coast',
            'CM' => 'Cameroon',
            'RW' => 'Rwanda',
            'ET' => 'Ethiopia',
            'AO' => 'Angola',
            'MW' => 'Malawi',
            'PH' => 'Philippines',
            'ID' => 'Indonesia',
            'MY' => 'Malaysia',
            'TH' => 'Thailand',
            'VN' => 'Vietnam',
            'PK' => 'Pakistan',
            'BD' => 'Bangladesh',
            'AE' => 'UAE',
            'SA' => 'Saudi Arabia',
            'QA' => 'Qatar',
            'MX' => 'Mexico',
            'AR' => 'Argentina',
            'CL' => 'Chile',
            'CO' => 'Colombia',
            'PE' => 'Peru',
            'UA' => 'Ukraine',
            'RO' => 'Romania',
            'CZ' => 'Czech Republic',
            'HU' => 'Hungary',
            'TR' => 'Turkey',
            'IE' => 'Ireland',
            'PT' => 'Portugal',
            'SE' => 'Sweden',
            'NO' => 'Norway',
            'DK' => 'Denmark',
            'FI' => 'Finland',
            'CH' => 'Switzerland',
            'BE' => 'Belgium',
            'IL' => 'Israel',
            'JP' => 'Japan',
            'KR' => 'South Korea',
        ];

        foreach ($joobleCountries as $code => $name) {
            $sources[] = [
                'name' => "Jooble {$name}",
                'feed_url' => "https://jooble.org/api/{$code}",
                'country_code' => $code,
                'feed_format' => 'api_jooble',
                'status' => 'active',
                'priority' => 70,
                'max_items_per_fetch' => 100,
                'fetch_interval_minutes' => 360,
                'auto_approve' => true,
                'notes' => "Jooble jobs for {$name}",
            ];
        }

        // ===========================================
        // CAREERJET API FEEDS (by Country)
        // ===========================================
        $careerjetCountries = [
            'ZA' => 'South Africa',
            'NG' => 'Nigeria',
            'KE' => 'Kenya',
            'EG' => 'Egypt',
            'MA' => 'Morocco',
        ];

        foreach ($careerjetCountries as $code => $name) {
            $sources[] = [
                'name' => "Careerjet {$name}",
                'feed_url' => "https://www.careerjet.com/search/jobs?l={$code}",
                'country_code' => $code,
                'feed_format' => 'api_careerjet',
                'status' => 'active',
                'priority' => 65,
                'max_items_per_fetch' => 50,
                'fetch_interval_minutes' => 480,
                'auto_approve' => true,
                'notes' => "Careerjet jobs for {$name}",
            ];
        }

        return $sources;
    }
}
