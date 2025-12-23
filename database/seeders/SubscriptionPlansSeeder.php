<?php

namespace Database\Seeders;

use App\Models\SubscriptionPlan;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SubscriptionPlansSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $plans = [
            [
                'name' => 'Free',
                'slug' => 'free',
                'description' => 'Basic job search features',
                'price' => 0.00,
                'interval' => 'monthly',
                'interval_count' => 1,
                'features' => [
                    'browse_jobs',
                    'apply_manually',
                ],
                'is_active' => true,
                'is_popular' => false,
                'sort_order' => 1,
            ],
            [
                'name' => 'Basic',
                'slug' => 'basic',
                'description' => 'Essential features for active job seekers',
                'price' => 9.99,
                'interval' => 'monthly',
                'interval_count' => 1,
                'features' => [
                    'browse_jobs',
                    'apply_manually',
                    'job_preferences',
                    'job_matches',
                    'cv_builder',
                ],
                'is_active' => true,
                'is_popular' => false,
                'sort_order' => 2,
            ],
            [
                'name' => 'Professional',
                'slug' => 'professional',
                'description' => 'Advanced features with auto-apply and unlimited access',
                'price' => 19.99,
                'interval' => 'monthly',
                'interval_count' => 1,
                'features' => [
                    'browse_jobs',
                    'apply_manually',
                    'job_preferences',
                    'job_matches',
                    'cv_builder',
                    'auto_apply',
                    'unlimited_applications',
                    'priority_support',
                ],
                'is_active' => true,
                'is_popular' => true,
                'sort_order' => 3,
            ],
            [
                'name' => 'Premium',
                'slug' => 'premium',
                'description' => 'Complete package with courses and career development',
                'price' => 29.99,
                'interval' => 'monthly',
                'interval_count' => 1,
                'features' => [
                    'browse_jobs',
                    'apply_manually',
                    'job_preferences',
                    'job_matches',
                    'cv_builder',
                    'auto_apply',
                    'unlimited_applications',
                    'course_enrollment',
                    'career_coaching',
                    'resume_review',
                    'priority_support',
                    'exclusive_jobs',
                ],
                'is_active' => true,
                'is_popular' => false,
                'sort_order' => 4,
            ],
        ];

        foreach ($plans as $plan) {
            SubscriptionPlan::updateOrCreate(
                ['slug' => $plan['slug']],
                $plan
            );
        }
    }
}
