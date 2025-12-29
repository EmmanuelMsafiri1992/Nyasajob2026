<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UnskilledWorkersSectionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get the maximum lft value to position this section after existing sections
        $maxLft = DB::table('home_sections')->max('lft') ?? 0;

        // Check if section already exists
        $exists = DB::table('home_sections')
            ->where('method', 'getUnskilledWorkers')
            ->exists();

        if (!$exists) {
            DB::table('home_sections')->insert([
                'method' => 'getUnskilledWorkers',
                'name' => 'Unskilled Workers',
                'value' => json_encode([
                    'max_jobs' => '6',
                    'max_profiles' => '6',
                    'jobs_title' => 'Unskilled Labor Jobs',
                    'profiles_title' => 'Available Workers',
                    'unskilled_category_id' => null,
                    'cache_expiration' => '3600',
                    'hide_on_mobile' => '0',
                ]),
                'view' => 'home.inc.unskilled-workers',
                'field' => null,
                'parent_id' => null,
                'lft' => $maxLft + 1,
                'rgt' => $maxLft + 2,
                'depth' => 0,
                'active' => 1,
            ]);

            $this->command->info('Unskilled Workers home section added successfully.');
        } else {
            $this->command->info('Unskilled Workers home section already exists.');
        }
    }
}
