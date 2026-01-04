<?php

namespace Database\Seeders;

use App\Models\QuizQuestion;
use App\Models\QuizResult;
use Illuminate\Database\Seeder;

class QuizSeeder extends Seeder
{
    public function run(): void
    {
        // Clear existing data
        QuizQuestion::truncate();
        QuizResult::truncate();

        // Seed Questions
        $questions = [
            [
                'question' => 'How do you prefer to work?',
                'options' => [
                    ['text' => 'Independently, setting my own pace', 'scores' => ['creative' => 2, 'analytical' => 1]],
                    ['text' => 'In a team, collaborating with others', 'scores' => ['leader' => 1, 'helper' => 2]],
                    ['text' => 'Leading and guiding others', 'scores' => ['leader' => 3]],
                    ['text' => 'Supporting and helping others succeed', 'scores' => ['helper' => 3]],
                ],
                'category' => 'work-style',
                'order' => 1,
                'active' => true,
            ],
            [
                'question' => 'What type of tasks do you enjoy most?',
                'options' => [
                    ['text' => 'Solving complex problems', 'scores' => ['analytical' => 3, 'technical' => 1]],
                    ['text' => 'Creating new ideas or designs', 'scores' => ['creative' => 3]],
                    ['text' => 'Helping people directly', 'scores' => ['helper' => 3]],
                    ['text' => 'Organizing and planning', 'scores' => ['organizer' => 3]],
                ],
                'category' => 'interests',
                'order' => 2,
                'active' => true,
            ],
            [
                'question' => 'What environment do you thrive in?',
                'options' => [
                    ['text' => 'Fast-paced and dynamic', 'scores' => ['leader' => 2, 'creative' => 1]],
                    ['text' => 'Structured and predictable', 'scores' => ['organizer' => 2, 'analytical' => 1]],
                    ['text' => 'Flexible and remote-friendly', 'scores' => ['technical' => 2, 'creative' => 1]],
                    ['text' => 'People-focused and social', 'scores' => ['helper' => 2, 'leader' => 1]],
                ],
                'category' => 'work-style',
                'order' => 3,
                'active' => true,
            ],
            [
                'question' => 'What motivates you at work?',
                'options' => [
                    ['text' => 'Financial rewards and career growth', 'scores' => ['leader' => 2, 'analytical' => 1]],
                    ['text' => 'Making a positive impact', 'scores' => ['helper' => 3]],
                    ['text' => 'Learning new skills', 'scores' => ['technical' => 2, 'analytical' => 1]],
                    ['text' => 'Creative freedom and expression', 'scores' => ['creative' => 3]],
                ],
                'category' => 'personality',
                'order' => 4,
                'active' => true,
            ],
            [
                'question' => 'How do you handle challenges?',
                'options' => [
                    ['text' => 'Analyze data and find logical solutions', 'scores' => ['analytical' => 3]],
                    ['text' => 'Think creatively and try new approaches', 'scores' => ['creative' => 3]],
                    ['text' => 'Seek advice and collaborate', 'scores' => ['helper' => 2, 'leader' => 1]],
                    ['text' => 'Follow established processes', 'scores' => ['organizer' => 3]],
                ],
                'category' => 'skills',
                'order' => 5,
                'active' => true,
            ],
            [
                'question' => 'What describes your communication style?',
                'options' => [
                    ['text' => 'Direct and to the point', 'scores' => ['leader' => 2, 'analytical' => 1]],
                    ['text' => 'Warm and empathetic', 'scores' => ['helper' => 3]],
                    ['text' => 'Creative and expressive', 'scores' => ['creative' => 3]],
                    ['text' => 'Clear and organized', 'scores' => ['organizer' => 2, 'technical' => 1]],
                ],
                'category' => 'personality',
                'order' => 6,
                'active' => true,
            ],
            [
                'question' => 'What would be your ideal project?',
                'options' => [
                    ['text' => 'Launching a new business venture', 'scores' => ['leader' => 3, 'creative' => 1]],
                    ['text' => 'Building a complex technical solution', 'scores' => ['technical' => 3, 'analytical' => 1]],
                    ['text' => 'Organizing a community event', 'scores' => ['helper' => 2, 'organizer' => 2]],
                    ['text' => 'Designing a creative campaign', 'scores' => ['creative' => 3]],
                ],
                'category' => 'interests',
                'order' => 7,
                'active' => true,
            ],
        ];

        foreach ($questions as $q) {
            QuizQuestion::create($q);
        }

        // Seed Results
        $results = [
            [
                'result_key' => 'creative',
                'title' => 'The Creative Innovator',
                'description' => 'You thrive on creativity and innovation. You prefer jobs that allow you to express yourself, think outside the box, and create something new. Consider roles in design, marketing, content creation, or creative industries.',
                'recommended_categories' => [],
                'traits' => ['Creative', 'Innovative', 'Independent', 'Artistic'],
                'icon' => 'fa-palette',
                'active' => true,
            ],
            [
                'result_key' => 'analytical',
                'title' => 'The Analytical Thinker',
                'description' => 'You excel at analyzing data, solving problems, and making data-driven decisions. Consider careers in finance, data analysis, research, or technology where your analytical skills can shine.',
                'recommended_categories' => [],
                'traits' => ['Logical', 'Detail-oriented', 'Problem-solver', 'Methodical'],
                'icon' => 'fa-chart-line',
                'active' => true,
            ],
            [
                'result_key' => 'leader',
                'title' => 'The Natural Leader',
                'description' => 'You have strong leadership qualities and enjoy guiding teams toward success. Management, consulting, or entrepreneurship could be great fits for your skills and ambitions.',
                'recommended_categories' => [],
                'traits' => ['Confident', 'Strategic', 'Decisive', 'Motivating'],
                'icon' => 'fa-crown',
                'active' => true,
            ],
            [
                'result_key' => 'helper',
                'title' => 'The Compassionate Helper',
                'description' => 'You find fulfillment in helping others and making a positive impact. Consider careers in healthcare, education, social work, or customer service where you can directly help people.',
                'recommended_categories' => [],
                'traits' => ['Empathetic', 'Patient', 'Supportive', 'Caring'],
                'icon' => 'fa-heart',
                'active' => true,
            ],
            [
                'result_key' => 'organizer',
                'title' => 'The Efficient Organizer',
                'description' => 'You excel at organizing, planning, and ensuring things run smoothly. Administrative, operations, or project management roles would suit your systematic approach.',
                'recommended_categories' => [],
                'traits' => ['Organized', 'Efficient', 'Reliable', 'Systematic'],
                'icon' => 'fa-tasks',
                'active' => true,
            ],
            [
                'result_key' => 'technical',
                'title' => 'The Technical Expert',
                'description' => 'You love learning new technical skills and working with technology. Software development, IT, engineering, or technical roles would be ideal for your skill set.',
                'recommended_categories' => [],
                'traits' => ['Technical', 'Curious', 'Adaptive', 'Skilled'],
                'icon' => 'fa-laptop-code',
                'active' => true,
            ],
        ];

        foreach ($results as $r) {
            QuizResult::create($r);
        }

        $this->command->info('Quiz questions and results seeded successfully!');
    }
}
