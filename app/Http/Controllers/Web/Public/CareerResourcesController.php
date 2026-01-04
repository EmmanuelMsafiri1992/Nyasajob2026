<?php

namespace App\Http\Controllers\Web\Public;

use App\Http\Controllers\Web\Public\FrontController;
use App\Models\CareerTip;
use App\Models\Category;
use App\Models\City;
use App\Models\Post;
use App\Models\QuizQuestion;
use App\Models\QuizResult;
use App\Models\UserQuizResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Larapen\LaravelMetaTags\Facades\MetaTag;

class CareerResourcesController extends FrontController
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Career Resources Hub - Main landing page
     */
    public function index()
    {
        $featuredTips = CareerTip::active()->featured()->orderByDesc('created_at')->limit(3)->get();
        $recentTips = CareerTip::active()->orderByDesc('created_at')->limit(6)->get();
        $categories = CareerTip::CATEGORIES;

        // Set meta tags
        MetaTag::set('title', 'Career Resources - Job Search Tips, CV Advice & Interview Prep | ' . config('app.name'));
        MetaTag::set('description', 'Free career resources including job search strategies, CV writing tips, interview preparation guides, and salary insights to help you land your dream job.');

        return view('career-resources.index', [
            'featuredTips' => $featuredTips,
            'recentTips' => $recentTips,
            'categories' => $categories,
        ]);
    }

    /**
     * Salary Insights - Show salary data by category and location
     */
    public function salaryInsights(Request $request)
    {
        $categoryId = $request->input('category');
        $cityId = $request->input('city');

        // Get categories with salary data
        $categoriesWithSalary = Cache::remember('salary_insights_categories', 3600, function () {
            return Category::whereHas('posts', function ($query) {
                $query->where('salary_min', '>', 0)
                    ->orWhere('salary_max', '>', 0);
            })->orderBy('name')->get();
        });

        // Get cities with salary data
        $citiesWithSalary = Cache::remember('salary_insights_cities', 3600, function () {
            return City::whereHas('posts', function ($query) {
                $query->where('salary_min', '>', 0)
                    ->orWhere('salary_max', '>', 0);
            })->orderBy('name')->get();
        });

        // Build salary statistics
        $salaryStats = $this->getSalaryStatistics($categoryId, $cityId);

        // Get salary by category chart data
        $salaryByCategory = $this->getSalaryByCategory();

        // Get salary by location chart data
        $salaryByLocation = $this->getSalaryByLocation();

        // Set meta tags
        MetaTag::set('title', 'Salary Insights - Know Your Worth | ' . config('app.name'));
        MetaTag::set('description', 'Explore salary ranges by job category and location. Get insights into average salaries, salary trends, and compensation data to negotiate better.');

        return view('career-resources.salary-insights', [
            'categoriesWithSalary' => $categoriesWithSalary,
            'citiesWithSalary' => $citiesWithSalary,
            'salaryStats' => $salaryStats,
            'salaryByCategory' => $salaryByCategory,
            'salaryByLocation' => $salaryByLocation,
            'selectedCategory' => $categoryId,
            'selectedCity' => $cityId,
        ]);
    }

    /**
     * Get salary statistics for given filters
     */
    protected function getSalaryStatistics(?int $categoryId = null, ?int $cityId = null): array
    {
        $cacheKey = "salary_stats_{$categoryId}_{$cityId}";

        return Cache::remember($cacheKey, 3600, function () use ($categoryId, $cityId) {
            $query = Post::where(function ($q) {
                $q->where('salary_min', '>', 0)
                    ->orWhere('salary_max', '>', 0);
            });

            if ($categoryId) {
                $query->where('category_id', $categoryId);
            }

            if ($cityId) {
                $query->where('city_id', $cityId);
            }

            $posts = $query->select('salary_min', 'salary_max', 'category_id')->get();

            if ($posts->isEmpty()) {
                return [
                    'count' => 0,
                    'avg_min' => 0,
                    'avg_max' => 0,
                    'overall_avg' => 0,
                    'min_salary' => 0,
                    'max_salary' => 0,
                    'median' => 0,
                ];
            }

            $salaries = [];
            foreach ($posts as $post) {
                if ($post->salary_min > 0) {
                    $salaries[] = $post->salary_min;
                }
                if ($post->salary_max > 0) {
                    $salaries[] = $post->salary_max;
                }
            }

            sort($salaries);
            $count = count($salaries);
            $median = $count > 0 ? $salaries[intval($count / 2)] : 0;

            return [
                'count' => $posts->count(),
                'avg_min' => round($posts->avg('salary_min')),
                'avg_max' => round($posts->avg('salary_max')),
                'overall_avg' => round(array_sum($salaries) / max(1, $count)),
                'min_salary' => min($salaries) ?: 0,
                'max_salary' => max($salaries) ?: 0,
                'median' => $median,
            ];
        });
    }

    /**
     * Get salary data grouped by category
     */
    protected function getSalaryByCategory(): array
    {
        return Cache::remember('salary_by_category_' . app()->getLocale(), 3600, function () {
            $results = DB::table('posts')
                ->join('categories', 'posts.category_id', '=', 'categories.id')
                ->select(
                    'categories.id',
                    'categories.name',
                    DB::raw('AVG(CASE WHEN posts.salary_min > 0 THEN posts.salary_min ELSE posts.salary_max END) as avg_salary'),
                    DB::raw('COUNT(*) as job_count')
                )
                ->where(function ($q) {
                    $q->where('posts.salary_min', '>', 0)
                        ->orWhere('posts.salary_max', '>', 0);
                })
                ->groupBy('categories.id', 'categories.name')
                ->orderByDesc('avg_salary')
                ->limit(10)
                ->get();

            return $results->map(function ($item) {
                // Parse translatable name - get current locale or English fallback
                $name = $item->name;
                if (is_string($name) && str_starts_with($name, '{')) {
                    $decoded = json_decode($name, true);
                    $locale = app()->getLocale();
                    $name = $decoded[$locale] ?? $decoded['en'] ?? $name;
                }
                return [
                    'name' => $name,
                    'salary' => round($item->avg_salary),
                    'jobs' => $item->job_count,
                ];
            })->toArray();
        });
    }

    /**
     * Get salary data grouped by location
     */
    protected function getSalaryByLocation(): array
    {
        return Cache::remember('salary_by_location_' . app()->getLocale(), 3600, function () {
            $results = DB::table('posts')
                ->join('cities', 'posts.city_id', '=', 'cities.id')
                ->select(
                    'cities.id',
                    'cities.name',
                    DB::raw('AVG(CASE WHEN posts.salary_min > 0 THEN posts.salary_min ELSE posts.salary_max END) as avg_salary'),
                    DB::raw('COUNT(*) as job_count')
                )
                ->where(function ($q) {
                    $q->where('posts.salary_min', '>', 0)
                        ->orWhere('posts.salary_max', '>', 0);
                })
                ->groupBy('cities.id', 'cities.name')
                ->orderByDesc('avg_salary')
                ->limit(10)
                ->get();

            return $results->map(function ($item) {
                // Parse translatable name - get current locale or English fallback
                $name = $item->name;
                if (is_string($name) && str_starts_with($name, '{')) {
                    $decoded = json_decode($name, true);
                    $locale = app()->getLocale();
                    $name = $decoded[$locale] ?? $decoded['en'] ?? $name;
                }
                return [
                    'name' => $name,
                    'salary' => round($item->avg_salary),
                    'jobs' => $item->job_count,
                ];
            })->toArray();
        });
    }

    /**
     * Career Tips - List all tips
     */
    public function careerTips(Request $request)
    {
        $category = $request->input('category');

        $query = CareerTip::active()->orderByDesc('is_featured')->orderByDesc('created_at');

        if ($category && isset(CareerTip::CATEGORIES[$category])) {
            $query->byCategory($category);
        }

        $tips = $query->paginate(12);
        $featuredTips = CareerTip::active()->featured()->limit(3)->get();
        $categories = CareerTip::CATEGORIES;

        // Set meta tags
        $title = $category && isset(CareerTip::CATEGORIES[$category])
            ? CareerTip::CATEGORIES[$category] . ' - Career Tips'
            : 'Career Tips & Advice';
        MetaTag::set('title', $title . ' | ' . config('app.name'));
        MetaTag::set('description', 'Expert career advice, job search tips, CV writing guides, and interview preparation resources to help you succeed in your job hunt.');

        return view('career-resources.tips.index', [
            'tips' => $tips,
            'featuredTips' => $featuredTips,
            'categories' => $categories,
            'selectedCategory' => $category,
        ]);
    }

    /**
     * Career Tips - Show single tip
     */
    public function showCareerTip(string $slug)
    {
        $tip = CareerTip::active()->where('slug', $slug)->firstOrFail();
        $tip->incrementViews();

        $relatedTips = $tip->relatedTips(4);

        // Set meta tags
        MetaTag::set('title', $tip->title . ' | ' . config('app.name'));
        MetaTag::set('description', $tip->excerpt ?: \Str::limit(strip_tags($tip->content), 160));

        return view('career-resources.tips.show', [
            'tip' => $tip,
            'relatedTips' => $relatedTips,
        ]);
    }

    /**
     * Job Quiz - Show quiz
     */
    public function jobQuiz()
    {
        $questions = QuizQuestion::active()->ordered()->get();
        $results = QuizResult::active()->get()->keyBy('result_key');

        if ($questions->isEmpty()) {
            // If no questions, seed some defaults
            $this->seedDefaultQuizQuestions();
            $questions = QuizQuestion::active()->ordered()->get();
            $results = QuizResult::active()->get()->keyBy('result_key');
        }

        // Set meta tags
        MetaTag::set('title', 'Find Your Ideal Job - Career Quiz | ' . config('app.name'));
        MetaTag::set('description', 'Take our free career quiz to discover your ideal job type based on your personality, skills, and preferences. Get personalized job recommendations.');

        return view('career-resources.quiz.index', [
            'questions' => $questions,
            'results' => $results,
        ]);
    }

    /**
     * Process quiz submission
     */
    public function submitQuiz(Request $request)
    {
        $answers = $request->input('answers', []);

        if (empty($answers)) {
            return response()->json(['error' => 'No answers provided'], 400);
        }

        // Calculate scores
        $scores = $this->calculateQuizScores($answers);

        // Determine result
        $resultKey = $this->determineQuizResult($scores);

        // Save response
        $response = UserQuizResponse::create([
            'user_id' => auth()->id(),
            'session_id' => session()->getId(),
            'answers' => $answers,
            'result_key' => $resultKey,
            'scores' => $scores,
        ]);

        $result = QuizResult::where('result_key', $resultKey)->first();

        return response()->json([
            'success' => true,
            'result' => $result,
            'scores' => $scores,
            'recommended_jobs_url' => route('search', ['c' => implode(',', $result->recommended_categories ?? [])]),
        ]);
    }

    /**
     * Calculate quiz scores from answers
     */
    protected function calculateQuizScores(array $answers): array
    {
        $scores = [];

        foreach ($answers as $questionId => $optionIndex) {
            $question = QuizQuestion::find($questionId);
            if (!$question) continue;

            $options = $question->options;
            if (isset($options[$optionIndex]['scores'])) {
                foreach ($options[$optionIndex]['scores'] as $key => $score) {
                    $scores[$key] = ($scores[$key] ?? 0) + $score;
                }
            }
        }

        return $scores;
    }

    /**
     * Determine quiz result based on scores
     */
    protected function determineQuizResult(array $scores): string
    {
        if (empty($scores)) {
            return 'generalist';
        }

        arsort($scores);
        return array_key_first($scores);
    }

    /**
     * Seed default quiz questions
     */
    protected function seedDefaultQuizQuestions(): void
    {
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
            ],
        ];

        foreach ($questions as $q) {
            QuizQuestion::create($q);
        }

        // Seed results
        $results = [
            [
                'result_key' => 'creative',
                'title' => 'The Creative Innovator',
                'description' => 'You thrive on creativity and innovation. You prefer jobs that allow you to express yourself, think outside the box, and create something new. Consider roles in design, marketing, content creation, or creative industries.',
                'recommended_categories' => [1, 5, 12], // Adjust based on actual category IDs
                'traits' => ['Creative', 'Innovative', 'Independent', 'Artistic'],
                'icon' => 'fa-palette',
            ],
            [
                'result_key' => 'analytical',
                'title' => 'The Analytical Thinker',
                'description' => 'You excel at analyzing data, solving problems, and making data-driven decisions. Consider careers in finance, data analysis, research, or technology where your analytical skills can shine.',
                'recommended_categories' => [2, 6, 11],
                'traits' => ['Logical', 'Detail-oriented', 'Problem-solver', 'Methodical'],
                'icon' => 'fa-chart-line',
            ],
            [
                'result_key' => 'leader',
                'title' => 'The Natural Leader',
                'description' => 'You have strong leadership qualities and enjoy guiding teams toward success. Management, consulting, or entrepreneurship could be great fits for your skills and ambitions.',
                'recommended_categories' => [3, 7, 9],
                'traits' => ['Confident', 'Strategic', 'Decisive', 'Motivating'],
                'icon' => 'fa-crown',
            ],
            [
                'result_key' => 'helper',
                'title' => 'The Compassionate Helper',
                'description' => 'You find fulfillment in helping others and making a positive impact. Consider careers in healthcare, education, social work, or customer service where you can directly help people.',
                'recommended_categories' => [4, 8, 10],
                'traits' => ['Empathetic', 'Patient', 'Supportive', 'Caring'],
                'icon' => 'fa-heart',
            ],
            [
                'result_key' => 'organizer',
                'title' => 'The Efficient Organizer',
                'description' => 'You excel at planning, organizing, and maintaining order. Administrative roles, project management, or operations could be ideal career paths for your organizational skills.',
                'recommended_categories' => [3, 6, 9],
                'traits' => ['Organized', 'Reliable', 'Systematic', 'Efficient'],
                'icon' => 'fa-tasks',
            ],
            [
                'result_key' => 'technical',
                'title' => 'The Technical Expert',
                'description' => 'You love working with technology and solving technical challenges. IT, engineering, software development, or technical support roles would suit your skills perfectly.',
                'recommended_categories' => [2, 11, 12],
                'traits' => ['Technical', 'Curious', 'Precise', 'Innovative'],
                'icon' => 'fa-laptop-code',
            ],
        ];

        foreach ($results as $r) {
            QuizResult::create($r);
        }
    }
}
