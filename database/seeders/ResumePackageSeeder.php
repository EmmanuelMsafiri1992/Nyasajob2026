<?php

namespace Database\Seeders;

use App\Models\ResumePackage;
use Illuminate\Database\Seeder;

class ResumePackageSeeder extends Seeder
{
    public function run(): void
    {
        $packages = [
            [
                'name' => 'Starter',
                'description' => 'Perfect for trying out our candidate database.',
                'price' => 9.99,
                'currency_code' => 'USD',
                'credits' => 5,
                'validity_days' => 30,
                'unlimited_search' => true,
                'export_allowed' => false,
                'is_featured' => false,
                'sort_order' => 1,
                'active' => true,
            ],
            [
                'name' => 'Professional',
                'description' => 'Best value for active recruiters. Most popular choice.',
                'price' => 29.99,
                'currency_code' => 'USD',
                'credits' => 20,
                'validity_days' => 60,
                'unlimited_search' => true,
                'export_allowed' => true,
                'is_featured' => true,
                'sort_order' => 2,
                'active' => true,
            ],
            [
                'name' => 'Enterprise',
                'description' => 'For high-volume recruiting needs.',
                'price' => 79.99,
                'currency_code' => 'USD',
                'credits' => 100,
                'validity_days' => 90,
                'unlimited_search' => true,
                'export_allowed' => true,
                'is_featured' => false,
                'sort_order' => 3,
                'active' => true,
            ],
        ];

        foreach ($packages as $package) {
            ResumePackage::updateOrCreate(
                ['name' => $package['name']],
                $package
            );
        }

        $this->command->info('Resume packages seeded successfully!');
    }
}
