<?php

namespace Database\Seeders;

use App\Models\WorkerSkill;
use Illuminate\Database\Seeder;

class WorkerSkillSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $skills = [
            // Domestic/Household
            [
                'name' => 'House Cleaning',
                'icon' => 'fa-solid fa-broom',
                'description' => 'General house cleaning, dusting, mopping, and organizing',
                'lft' => 1,
                'rgt' => 2,
                'depth' => 0,
            ],
            [
                'name' => 'Laundry & Ironing',
                'icon' => 'fa-solid fa-shirt',
                'description' => 'Washing, drying, ironing, and folding clothes',
                'lft' => 3,
                'rgt' => 4,
                'depth' => 0,
            ],
            [
                'name' => 'Cooking',
                'icon' => 'fa-solid fa-utensils',
                'description' => 'Meal preparation and cooking',
                'lft' => 5,
                'rgt' => 6,
                'depth' => 0,
            ],
            [
                'name' => 'Child Care',
                'icon' => 'fa-solid fa-baby',
                'description' => 'Looking after children, babysitting',
                'lft' => 7,
                'rgt' => 8,
                'depth' => 0,
            ],
            [
                'name' => 'Elder Care',
                'icon' => 'fa-solid fa-person-cane',
                'description' => 'Caring for elderly persons',
                'lft' => 9,
                'rgt' => 10,
                'depth' => 0,
            ],
            // Outdoor/Manual
            [
                'name' => 'Gardening',
                'icon' => 'fa-solid fa-seedling',
                'description' => 'Garden maintenance, planting, and landscaping',
                'lft' => 11,
                'rgt' => 12,
                'depth' => 0,
            ],
            [
                'name' => 'Security Guard',
                'icon' => 'fa-solid fa-shield-halved',
                'description' => 'Security and watchman services',
                'lft' => 13,
                'rgt' => 14,
                'depth' => 0,
            ],
            [
                'name' => 'Driving',
                'icon' => 'fa-solid fa-car',
                'description' => 'Personal or commercial driving',
                'lft' => 15,
                'rgt' => 16,
                'depth' => 0,
            ],
            [
                'name' => 'Farm Work',
                'icon' => 'fa-solid fa-tractor',
                'description' => 'Agricultural and farm labor',
                'lft' => 17,
                'rgt' => 18,
                'depth' => 0,
            ],
            [
                'name' => 'Construction Labor',
                'icon' => 'fa-solid fa-hammer',
                'description' => 'General construction and building work',
                'lft' => 19,
                'rgt' => 20,
                'depth' => 0,
            ],
            // Skilled Trades (Basic)
            [
                'name' => 'Painting',
                'icon' => 'fa-solid fa-paint-roller',
                'description' => 'House painting and decorating',
                'lft' => 21,
                'rgt' => 22,
                'depth' => 0,
            ],
            [
                'name' => 'Plumbing (Basic)',
                'icon' => 'fa-solid fa-wrench',
                'description' => 'Basic plumbing repairs and maintenance',
                'lft' => 23,
                'rgt' => 24,
                'depth' => 0,
            ],
            [
                'name' => 'Electrical (Basic)',
                'icon' => 'fa-solid fa-bolt',
                'description' => 'Basic electrical work and repairs',
                'lft' => 25,
                'rgt' => 26,
                'depth' => 0,
            ],
            [
                'name' => 'Carpentry',
                'icon' => 'fa-solid fa-screwdriver',
                'description' => 'Basic woodwork and furniture repairs',
                'lft' => 27,
                'rgt' => 28,
                'depth' => 0,
            ],
            // Services
            [
                'name' => 'Delivery/Errands',
                'icon' => 'fa-solid fa-truck',
                'description' => 'Running errands and delivery services',
                'lft' => 29,
                'rgt' => 30,
                'depth' => 0,
            ],
            [
                'name' => 'Catering Assistant',
                'icon' => 'fa-solid fa-champagne-glasses',
                'description' => 'Helping with catering and events',
                'lft' => 31,
                'rgt' => 32,
                'depth' => 0,
            ],
            [
                'name' => 'Pet Care',
                'icon' => 'fa-solid fa-dog',
                'description' => 'Looking after pets and animals',
                'lft' => 33,
                'rgt' => 34,
                'depth' => 0,
            ],
            [
                'name' => 'Tailoring/Sewing',
                'icon' => 'fa-solid fa-scissors',
                'description' => 'Basic sewing and clothing repairs',
                'lft' => 35,
                'rgt' => 36,
                'depth' => 0,
            ],
            [
                'name' => 'Hair Styling',
                'icon' => 'fa-solid fa-spray-can-sparkles',
                'description' => 'Hair cutting and styling',
                'lft' => 37,
                'rgt' => 38,
                'depth' => 0,
            ],
            [
                'name' => 'General Helper',
                'icon' => 'fa-solid fa-hands-helping',
                'description' => 'General assistance and odd jobs',
                'lft' => 39,
                'rgt' => 40,
                'depth' => 0,
            ],
        ];

        foreach ($skills as $skill) {
            WorkerSkill::updateOrCreate(
                ['name' => $skill['name']],
                array_merge($skill, ['active' => true])
            );
        }
    }
}
