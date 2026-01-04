<?php

namespace App\Http\Controllers\Web\Admin;

use App\Models\CareerTip;
use App\Http\Controllers\Web\Admin\Panel\PanelController;
use App\Http\Requests\Admin\CareerTipRequest;

class CareerTipController extends PanelController
{
    public function setup()
    {
        $this->xPanel->setModel(CareerTip::class);
        $this->xPanel->setRoute(admin_uri('career-tips'));
        $this->xPanel->setEntityNameStrings('career tip', 'career tips');
        $this->xPanel->orderBy('created_at', 'desc');
        $this->xPanel->addButtonFromModelFunction('top', 'seed_tips', 'seedTipsButton', 'beginning');

        // Filters
        $this->xPanel->addFilter(
            [
                'name' => 'category',
                'type' => 'dropdown',
                'label' => 'Category',
            ],
            CareerTip::CATEGORIES,
            function ($value) {
                $this->xPanel->addClause('where', 'category', $value);
            }
        );

        $this->xPanel->addFilter(
            [
                'name' => 'is_featured',
                'type' => 'dropdown',
                'label' => 'Featured',
            ],
            [
                1 => 'Featured',
                0 => 'Not Featured',
            ],
            function ($value) {
                $this->xPanel->addClause('where', 'is_featured', $value);
            }
        );

        // Columns
        $this->xPanel->addColumn([
            'name' => 'id',
            'label' => 'ID',
            'type' => 'number',
        ]);

        $this->xPanel->addColumn([
            'name' => 'title',
            'label' => 'Title',
            'type' => 'text',
        ]);

        $this->xPanel->addColumn([
            'name' => 'category',
            'label' => 'Category',
            'type' => 'text',
            'value' => function ($entry) {
                return $entry->category_label;
            },
        ]);

        $this->xPanel->addColumn([
            'name' => 'is_featured',
            'label' => 'Featured',
            'type' => 'boolean',
        ]);

        $this->xPanel->addColumn([
            'name' => 'views',
            'label' => 'Views',
            'type' => 'number',
        ]);

        $this->xPanel->addColumn([
            'name' => 'active',
            'label' => 'Active',
            'type' => 'boolean',
        ]);

        $this->xPanel->addColumn([
            'name' => 'created_at',
            'label' => 'Created',
            'type' => 'datetime',
        ]);

        // Fields
        $this->xPanel->addField([
            'name' => 'title',
            'label' => 'Title',
            'type' => 'text',
            'attributes' => [
                'placeholder' => 'Enter the article title',
            ],
        ]);

        $this->xPanel->addField([
            'name' => 'slug',
            'label' => 'Slug (URL)',
            'type' => 'text',
            'hint' => 'Leave empty to auto-generate from title',
            'attributes' => [
                'placeholder' => 'custom-url-slug',
            ],
        ]);

        $this->xPanel->addField([
            'name' => 'category',
            'label' => 'Category',
            'type' => 'select_from_array',
            'options' => CareerTip::CATEGORIES,
            'allows_null' => false,
        ]);

        $this->xPanel->addField([
            'name' => 'excerpt',
            'label' => 'Excerpt',
            'type' => 'textarea',
            'hint' => 'A short summary of the article (displayed in listings)',
            'attributes' => [
                'rows' => 3,
            ],
        ]);

        $this->xPanel->addField([
            'name' => 'content',
            'label' => 'Content',
            'type' => 'wysiwyg',
            'hint' => 'The full article content',
        ]);

        $this->xPanel->addField([
            'name' => 'featured_image',
            'label' => 'Featured Image',
            'type' => 'text',
            'hint' => 'Path to image in storage (e.g., career-tips/image.jpg)',
        ]);

        $this->xPanel->addField([
            'name' => 'reading_time',
            'label' => 'Reading Time (minutes)',
            'type' => 'number',
            'default' => 5,
        ]);

        $this->xPanel->addField([
            'name' => 'is_featured',
            'label' => 'Featured Article',
            'type' => 'checkbox',
            'hint' => 'Featured articles appear prominently on the career resources page',
        ]);

        $this->xPanel->addField([
            'name' => 'active',
            'label' => 'Active',
            'type' => 'checkbox',
            'default' => true,
        ]);
    }

    public function store(CareerTipRequest $request)
    {
        return parent::storeCrud($request);
    }

    public function update(CareerTipRequest $request)
    {
        return parent::updateCrud($request);
    }

    /**
     * Seed default career tips
     */
    public function seedDefaults()
    {
        // Check if tips already exist
        if (CareerTip::count() > 0) {
            \Alert::warning('Career tips already exist. Clear existing tips first if you want to reseed.')->flash();
            return redirect()->back();
        }

        $tips = [
            [
                'title' => 'How to Write a CV That Gets Noticed',
                'category' => 'cv',
                'excerpt' => 'Learn the key elements of a standout CV that will catch recruiters\' attention.',
                'content' => '<h2>Start with a Strong Summary</h2><p>Your CV summary is the first thing recruiters see. Make it count by highlighting your key achievements and career goals.</p><h2>Use Action Words</h2><p>Start bullet points with strong action verbs like "Achieved," "Implemented," "Led," or "Developed."</p><h2>Quantify Your Achievements</h2><p>Numbers speak louder than words. Instead of "Improved sales," say "Increased sales by 25% in 6 months."</p><h2>Tailor for Each Application</h2><p>Customize your CV for each job by incorporating keywords from the job description.</p>',
                'reading_time' => 5,
                'is_featured' => true,
                'active' => true,
            ],
            [
                'title' => 'Top 10 Interview Questions and How to Answer Them',
                'category' => 'interview',
                'excerpt' => 'Prepare for your next interview with these common questions and winning answers.',
                'content' => '<h2>1. Tell Me About Yourself</h2><p>Focus on your professional journey, relevant experience, and why you\'re excited about this role.</p><h2>2. Why Do You Want This Job?</h2><p>Show you\'ve researched the company and explain how the role aligns with your career goals.</p><h2>3. What Are Your Strengths?</h2><p>Choose strengths relevant to the job and provide examples.</p><h2>4. What Are Your Weaknesses?</h2><p>Be honest but show how you\'re working to improve.</p><h2>5. Where Do You See Yourself in 5 Years?</h2><p>Show ambition while being realistic about growth within the company.</p>',
                'reading_time' => 8,
                'is_featured' => true,
                'active' => true,
            ],
            [
                'title' => 'Job Search Strategies That Actually Work',
                'category' => 'job-search',
                'excerpt' => 'Discover proven strategies to find your next job faster and more effectively.',
                'content' => '<h2>Network, Network, Network</h2><p>Up to 80% of jobs are filled through networking. Reach out to former colleagues, attend industry events, and be active on LinkedIn.</p><h2>Optimize Your Online Presence</h2><p>Make sure your LinkedIn profile is complete and professional. Many recruiters search for candidates online.</p><h2>Set Up Job Alerts</h2><p>Use job board alerts to be among the first to apply for new positions.</p><h2>Follow Up</h2><p>Don\'t be afraid to follow up on applications after a week or two.</p>',
                'reading_time' => 6,
                'is_featured' => true,
                'active' => true,
            ],
            [
                'title' => 'Remote Work: Tips for Success',
                'category' => 'remote-work',
                'excerpt' => 'Master the art of working from home with these essential tips.',
                'content' => '<h2>Create a Dedicated Workspace</h2><p>Set up a comfortable, distraction-free area for work.</p><h2>Maintain Regular Hours</h2><p>Stick to a schedule to maintain work-life balance.</p><h2>Over-communicate</h2><p>Without in-person interaction, it\'s important to keep your team informed of your progress.</p><h2>Take Regular Breaks</h2><p>Use techniques like the Pomodoro method to stay productive.</p>',
                'reading_time' => 4,
                'is_featured' => false,
                'active' => true,
            ],
            [
                'title' => 'Salary Negotiation: Get What You Deserve',
                'category' => 'career-growth',
                'excerpt' => 'Learn how to confidently negotiate your salary and benefits.',
                'content' => '<h2>Do Your Research</h2><p>Know the market rate for your role in your location.</p><h2>Timing Is Key</h2><p>Negotiate after receiving an offer, not before.</p><h2>Consider the Full Package</h2><p>Salary is just one part. Think about benefits, flexibility, and growth opportunities.</p><h2>Practice Your Pitch</h2><p>Rehearse what you\'ll say so you feel confident during the negotiation.</p>',
                'reading_time' => 5,
                'is_featured' => false,
                'active' => true,
            ],
        ];

        foreach ($tips as $tip) {
            CareerTip::create($tip);
        }

        \Alert::success('Successfully seeded ' . count($tips) . ' career tips.')->flash();
        return redirect()->back();
    }
}
