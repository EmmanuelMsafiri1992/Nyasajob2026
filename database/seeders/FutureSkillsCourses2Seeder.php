<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\CourseModule;
use App\Models\CourseLesson;
use App\Models\User;
use Illuminate\Database\Seeder;

class FutureSkillsCourses2Seeder extends Seeder
{
    private $instructor;

    public function run(): void
    {
        $this->instructor = User::where('is_admin', 1)->first() ?? User::first();

        $courses = [
            ['slug' => 'effective-communication-skills', 'method' => 'createCourse16', 'name' => 'Effective Communication Skills'],
            ['slug' => 'financial-literacy-for-professionals', 'method' => 'createCourse17', 'name' => 'Financial Literacy for Professionals'],
            ['slug' => 'leadership-in-digital-age', 'method' => 'createCourse18', 'name' => 'Leadership in the Digital Age'],
            ['slug' => 'critical-thinking-problem-solving', 'method' => 'createCourse19', 'name' => 'Critical Thinking & Problem Solving'],
            ['slug' => 'emotional-intelligence-at-work', 'method' => 'createCourse20', 'name' => 'Emotional Intelligence at Work'],
            ['slug' => 'blockchain-cryptocurrency-basics', 'method' => 'createCourse21', 'name' => 'Blockchain & Cryptocurrency Basics'],
            ['slug' => 'ux-ui-design-fundamentals', 'method' => 'createCourse22', 'name' => 'UX/UI Design Fundamentals'],
            ['slug' => 'data-visualization-power-bi', 'method' => 'createCourse23', 'name' => 'Data Visualization with Power BI'],
            ['slug' => 'api-development-integration', 'method' => 'createCourse24', 'name' => 'API Development & Integration'],
            ['slug' => 'git-version-control', 'method' => 'createCourse25', 'name' => 'Git & Version Control'],
            ['slug' => 'docker-containerization', 'method' => 'createCourse26', 'name' => 'Docker & Containerization'],
            ['slug' => 'entrepreneurship-startup-basics', 'method' => 'createCourse27', 'name' => 'Entrepreneurship & Startup Basics'],
            ['slug' => 'ecommerce-business-essentials', 'method' => 'createCourse28', 'name' => 'E-commerce Business Essentials'],
            ['slug' => 'react-for-beginners', 'method' => 'createCourse29', 'name' => 'React.js for Beginners'],
            ['slug' => 'data-privacy-gdpr', 'method' => 'createCourse30', 'name' => 'Data Privacy & GDPR Compliance'],
        ];

        foreach ($courses as $index => $c) {
            $num = 16 + $index;
            if (!Course::where('slug', $c['slug'])->exists()) {
                $this->{$c['method']}();
                $this->command->info("Course {$num} created: {$c['name']}");
            } else {
                $this->command->info("Course {$num} skipped (already exists)");
            }
        }
    }

    private function createCourse16(): void
    {
        $course = Course::create([
            'title' => 'Effective Communication Skills', 'slug' => 'effective-communication-skills',
            'description' => 'Master professional communication. Learn to write clearly, speak confidently, and present effectively in any business setting.',
            'objectives' => "- Write professional emails and documents\n- Present with confidence\n- Listen actively\n- Navigate difficult conversations",
            'price' => 29.99, 'is_free' => false, 'level' => 'beginner', 'duration_hours' => 4,
            'instructor_id' => $this->instructor?->id, 'is_published' => true,
        ]);
        $m1 = CourseModule::create(['course_id' => $course->id, 'title' => 'Written Communication', 'order' => 1]);
        $this->createLessons($m1, [
            ['title' => 'Professional Email Writing', 'content' => '<h2>Email Best Practices</h2><ul><li>Clear subject lines</li><li>One topic per email</li><li>Action items visible</li><li>Professional tone</li></ul>'],
            ['title' => 'Business Writing Essentials', 'content' => '<h2>Write Clearly</h2><ul><li>Know your audience</li><li>Get to the point</li><li>Use simple words</li><li>Proofread always</li></ul>'],
        ]);
        $m2 = CourseModule::create(['course_id' => $course->id, 'title' => 'Verbal Communication', 'order' => 2]);
        $this->createLessons($m2, [
            ['title' => 'Presentation Skills', 'content' => '<h2>Present Like a Pro</h2><ul><li>Structure: tell them what you\'ll tell them</li><li>Eye contact</li><li>Manage nerves</li><li>Handle Q&A</li></ul>'],
            ['title' => 'Active Listening', 'content' => '<h2>Listen to Understand</h2><ul><li>Pay full attention</li><li>Don\'t interrupt</li><li>Ask clarifying questions</li><li>Summarize back</li></ul>'],
        ]);
    }

    private function createCourse17(): void
    {
        $course = Course::create([
            'title' => 'Financial Literacy for Professionals', 'slug' => 'financial-literacy-for-professionals',
            'description' => 'Take control of your finances. Learn budgeting, investing, retirement planning, and smart money management.',
            'objectives' => "- Create and stick to a budget\n- Understand investing basics\n- Plan for retirement\n- Build wealth long-term",
            'price' => 39.99, 'is_free' => false, 'level' => 'beginner', 'duration_hours' => 4,
            'instructor_id' => $this->instructor?->id, 'is_published' => true,
        ]);
        $m1 = CourseModule::create(['course_id' => $course->id, 'title' => 'Personal Finance Basics', 'order' => 1]);
        $this->createLessons($m1, [
            ['title' => 'Budgeting 101', 'content' => '<h2>Track Your Money</h2><p>50/30/20 Rule: 50% needs, 30% wants, 20% savings</p>'],
            ['title' => 'Emergency Fund', 'content' => '<h2>Financial Safety Net</h2><p>Save 3-6 months of expenses for emergencies.</p>'],
            ['title' => 'Debt Management', 'content' => '<h2>Get Out of Debt</h2><ul><li>Avalanche: highest interest first</li><li>Snowball: smallest balance first</li></ul>'],
        ]);
        $m2 = CourseModule::create(['course_id' => $course->id, 'title' => 'Investing Basics', 'order' => 2]);
        $this->createLessons($m2, [
            ['title' => 'Introduction to Investing', 'content' => '<h2>Grow Your Money</h2><ul><li>Stocks</li><li>Bonds</li><li>Mutual funds</li><li>ETFs</li></ul>'],
            ['title' => 'Retirement Planning', 'content' => '<h2>Plan for the Future</h2><p>Start early, compound interest is powerful. 401k, IRA, pension plans.</p>'],
        ]);
    }

    private function createCourse18(): void
    {
        $course = Course::create([
            'title' => 'Leadership in the Digital Age', 'slug' => 'leadership-in-digital-age',
            'description' => 'Lead teams in a digital world. Learn modern leadership techniques for remote teams, digital transformation, and change management.',
            'objectives' => "- Lead remote and hybrid teams\n- Drive digital transformation\n- Inspire and motivate\n- Make strategic decisions",
            'price' => 49.99, 'is_free' => false, 'level' => 'intermediate', 'duration_hours' => 5,
            'instructor_id' => $this->instructor?->id, 'is_published' => true,
        ]);
        $m1 = CourseModule::create(['course_id' => $course->id, 'title' => 'Modern Leadership', 'order' => 1]);
        $this->createLessons($m1, [
            ['title' => 'Leadership Styles', 'content' => '<h2>Find Your Style</h2><ul><li>Servant leadership</li><li>Transformational</li><li>Democratic</li><li>Situational</li></ul>'],
            ['title' => 'Leading Remote Teams', 'content' => '<h2>Distance Leadership</h2><ul><li>Over-communicate</li><li>Build trust</li><li>Results over hours</li><li>Virtual team building</li></ul>'],
        ]);
        $m2 = CourseModule::create(['course_id' => $course->id, 'title' => 'Change Management', 'order' => 2]);
        $this->createLessons($m2, [
            ['title' => 'Driving Change', 'content' => '<h2>Lead Transformation</h2><ol><li>Create urgency</li><li>Build coalition</li><li>Form vision</li><li>Communicate</li><li>Remove obstacles</li></ol>'],
            ['title' => 'Building High-Performance Teams', 'content' => '<h2>Team Excellence</h2><ul><li>Clear goals</li><li>Right people</li><li>Psychological safety</li><li>Accountability</li></ul>'],
        ]);
    }

    private function createCourse19(): void
    {
        $course = Course::create([
            'title' => 'Critical Thinking & Problem Solving', 'slug' => 'critical-thinking-problem-solving',
            'description' => 'Think better, solve smarter. Develop critical thinking skills to analyze problems, make decisions, and solve complex challenges.',
            'objectives' => "- Analyze problems systematically\n- Avoid cognitive biases\n- Make better decisions\n- Solve complex problems",
            'price' => 0, 'is_free' => true, 'level' => 'beginner', 'duration_hours' => 3,
            'instructor_id' => $this->instructor?->id, 'is_published' => true,
        ]);
        $m1 = CourseModule::create(['course_id' => $course->id, 'title' => 'Critical Thinking', 'order' => 1]);
        $this->createLessons($m1, [
            ['title' => 'What is Critical Thinking?', 'content' => '<h2>Think Critically</h2><p>Objective analysis, evaluation of evidence, forming reasoned judgments.</p>'],
            ['title' => 'Cognitive Biases', 'content' => '<h2>Know Your Biases</h2><ul><li>Confirmation bias</li><li>Anchoring</li><li>Sunk cost fallacy</li><li>Availability heuristic</li></ul>'],
        ]);
        $m2 = CourseModule::create(['course_id' => $course->id, 'title' => 'Problem Solving', 'order' => 2]);
        $this->createLessons($m2, [
            ['title' => 'Problem Solving Frameworks', 'content' => '<h2>Structured Approaches</h2><ul><li>Define the problem</li><li>Analyze root causes</li><li>Generate solutions</li><li>Evaluate and select</li><li>Implement and review</li></ul>'],
            ['title' => 'Decision Making', 'content' => '<h2>Make Better Decisions</h2><ul><li>Pros/cons lists</li><li>Decision matrices</li><li>Consider second-order effects</li></ul>'],
        ]);
    }

    private function createCourse20(): void
    {
        $course = Course::create([
            'title' => 'Emotional Intelligence at Work', 'slug' => 'emotional-intelligence-at-work',
            'description' => 'Develop your EQ. Learn to understand and manage emotions for better relationships, leadership, and career success.',
            'objectives' => "- Improve self-awareness\n- Manage emotions effectively\n- Build better relationships\n- Navigate workplace dynamics",
            'price' => 34.99, 'is_free' => false, 'level' => 'beginner', 'duration_hours' => 4,
            'instructor_id' => $this->instructor?->id, 'is_published' => true,
        ]);
        $m1 = CourseModule::create(['course_id' => $course->id, 'title' => 'Understanding EQ', 'order' => 1]);
        $this->createLessons($m1, [
            ['title' => 'What is Emotional Intelligence?', 'content' => '<h2>The EQ Framework</h2><ul><li>Self-awareness</li><li>Self-regulation</li><li>Motivation</li><li>Empathy</li><li>Social skills</li></ul>'],
            ['title' => 'Self-Awareness', 'content' => '<h2>Know Yourself</h2><p>Recognize your emotions, strengths, weaknesses, and their impact on others.</p>'],
        ]);
        $m2 = CourseModule::create(['course_id' => $course->id, 'title' => 'EQ in Action', 'order' => 2]);
        $this->createLessons($m2, [
            ['title' => 'Managing Emotions', 'content' => '<h2>Self-Regulation</h2><ul><li>Pause before reacting</li><li>Identify triggers</li><li>Choose your response</li></ul>'],
            ['title' => 'Empathy and Relationships', 'content' => '<h2>Connect with Others</h2><p>Understand others\' perspectives, build trust, navigate conflict constructively.</p>'],
        ]);
    }

    private function createCourse21(): void
    {
        $course = Course::create([
            'title' => 'Blockchain & Cryptocurrency Basics', 'slug' => 'blockchain-cryptocurrency-basics',
            'description' => 'Understand blockchain technology. Learn how cryptocurrencies work, DeFi, NFTs, and the future of decentralized systems.',
            'objectives' => "- Understand blockchain technology\n- Know how cryptocurrencies work\n- Explore DeFi and NFTs\n- Assess risks and opportunities",
            'price' => 39.99, 'is_free' => false, 'level' => 'beginner', 'duration_hours' => 4,
            'instructor_id' => $this->instructor?->id, 'is_published' => true,
        ]);
        $m1 = CourseModule::create(['course_id' => $course->id, 'title' => 'Blockchain Fundamentals', 'order' => 1]);
        $this->createLessons($m1, [
            ['title' => 'What is Blockchain?', 'content' => '<h2>Distributed Ledger</h2><p>Decentralized, immutable record of transactions. No central authority needed.</p>'],
            ['title' => 'How Blockchain Works', 'content' => '<h2>The Technology</h2><ul><li>Blocks and chains</li><li>Cryptographic hashing</li><li>Consensus mechanisms</li><li>Mining/Staking</li></ul>'],
        ]);
        $m2 = CourseModule::create(['course_id' => $course->id, 'title' => 'Cryptocurrency', 'order' => 2]);
        $this->createLessons($m2, [
            ['title' => 'Understanding Crypto', 'content' => '<h2>Digital Currency</h2><ul><li>Bitcoin: digital gold</li><li>Ethereum: smart contracts</li><li>Stablecoins</li><li>Altcoins</li></ul>'],
            ['title' => 'DeFi and NFTs', 'content' => '<h2>New Applications</h2><ul><li>DeFi: Decentralized finance</li><li>NFTs: Digital ownership</li><li>DAOs: Decentralized organizations</li></ul>'],
        ]);
    }

    private function createCourse22(): void
    {
        $course = Course::create([
            'title' => 'UX/UI Design Fundamentals', 'slug' => 'ux-ui-design-fundamentals',
            'description' => 'Design user-friendly products. Learn UX research, UI design principles, prototyping, and create interfaces people love to use.',
            'objectives' => "- Apply UX design principles\n- Create user interfaces\n- Conduct user research\n- Prototype and test designs",
            'price' => 49.99, 'is_free' => false, 'level' => 'beginner', 'duration_hours' => 6,
            'instructor_id' => $this->instructor?->id, 'is_published' => true,
        ]);
        $m1 = CourseModule::create(['course_id' => $course->id, 'title' => 'UX Fundamentals', 'order' => 1]);
        $this->createLessons($m1, [
            ['title' => 'What is UX Design?', 'content' => '<h2>User Experience</h2><p>UX = How users feel when interacting with a product. Usable, useful, desirable.</p>'],
            ['title' => 'User Research', 'content' => '<h2>Understand Users</h2><ul><li>Interviews</li><li>Surveys</li><li>User personas</li><li>Journey mapping</li></ul>'],
        ]);
        $m2 = CourseModule::create(['course_id' => $course->id, 'title' => 'UI Design', 'order' => 2]);
        $this->createLessons($m2, [
            ['title' => 'UI Design Principles', 'content' => '<h2>Visual Design</h2><ul><li>Hierarchy</li><li>Consistency</li><li>Contrast</li><li>White space</li></ul>'],
            ['title' => 'Prototyping', 'content' => '<h2>Test Your Designs</h2><p>Tools: Figma, Sketch, Adobe XD. Low-fi to high-fi prototypes.</p>'],
        ]);
    }

    private function createCourse23(): void
    {
        $course = Course::create([
            'title' => 'Data Visualization with Power BI', 'slug' => 'data-visualization-power-bi',
            'description' => 'Create stunning dashboards with Power BI. Connect data sources, build reports, and share insights with interactive visualizations.',
            'objectives' => "- Connect to data sources\n- Build interactive reports\n- Create DAX calculations\n- Share and collaborate",
            'price' => 49.99, 'is_free' => false, 'level' => 'intermediate', 'duration_hours' => 6,
            'instructor_id' => $this->instructor?->id, 'is_published' => true,
        ]);
        $m1 = CourseModule::create(['course_id' => $course->id, 'title' => 'Power BI Basics', 'order' => 1]);
        $this->createLessons($m1, [
            ['title' => 'Introduction to Power BI', 'content' => '<h2>Microsoft\'s BI Tool</h2><ul><li>Power BI Desktop</li><li>Power BI Service</li><li>Power BI Mobile</li></ul>'],
            ['title' => 'Connecting to Data', 'content' => '<h2>Data Sources</h2><p>Excel, SQL, Web, APIs, SharePoint, and 100+ connectors.</p>'],
        ]);
        $m2 = CourseModule::create(['course_id' => $course->id, 'title' => 'Building Reports', 'order' => 2]);
        $this->createLessons($m2, [
            ['title' => 'Creating Visualizations', 'content' => '<h2>Chart Types</h2><ul><li>Bar/Column</li><li>Line/Area</li><li>Pie/Donut</li><li>Maps</li><li>Tables</li></ul>'],
            ['title' => 'DAX Basics', 'content' => '<h2>Calculations</h2><p>Data Analysis Expressions for custom measures and calculated columns.</p>'],
            ['title' => 'Publishing and Sharing', 'content' => '<h2>Collaborate</h2><p>Publish to Power BI Service, share dashboards, schedule refreshes.</p>'],
        ]);
    }

    private function createCourse24(): void
    {
        $course = Course::create([
            'title' => 'API Development & Integration', 'slug' => 'api-development-integration',
            'description' => 'Build and consume APIs. Learn REST principles, API design, authentication, and integration patterns for modern applications.',
            'objectives' => "- Understand REST principles\n- Design good APIs\n- Implement authentication\n- Integrate third-party APIs",
            'price' => 44.99, 'is_free' => false, 'level' => 'intermediate', 'duration_hours' => 5,
            'instructor_id' => $this->instructor?->id, 'is_published' => true,
        ]);
        $m1 = CourseModule::create(['course_id' => $course->id, 'title' => 'API Fundamentals', 'order' => 1]);
        $this->createLessons($m1, [
            ['title' => 'What is an API?', 'content' => '<h2>Application Programming Interface</h2><p>Contract between software systems. Request/Response pattern.</p>'],
            ['title' => 'REST Principles', 'content' => '<h2>RESTful APIs</h2><ul><li>HTTP methods: GET, POST, PUT, DELETE</li><li>Stateless</li><li>Resource-based URLs</li><li>JSON responses</li></ul>'],
        ]);
        $m2 = CourseModule::create(['course_id' => $course->id, 'title' => 'Working with APIs', 'order' => 2]);
        $this->createLessons($m2, [
            ['title' => 'API Authentication', 'content' => '<h2>Secure Your APIs</h2><ul><li>API Keys</li><li>OAuth 2.0</li><li>JWT tokens</li></ul>'],
            ['title' => 'Consuming APIs', 'content' => '<h2>Integration</h2><p>HTTP clients, error handling, rate limiting, webhooks.</p>'],
        ]);
    }

    private function createCourse25(): void
    {
        $course = Course::create([
            'title' => 'Git & Version Control', 'slug' => 'git-version-control',
            'description' => 'Master Git for code management. Learn version control, branching, merging, and collaboration workflows used by professional developers.',
            'objectives' => "- Use Git commands confidently\n- Work with branches\n- Collaborate on GitHub\n- Resolve merge conflicts",
            'price' => 0, 'is_free' => true, 'level' => 'beginner', 'duration_hours' => 4,
            'instructor_id' => $this->instructor?->id, 'is_published' => true,
        ]);
        $m1 = CourseModule::create(['course_id' => $course->id, 'title' => 'Git Basics', 'order' => 1]);
        $this->createLessons($m1, [
            ['title' => 'Introduction to Version Control', 'content' => '<h2>Why Git?</h2><p>Track changes, collaborate, revert mistakes, work on features in parallel.</p>'],
            ['title' => 'Essential Git Commands', 'content' => '<h2>Core Commands</h2><ul><li>git init, clone</li><li>git add, commit</li><li>git push, pull</li><li>git status, log</li></ul>'],
        ]);
        $m2 = CourseModule::create(['course_id' => $course->id, 'title' => 'Branching & Collaboration', 'order' => 2]);
        $this->createLessons($m2, [
            ['title' => 'Working with Branches', 'content' => '<h2>Parallel Development</h2><ul><li>git branch</li><li>git checkout</li><li>git merge</li></ul>'],
            ['title' => 'GitHub Workflows', 'content' => '<h2>Collaborate</h2><ul><li>Pull requests</li><li>Code reviews</li><li>Forks</li><li>Issues</li></ul>'],
        ]);
    }

    private function createCourse26(): void
    {
        $course = Course::create([
            'title' => 'Docker & Containerization', 'slug' => 'docker-containerization',
            'description' => 'Learn Docker for modern development. Containerize applications, create images, and orchestrate containers for consistent deployments.',
            'objectives' => "- Understand containerization\n- Create Docker images\n- Run and manage containers\n- Use Docker Compose",
            'price' => 49.99, 'is_free' => false, 'level' => 'intermediate', 'duration_hours' => 5,
            'instructor_id' => $this->instructor?->id, 'is_published' => true,
        ]);
        $m1 = CourseModule::create(['course_id' => $course->id, 'title' => 'Docker Fundamentals', 'order' => 1]);
        $this->createLessons($m1, [
            ['title' => 'What is Docker?', 'content' => '<h2>Containerization</h2><p>Package applications with dependencies. Run anywhere consistently.</p>'],
            ['title' => 'Docker Architecture', 'content' => '<h2>How Docker Works</h2><ul><li>Images</li><li>Containers</li><li>Dockerfile</li><li>Registry</li></ul>'],
        ]);
        $m2 = CourseModule::create(['course_id' => $course->id, 'title' => 'Working with Docker', 'order' => 2]);
        $this->createLessons($m2, [
            ['title' => 'Docker Commands', 'content' => '<h2>Essential Commands</h2><ul><li>docker build</li><li>docker run</li><li>docker ps</li><li>docker exec</li></ul>'],
            ['title' => 'Docker Compose', 'content' => '<h2>Multi-Container Apps</h2><p>Define and run multi-container applications with YAML configuration.</p>'],
        ]);
    }

    private function createCourse27(): void
    {
        $course = Course::create([
            'title' => 'Entrepreneurship & Startup Basics', 'slug' => 'entrepreneurship-startup-basics',
            'description' => 'Start your business journey. Learn to validate ideas, build MVPs, find customers, and navigate the startup world.',
            'objectives' => "- Validate business ideas\n- Build an MVP\n- Find product-market fit\n- Understand funding options",
            'price' => 39.99, 'is_free' => false, 'level' => 'beginner', 'duration_hours' => 5,
            'instructor_id' => $this->instructor?->id, 'is_published' => true,
        ]);
        $m1 = CourseModule::create(['course_id' => $course->id, 'title' => 'Startup Foundations', 'order' => 1]);
        $this->createLessons($m1, [
            ['title' => 'The Entrepreneurial Mindset', 'content' => '<h2>Think Like a Founder</h2><ul><li>Embrace uncertainty</li><li>Learn from failure</li><li>Bias for action</li><li>Customer obsession</li></ul>'],
            ['title' => 'Validating Your Idea', 'content' => '<h2>Test Before You Build</h2><ul><li>Problem interviews</li><li>Landing page tests</li><li>Smoke tests</li><li>Pre-sales</li></ul>'],
        ]);
        $m2 = CourseModule::create(['course_id' => $course->id, 'title' => 'Building & Growing', 'order' => 2]);
        $this->createLessons($m2, [
            ['title' => 'Building an MVP', 'content' => '<h2>Minimum Viable Product</h2><p>Build the smallest thing that delivers value. Learn, iterate, improve.</p>'],
            ['title' => 'Funding Options', 'content' => '<h2>Finance Your Startup</h2><ul><li>Bootstrapping</li><li>Angel investors</li><li>Venture capital</li><li>Crowdfunding</li></ul>'],
        ]);
    }

    private function createCourse28(): void
    {
        $course = Course::create([
            'title' => 'E-commerce Business Essentials', 'slug' => 'ecommerce-business-essentials',
            'description' => 'Build and grow online stores. Learn e-commerce platforms, payment processing, fulfillment, and digital selling strategies.',
            'objectives' => "- Set up an online store\n- Process payments securely\n- Manage fulfillment\n- Drive online sales",
            'price' => 44.99, 'is_free' => false, 'level' => 'beginner', 'duration_hours' => 5,
            'instructor_id' => $this->instructor?->id, 'is_published' => true,
        ]);
        $m1 = CourseModule::create(['course_id' => $course->id, 'title' => 'E-commerce Setup', 'order' => 1]);
        $this->createLessons($m1, [
            ['title' => 'E-commerce Platforms', 'content' => '<h2>Choose Your Platform</h2><ul><li>Shopify</li><li>WooCommerce</li><li>BigCommerce</li><li>Magento</li></ul>'],
            ['title' => 'Payment Processing', 'content' => '<h2>Accept Payments</h2><ul><li>Payment gateways</li><li>Stripe, PayPal</li><li>Security (PCI)</li><li>Multiple currencies</li></ul>'],
        ]);
        $m2 = CourseModule::create(['course_id' => $course->id, 'title' => 'E-commerce Operations', 'order' => 2]);
        $this->createLessons($m2, [
            ['title' => 'Fulfillment Strategies', 'content' => '<h2>Get Products to Customers</h2><ul><li>Self-fulfillment</li><li>3PL</li><li>Dropshipping</li><li>Amazon FBA</li></ul>'],
            ['title' => 'E-commerce Marketing', 'content' => '<h2>Drive Sales</h2><ul><li>SEO for products</li><li>Email marketing</li><li>Retargeting</li><li>Influencer partnerships</li></ul>'],
        ]);
    }

    private function createCourse29(): void
    {
        $course = Course::create([
            'title' => 'React.js for Beginners', 'slug' => 'react-for-beginners',
            'description' => 'Build modern web apps with React. Learn components, state, hooks, and create interactive user interfaces.',
            'objectives' => "- Understand React fundamentals\n- Build reusable components\n- Manage state with hooks\n- Create single-page applications",
            'price' => 49.99, 'is_free' => false, 'level' => 'intermediate', 'duration_hours' => 7,
            'instructor_id' => $this->instructor?->id, 'is_published' => true,
        ]);
        $m1 = CourseModule::create(['course_id' => $course->id, 'title' => 'React Fundamentals', 'order' => 1]);
        $this->createLessons($m1, [
            ['title' => 'Introduction to React', 'content' => '<h2>Why React?</h2><p>Component-based, virtual DOM, declarative, huge ecosystem.</p>'],
            ['title' => 'Components and JSX', 'content' => '<h2>Building Blocks</h2><p>JSX = JavaScript + HTML. Create reusable UI components.</p>'],
            ['title' => 'Props and State', 'content' => '<h2>Data Flow</h2><ul><li>Props: data passed down</li><li>State: internal data</li></ul>'],
        ]);
        $m2 = CourseModule::create(['course_id' => $course->id, 'title' => 'React Hooks', 'order' => 2]);
        $this->createLessons($m2, [
            ['title' => 'useState and useEffect', 'content' => '<h2>Essential Hooks</h2><ul><li>useState: manage state</li><li>useEffect: side effects</li></ul>'],
            ['title' => 'Building a React App', 'content' => '<h2>Putting It Together</h2><p>Create a complete app with routing, API calls, and state management.</p>'],
        ]);
    }

    private function createCourse30(): void
    {
        $course = Course::create([
            'title' => 'Data Privacy & GDPR Compliance', 'slug' => 'data-privacy-gdpr',
            'description' => 'Navigate data privacy regulations. Understand GDPR, CCPA, and best practices for protecting personal data in your organization.',
            'objectives' => "- Understand GDPR requirements\n- Implement privacy by design\n- Handle data subject requests\n- Avoid compliance penalties",
            'price' => 39.99, 'is_free' => false, 'level' => 'intermediate', 'duration_hours' => 4,
            'instructor_id' => $this->instructor?->id, 'is_published' => true,
        ]);
        $m1 = CourseModule::create(['course_id' => $course->id, 'title' => 'Privacy Fundamentals', 'order' => 1]);
        $this->createLessons($m1, [
            ['title' => 'Introduction to Data Privacy', 'content' => '<h2>Why Privacy Matters</h2><p>Protect individuals, build trust, avoid fines, legal requirement.</p>'],
            ['title' => 'GDPR Overview', 'content' => '<h2>Key Principles</h2><ul><li>Lawful basis</li><li>Purpose limitation</li><li>Data minimization</li><li>Accuracy</li><li>Storage limitation</li></ul>'],
        ]);
        $m2 = CourseModule::create(['course_id' => $course->id, 'title' => 'Compliance in Practice', 'order' => 2]);
        $this->createLessons($m2, [
            ['title' => 'Data Subject Rights', 'content' => '<h2>Individual Rights</h2><ul><li>Access</li><li>Rectification</li><li>Erasure</li><li>Portability</li><li>Object</li></ul>'],
            ['title' => 'Privacy by Design', 'content' => '<h2>Build Privacy In</h2><p>Consider privacy at every stage. Default settings, transparency, security.</p>'],
        ]);
    }

    private function createLessons(CourseModule $module, array $lessons): void
    {
        foreach ($lessons as $index => $lesson) {
            CourseLesson::create([
                'module_id' => $module->id,
                'title' => $lesson['title'],
                'content' => $lesson['content'],
                'type' => 'text',
                'order' => $index + 1,
                'duration_minutes' => 10,
                'is_free_preview' => $index === 0,
            ]);
        }
    }
}
