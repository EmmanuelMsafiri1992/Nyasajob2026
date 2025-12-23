<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\CourseModule;
use App\Models\CourseLesson;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class FutureSkillsCoursesSeeder extends Seeder
{
    private $instructor;

    public function run(): void
    {
        $this->instructor = User::where('is_admin', 1)->first() ?? User::first();

        if (!Course::where('slug', 'introduction-to-artificial-intelligence')->exists()) {
            $this->createCourse1();
            $this->command->info("Course 1 created: Introduction to Artificial Intelligence");
        } else {
            $this->command->info("Course 1 skipped (already exists)");
        }

        if (!Course::where('slug', 'prompt-engineering-mastery')->exists()) {
            $this->createCourse2();
            $this->command->info("Course 2 created: Prompt Engineering Mastery");
        } else {
            $this->command->info("Course 2 skipped (already exists)");
        }

        if (!Course::where('slug', 'machine-learning-fundamentals')->exists()) {
            $this->createCourse3();
            $this->command->info("Course 3 created: Machine Learning Fundamentals");
        } else {
            $this->command->info("Course 3 skipped (already exists)");
        }

        if (!Course::where('slug', 'ai-tools-for-business-productivity')->exists()) {
            $this->createCourse4();
            $this->command->info("Course 4 created: AI Tools for Business Productivity");
        } else {
            $this->command->info("Course 4 skipped (already exists)");
        }

        if (!Course::where('slug', 'data-literacy-for-everyone')->exists()) {
            $this->createCourse5();
            $this->command->info("Course 5 created: Data Literacy for Everyone");
        } else {
            $this->command->info("Course 5 skipped (already exists)");
        }

        if (!Course::where('slug', 'cybersecurity-fundamentals')->exists()) {
            $this->createCourse6();
            $this->command->info("Course 6 created: Cybersecurity Fundamentals");
        } else {
            $this->command->info("Course 6 skipped (already exists)");
        }

        if (!Course::where('slug', 'excel-for-data-analysis')->exists()) {
            $this->createCourse7();
            $this->command->info("Course 7 created: Excel for Data Analysis");
        } else {
            $this->command->info("Course 7 skipped (already exists)");
        }

        if (!Course::where('slug', 'digital-marketing-fundamentals')->exists()) {
            $this->createCourse8();
            $this->command->info("Course 8 created: Digital Marketing Fundamentals");
        } else {
            $this->command->info("Course 8 skipped (already exists)");
        }

        if (!Course::where('slug', 'project-management-with-agile')->exists()) {
            $this->createCourse9();
            $this->command->info("Course 9 created: Project Management with Agile");
        } else {
            $this->command->info("Course 9 skipped (already exists)");
        }

        if (!Course::where('slug', 'cloud-computing-fundamentals')->exists()) {
            $this->createCourse10();
            $this->command->info("Course 10 created: Cloud Computing Fundamentals");
        } else {
            $this->command->info("Course 10 skipped (already exists)");
        }

        $this->createCoursesIfNotExist([
            ['slug' => 'sql-database-fundamentals', 'method' => 'createCourse11', 'name' => 'SQL Database Fundamentals'],
            ['slug' => 'python-for-beginners', 'method' => 'createCourse12', 'name' => 'Python for Beginners'],
            ['slug' => 'html-css-fundamentals', 'method' => 'createCourse13', 'name' => 'HTML & CSS Fundamentals'],
            ['slug' => 'javascript-essentials', 'method' => 'createCourse14', 'name' => 'JavaScript Essentials'],
            ['slug' => 'remote-work-productivity', 'method' => 'createCourse15', 'name' => 'Remote Work & Productivity'],
        ]);
    }

    private function createCoursesIfNotExist(array $courses): void
    {
        foreach ($courses as $index => $c) {
            $num = 11 + $index;
            if (!Course::where('slug', $c['slug'])->exists()) {
                $this->{$c['method']}();
                $this->command->info("Course {$num} created: {$c['name']}");
            } else {
                $this->command->info("Course {$num} skipped (already exists)");
            }
        }
    }

    private function createCourse1(): void
    {
        $course = Course::create([
            'title' => 'Introduction to Artificial Intelligence',
            'slug' => 'introduction-to-artificial-intelligence',
            'description' => 'Discover the fascinating world of AI. Learn what artificial intelligence is, how it works, and how it\'s transforming every industry. No technical background required.',
            'objectives' => "- Understand what AI is and its different types\n- Learn how AI systems learn and make decisions\n- Explore real-world AI applications\n- Understand AI\'s impact on jobs and society\n- Identify opportunities to use AI in your work",
            'price' => 0,
            'is_free' => true,
            'level' => 'beginner',
            'duration_hours' => 3,
            'instructor_id' => $this->instructor?->id,
            'is_published' => true,
        ]);

        // Module 1
        $m1 = CourseModule::create([
            'course_id' => $course->id,
            'title' => 'What is Artificial Intelligence?',
            'description' => 'Understanding the basics of AI and its history',
            'order' => 1,
        ]);

        $this->createLessons($m1, [
            ['title' => 'Welcome to the AI Revolution', 'content' => '<h2>The AI Revolution is Here</h2><p>Artificial Intelligence is no longer science fiction. From the moment you wake up to smartphone suggestions, to Netflix recommendations, to voice assistants - AI is everywhere.</p><h3>What You\'ll Learn</h3><ul><li>What AI really means</li><li>Why AI matters now more than ever</li><li>How AI will shape your future career</li></ul><p>By the end of this course, you\'ll understand AI well enough to have informed conversations and identify opportunities in your field.</p>'],
            ['title' => 'Defining Artificial Intelligence', 'content' => '<h2>What is AI?</h2><p>AI is the simulation of human intelligence by machines. It\'s about creating systems that can learn, reason, and make decisions.</p><h3>Key Concepts</h3><ul><li><strong>Intelligence:</strong> The ability to learn, understand, and apply knowledge</li><li><strong>Artificial:</strong> Made by humans, not natural</li><li><strong>Machine Learning:</strong> AI that improves through experience</li></ul><h3>AI vs Traditional Software</h3><p>Traditional software follows exact rules. AI learns patterns and makes predictions based on data.</p>'],
            ['title' => 'Brief History of AI', 'content' => '<h2>The Journey of AI</h2><h3>Timeline</h3><ul><li><strong>1950:</strong> Alan Turing proposes the Turing Test</li><li><strong>1956:</strong> Term "Artificial Intelligence" coined</li><li><strong>1997:</strong> IBM\'s Deep Blue beats chess champion</li><li><strong>2011:</strong> IBM Watson wins Jeopardy!</li><li><strong>2022:</strong> ChatGPT launches, AI goes mainstream</li></ul><p>After decades of research, AI has finally reached a tipping point where it\'s practical and accessible to everyone.</p>'],
            ['title' => 'Types of AI: Narrow vs General', 'content' => '<h2>Types of Artificial Intelligence</h2><h3>Narrow AI (Weak AI)</h3><p>Designed for specific tasks. This is what we have today.</p><ul><li>Voice assistants (Siri, Alexa)</li><li>Recommendation systems</li><li>Image recognition</li></ul><h3>General AI (Strong AI)</h3><p>Human-level intelligence across all tasks. This doesn\'t exist yet.</p><h3>Super AI</h3><p>Beyond human intelligence. Theoretical and debated.</p>'],
        ]);

        // Module 2
        $m2 = CourseModule::create([
            'course_id' => $course->id,
            'title' => 'How AI Works',
            'description' => 'Understanding the mechanics behind AI systems',
            'order' => 2,
        ]);

        $this->createLessons($m2, [
            ['title' => 'Machine Learning Explained Simply', 'content' => '<h2>How Machines Learn</h2><p>Machine Learning is how AI improves from experience, just like humans learn from practice.</p><h3>The Process</h3><ol><li><strong>Data:</strong> Feed the system examples</li><li><strong>Training:</strong> System finds patterns</li><li><strong>Model:</strong> Creates rules from patterns</li><li><strong>Prediction:</strong> Applies rules to new situations</li></ol><h3>Example: Email Spam Filter</h3><p>Show the system 10,000 spam emails and 10,000 good emails. It learns patterns like "Nigerian prince" = spam.</p>'],
            ['title' => 'Neural Networks: AI\'s Brain', 'content' => '<h2>Neural Networks</h2><p>Inspired by the human brain, neural networks are layers of connected nodes that process information.</p><h3>How They Work</h3><ul><li><strong>Input Layer:</strong> Receives data (image, text, numbers)</li><li><strong>Hidden Layers:</strong> Process and find patterns</li><li><strong>Output Layer:</strong> Makes prediction or decision</li></ul><h3>Deep Learning</h3><p>Neural networks with many hidden layers. This powers image recognition, language translation, and ChatGPT.</p>'],
            ['title' => 'Training Data: The Fuel of AI', 'content' => '<h2>Data is Everything</h2><p>AI is only as good as the data it learns from.</p><h3>Types of Training Data</h3><ul><li><strong>Labeled Data:</strong> Data with correct answers (supervised learning)</li><li><strong>Unlabeled Data:</strong> Raw data for pattern finding (unsupervised learning)</li></ul><h3>Data Quality Matters</h3><p>Garbage in, garbage out. Biased data creates biased AI.</p><h3>Big Data</h3><p>Modern AI needs massive amounts of data. GPT-4 trained on hundreds of billions of words.</p>'],
            ['title' => 'AI Models and Algorithms', 'content' => '<h2>Common AI Approaches</h2><h3>Supervised Learning</h3><p>Learn from labeled examples. Used for prediction and classification.</p><h3>Unsupervised Learning</h3><p>Find hidden patterns. Used for clustering and anomaly detection.</p><h3>Reinforcement Learning</h3><p>Learn by trial and error. Used for games and robotics.</p><h3>Large Language Models (LLMs)</h3><p>Trained on text to understand and generate language. Powers ChatGPT, Claude, and similar tools.</p>'],
        ]);

        // Module 3
        $m3 = CourseModule::create([
            'course_id' => $course->id,
            'title' => 'AI in the Real World',
            'description' => 'Practical applications of AI across industries',
            'order' => 3,
        ]);

        $this->createLessons($m3, [
            ['title' => 'AI in Healthcare', 'content' => '<h2>AI Transforming Healthcare</h2><h3>Applications</h3><ul><li><strong>Diagnosis:</strong> AI detects diseases from scans faster than humans</li><li><strong>Drug Discovery:</strong> AI finds new medicines in months, not years</li><li><strong>Personalized Treatment:</strong> AI recommends treatments based on your genetics</li><li><strong>Administrative:</strong> AI handles paperwork and scheduling</li></ul><h3>Real Example</h3><p>Google\'s AI can detect diabetic retinopathy from eye scans with 90%+ accuracy.</p>'],
            ['title' => 'AI in Business and Finance', 'content' => '<h2>AI in Business</h2><h3>Applications</h3><ul><li><strong>Customer Service:</strong> Chatbots handle common questions 24/7</li><li><strong>Fraud Detection:</strong> AI spots suspicious transactions instantly</li><li><strong>Trading:</strong> Algorithmic trading makes split-second decisions</li><li><strong>Forecasting:</strong> AI predicts sales, inventory needs, market trends</li></ul><h3>Impact</h3><p>Companies using AI see 20-30% efficiency improvements on average.</p>'],
            ['title' => 'AI in Creative Fields', 'content' => '<h2>AI and Creativity</h2><h3>What AI Can Create</h3><ul><li><strong>Images:</strong> DALL-E, Midjourney generate art from text</li><li><strong>Writing:</strong> ChatGPT writes articles, code, poetry</li><li><strong>Music:</strong> AI composes original songs</li><li><strong>Video:</strong> AI generates and edits video content</li></ul><h3>The Debate</h3><p>Is AI-generated content "art"? Will AI replace human creators? The answer: AI is a tool that amplifies human creativity.</p>'],
            ['title' => 'AI in Daily Life', 'content' => '<h2>AI You Use Every Day</h2><h3>In Your Phone</h3><ul><li>Face recognition unlock</li><li>Autocorrect and predictive text</li><li>Photo enhancement</li></ul><h3>At Home</h3><ul><li>Smart speakers (Alexa, Google Home)</li><li>Streaming recommendations</li><li>Smart thermostats</li></ul><h3>Online</h3><ul><li>Search engine results</li><li>Social media feeds</li><li>Online ads</li></ul>'],
        ]);

        // Module 4
        $m4 = CourseModule::create([
            'course_id' => $course->id,
            'title' => 'AI Ethics and Future',
            'description' => 'Responsible AI and what\'s coming next',
            'order' => 4,
        ]);

        $this->createLessons($m4, [
            ['title' => 'AI Ethics and Bias', 'content' => '<h2>The Ethics of AI</h2><h3>Key Concerns</h3><ul><li><strong>Bias:</strong> AI can perpetuate human biases from training data</li><li><strong>Privacy:</strong> AI needs data, raising privacy concerns</li><li><strong>Transparency:</strong> "Black box" AI makes unexplainable decisions</li><li><strong>Accountability:</strong> Who\'s responsible when AI makes mistakes?</li></ul><h3>Real Examples</h3><p>Facial recognition has shown bias against certain ethnicities. Hiring AI has shown gender bias.</p>'],
            ['title' => 'AI and Jobs: Threat or Opportunity?', 'content' => '<h2>AI and Employment</h2><h3>Jobs at Risk</h3><ul><li>Data entry and processing</li><li>Basic customer service</li><li>Simple writing tasks</li><li>Routine analysis</li></ul><h3>Jobs AI Creates</h3><ul><li>AI trainers and prompt engineers</li><li>AI ethics specialists</li><li>AI integration consultants</li><li>Human-AI collaboration roles</li></ul><h3>The Truth</h3><p>AI won\'t replace humans, but humans using AI will replace those who don\'t.</p>'],
            ['title' => 'The Future of AI', 'content' => '<h2>What\'s Coming</h2><h3>Near Future (1-5 years)</h3><ul><li>AI assistants in every job</li><li>Personalized education</li><li>Advanced medical diagnosis</li></ul><h3>Medium Term (5-15 years)</h3><ul><li>Autonomous vehicles mainstream</li><li>AI scientists and researchers</li><li>Highly personalized everything</li></ul><h3>Long Term</h3><ul><li>General AI possibility</li><li>Human-AI collaboration at scale</li><li>Unknown transformations</li></ul>'],
            ['title' => 'Getting Started with AI', 'content' => '<h2>Your AI Journey Starts Now</h2><h3>Action Steps</h3><ol><li><strong>Use AI tools:</strong> Try ChatGPT, Claude, Midjourney</li><li><strong>Stay informed:</strong> Follow AI news and developments</li><li><strong>Learn more:</strong> Take courses on specific AI applications</li><li><strong>Apply at work:</strong> Find AI opportunities in your field</li></ol><h3>Recommended Next Courses</h3><ul><li>Prompt Engineering Mastery</li><li>AI Tools for Business Productivity</li><li>Machine Learning Fundamentals</li></ul><p><strong>Congratulations!</strong> You now understand AI better than most people.</p>'],
        ]);
    }

    private function createCourse2(): void
    {
        $course = Course::create([
            'title' => 'Prompt Engineering Mastery',
            'slug' => 'prompt-engineering-mastery',
            'description' => 'Master the art of communicating with AI. Learn to write effective prompts that get amazing results from ChatGPT, Claude, and other AI tools.',
            'objectives' => "- Write clear, effective prompts\n- Use advanced prompting techniques\n- Get consistent, high-quality AI outputs\n- Apply prompt engineering at work",
            'price' => 29.99,
            'is_free' => false,
            'level' => 'beginner',
            'duration_hours' => 4,
            'instructor_id' => $this->instructor?->id,
            'is_published' => true,
        ]);

        $m1 = CourseModule::create(['course_id' => $course->id, 'title' => 'Prompt Fundamentals', 'description' => 'Core concepts of effective prompting', 'order' => 1]);
        $this->createLessons($m1, [
            ['title' => 'What is Prompt Engineering?', 'content' => '<h2>The New Skill Everyone Needs</h2><p>Prompt engineering is the art of communicating with AI to get the best results.</p><h3>Why It Matters</h3><ul><li>Better prompts = better AI outputs</li><li>Save hours of back-and-forth</li><li>Unlock AI capabilities others miss</li></ul>'],
            ['title' => 'Anatomy of a Good Prompt', 'content' => '<h2>Building Blocks of Prompts</h2><h3>Key Elements</h3><ul><li><strong>Context:</strong> Background information</li><li><strong>Task:</strong> What you want done</li><li><strong>Format:</strong> How you want the output</li><li><strong>Constraints:</strong> Limitations or rules</li></ul>'],
            ['title' => 'Common Prompting Mistakes', 'content' => '<h2>Avoid These Errors</h2><ul><li>Being too vague</li><li>Not providing examples</li><li>Asking multiple things at once</li><li>Not specifying format</li><li>Forgetting context</li></ul>'],
        ]);

        $m2 = CourseModule::create(['course_id' => $course->id, 'title' => 'Advanced Techniques', 'description' => 'Powerful prompting strategies', 'order' => 2]);
        $this->createLessons($m2, [
            ['title' => 'Role-Based Prompting', 'content' => '<h2>Assign AI a Role</h2><p>Tell AI to act as an expert for better results.</p><h3>Example</h3><p>"Act as a senior marketing manager with 15 years experience..."</p>'],
            ['title' => 'Chain of Thought Prompting', 'content' => '<h2>Step-by-Step Reasoning</h2><p>Ask AI to think through problems step by step.</p><h3>Example</h3><p>"Let\'s solve this step by step..."</p>'],
            ['title' => 'Few-Shot Learning', 'content' => '<h2>Teaching by Example</h2><p>Provide examples of what you want.</p><h3>Format</h3><p>Input: [example] Output: [example] Now do: [your task]</p>'],
            ['title' => 'Iterative Refinement', 'content' => '<h2>Improve Through Conversation</h2><p>Build on AI responses to get exactly what you need.</p>'],
        ]);

        $m3 = CourseModule::create(['course_id' => $course->id, 'title' => 'Practical Applications', 'description' => 'Real-world prompt templates', 'order' => 3]);
        $this->createLessons($m3, [
            ['title' => 'Writing and Content Creation', 'content' => '<h2>Prompts for Writers</h2><h3>Templates</h3><ul><li>Blog posts</li><li>Email drafts</li><li>Social media</li><li>Reports</li></ul>'],
            ['title' => 'Coding and Technical Work', 'content' => '<h2>Prompts for Developers</h2><h3>Use Cases</h3><ul><li>Code generation</li><li>Bug fixing</li><li>Documentation</li><li>Code review</li></ul>'],
            ['title' => 'Business and Analysis', 'content' => '<h2>Prompts for Business</h2><h3>Applications</h3><ul><li>Data analysis</li><li>Strategy planning</li><li>Meeting summaries</li><li>Presentations</li></ul>'],
        ]);
    }

    private function createCourse3(): void
    {
        $course = Course::create([
            'title' => 'Machine Learning Fundamentals',
            'slug' => 'machine-learning-fundamentals',
            'description' => 'Understand how machines learn from data. This course covers the core concepts of machine learning without requiring programming skills.',
            'objectives' => "- Understand ML algorithms and when to use them\n- Know the ML workflow from data to deployment\n- Evaluate ML models effectively\n- Apply ML concepts in your field",
            'price' => 49.99,
            'is_free' => false,
            'level' => 'intermediate',
            'duration_hours' => 6,
            'instructor_id' => $this->instructor?->id,
            'is_published' => true,
        ]);

        $m1 = CourseModule::create(['course_id' => $course->id, 'title' => 'ML Foundations', 'description' => 'Core concepts of machine learning', 'order' => 1]);
        $this->createLessons($m1, [
            ['title' => 'What is Machine Learning?', 'content' => '<h2>Machines That Learn</h2><p>ML is a subset of AI where systems improve from experience without being explicitly programmed.</p><h3>Types</h3><ul><li>Supervised Learning</li><li>Unsupervised Learning</li><li>Reinforcement Learning</li></ul>'],
            ['title' => 'Supervised Learning Explained', 'content' => '<h2>Learning from Labels</h2><p>Train with input-output pairs. The algorithm learns to map inputs to correct outputs.</p><h3>Examples</h3><ul><li>Spam detection</li><li>Price prediction</li><li>Image classification</li></ul>'],
            ['title' => 'Unsupervised Learning Explained', 'content' => '<h2>Finding Hidden Patterns</h2><p>No labels provided. Algorithm discovers structure in data.</p><h3>Examples</h3><ul><li>Customer segmentation</li><li>Anomaly detection</li><li>Recommendation systems</li></ul>'],
        ]);

        $m2 = CourseModule::create(['course_id' => $course->id, 'title' => 'ML Algorithms', 'description' => 'Common algorithms and when to use them', 'order' => 2]);
        $this->createLessons($m2, [
            ['title' => 'Linear Regression', 'content' => '<h2>Predicting Numbers</h2><p>Find the line that best fits your data.</p><h3>Use Cases</h3><ul><li>Sales forecasting</li><li>Price estimation</li><li>Trend analysis</li></ul>'],
            ['title' => 'Classification Algorithms', 'content' => '<h2>Predicting Categories</h2><p>Algorithms that sort data into classes.</p><h3>Popular Algorithms</h3><ul><li>Decision Trees</li><li>Random Forests</li><li>Support Vector Machines</li></ul>'],
            ['title' => 'Clustering Algorithms', 'content' => '<h2>Grouping Similar Items</h2><p>Find natural groupings in data.</p><h3>Applications</h3><ul><li>Customer segments</li><li>Document grouping</li><li>Image compression</li></ul>'],
            ['title' => 'Neural Networks Overview', 'content' => '<h2>Deep Learning</h2><p>Layers of connected nodes that can learn complex patterns.</p><h3>When to Use</h3><ul><li>Image recognition</li><li>Natural language</li><li>Complex patterns</li></ul>'],
        ]);

        $m3 = CourseModule::create(['course_id' => $course->id, 'title' => 'ML in Practice', 'description' => 'Building and evaluating models', 'order' => 3]);
        $this->createLessons($m3, [
            ['title' => 'The ML Workflow', 'content' => '<h2>From Data to Model</h2><ol><li>Collect data</li><li>Clean and prepare</li><li>Choose algorithm</li><li>Train model</li><li>Evaluate</li><li>Deploy</li></ol>'],
            ['title' => 'Evaluating ML Models', 'content' => '<h2>Is Your Model Good?</h2><h3>Metrics</h3><ul><li>Accuracy</li><li>Precision & Recall</li><li>F1 Score</li><li>RMSE</li></ul>'],
            ['title' => 'Common ML Pitfalls', 'content' => '<h2>Avoid These Mistakes</h2><ul><li>Overfitting</li><li>Data leakage</li><li>Biased training data</li><li>Wrong metric choice</li></ul>'],
        ]);
    }

    private function createCourse4(): void
    {
        $course = Course::create([
            'title' => 'AI Tools for Business Productivity',
            'slug' => 'ai-tools-for-business-productivity',
            'description' => 'Supercharge your productivity with AI tools. Learn to use ChatGPT, Claude, Copilot, and other AI assistants to work smarter, not harder.',
            'objectives' => "- Master popular AI productivity tools\n- Automate repetitive tasks\n- Create content 10x faster\n- Make better decisions with AI assistance",
            'price' => 39.99,
            'is_free' => false,
            'level' => 'beginner',
            'duration_hours' => 4,
            'instructor_id' => $this->instructor?->id,
            'is_published' => true,
        ]);

        $m1 = CourseModule::create(['course_id' => $course->id, 'title' => 'AI Assistant Tools', 'description' => 'ChatGPT, Claude, and more', 'order' => 1]);
        $this->createLessons($m1, [
            ['title' => 'Introduction to AI Assistants', 'content' => '<h2>Your AI Helpers</h2><p>AI assistants can help with writing, research, analysis, coding, and creative tasks.</p><h3>Popular Tools</h3><ul><li>ChatGPT (OpenAI)</li><li>Claude (Anthropic)</li><li>Copilot (Microsoft)</li><li>Gemini (Google)</li></ul>'],
            ['title' => 'ChatGPT Deep Dive', 'content' => '<h2>Mastering ChatGPT</h2><h3>Best Uses</h3><ul><li>Writing assistance</li><li>Research and summarization</li><li>Coding help</li><li>Learning new topics</li></ul>'],
            ['title' => 'Claude and Other Alternatives', 'content' => '<h2>Beyond ChatGPT</h2><h3>Claude Strengths</h3><ul><li>Longer conversations</li><li>Document analysis</li><li>Nuanced writing</li></ul><h3>When to Use What</h3><p>Different tools excel at different tasks.</p>'],
        ]);

        $m2 = CourseModule::create(['course_id' => $course->id, 'title' => 'AI for Content Creation', 'description' => 'Writing, images, and more', 'order' => 2]);
        $this->createLessons($m2, [
            ['title' => 'AI Writing Tools', 'content' => '<h2>Write Faster with AI</h2><h3>Tools</h3><ul><li>Jasper</li><li>Copy.ai</li><li>Grammarly</li><li>Notion AI</li></ul>'],
            ['title' => 'AI Image Generation', 'content' => '<h2>Create Images with AI</h2><h3>Tools</h3><ul><li>DALL-E</li><li>Midjourney</li><li>Stable Diffusion</li><li>Canva AI</li></ul>'],
            ['title' => 'AI for Presentations', 'content' => '<h2>Presentations Made Easy</h2><h3>Tools</h3><ul><li>Beautiful.ai</li><li>Tome</li><li>Gamma</li></ul>'],
        ]);

        $m3 = CourseModule::create(['course_id' => $course->id, 'title' => 'AI Workflow Integration', 'description' => 'Automate your work', 'order' => 3]);
        $this->createLessons($m3, [
            ['title' => 'AI in Microsoft 365', 'content' => '<h2>Copilot in Office</h2><h3>Features</h3><ul><li>Word: Draft documents</li><li>Excel: Analyze data</li><li>PowerPoint: Create slides</li><li>Outlook: Write emails</li></ul>'],
            ['title' => 'AI Automation Tools', 'content' => '<h2>Automate Repetitive Tasks</h2><h3>Tools</h3><ul><li>Zapier AI</li><li>Make (Integromat)</li><li>Power Automate</li></ul>'],
            ['title' => 'Building Your AI Workflow', 'content' => '<h2>Create Your System</h2><ol><li>Identify repetitive tasks</li><li>Choose right AI tool</li><li>Set up automation</li><li>Review and improve</li></ol>'],
        ]);
    }

    private function createCourse5(): void
    {
        $course = Course::create([
            'title' => 'Data Literacy for Everyone',
            'slug' => 'data-literacy-for-everyone',
            'description' => 'Understand data in today\'s world. Learn to read charts, understand statistics, and make data-driven decisions without being a data scientist.',
            'objectives' => "- Read and interpret data visualizations\n- Understand basic statistics\n- Spot misleading data\n- Make data-informed decisions",
            'price' => 0,
            'is_free' => true,
            'level' => 'beginner',
            'duration_hours' => 3,
            'instructor_id' => $this->instructor?->id,
            'is_published' => true,
        ]);

        $m1 = CourseModule::create(['course_id' => $course->id, 'title' => 'Understanding Data', 'description' => 'What is data and why it matters', 'order' => 1]);
        $this->createLessons($m1, [
            ['title' => 'What is Data Literacy?', 'content' => '<h2>The New Essential Skill</h2><p>Data literacy is the ability to read, understand, create, and communicate data.</p><h3>Why It Matters</h3><ul><li>Better decisions</li><li>Career advancement</li><li>Avoid being misled</li></ul>'],
            ['title' => 'Types of Data', 'content' => '<h2>Data Categories</h2><h3>Quantitative</h3><p>Numbers you can measure: sales, temperature, counts</p><h3>Qualitative</h3><p>Descriptive data: colors, opinions, categories</p>'],
            ['title' => 'Data Sources', 'content' => '<h2>Where Data Comes From</h2><ul><li>Surveys and forms</li><li>Transactions</li><li>Sensors and IoT</li><li>Social media</li><li>Public datasets</li></ul>'],
        ]);

        $m2 = CourseModule::create(['course_id' => $course->id, 'title' => 'Reading Data Visualizations', 'description' => 'Charts, graphs, and dashboards', 'order' => 2]);
        $this->createLessons($m2, [
            ['title' => 'Bar Charts and Line Graphs', 'content' => '<h2>Common Charts</h2><h3>Bar Charts</h3><p>Compare categories</p><h3>Line Graphs</h3><p>Show trends over time</p>'],
            ['title' => 'Pie Charts and Beyond', 'content' => '<h2>More Visualization Types</h2><ul><li>Pie charts: parts of whole</li><li>Scatter plots: relationships</li><li>Heat maps: intensity</li></ul>'],
            ['title' => 'Spotting Misleading Charts', 'content' => '<h2>Data Deception</h2><h3>Red Flags</h3><ul><li>Truncated axes</li><li>Cherry-picked data</li><li>Missing context</li><li>3D effects</li></ul>'],
        ]);

        $m3 = CourseModule::create(['course_id' => $course->id, 'title' => 'Basic Statistics', 'description' => 'Numbers everyone should know', 'order' => 3]);
        $this->createLessons($m3, [
            ['title' => 'Averages Explained', 'content' => '<h2>Measures of Center</h2><h3>Mean</h3><p>Add all numbers, divide by count</p><h3>Median</h3><p>Middle value</p><h3>Mode</h3><p>Most common value</p>'],
            ['title' => 'Understanding Percentages', 'content' => '<h2>Percentages and Proportions</h2><h3>Key Concepts</h3><ul><li>Percent change</li><li>Percentage points</li><li>Per capita</li></ul>'],
            ['title' => 'Correlation vs Causation', 'content' => '<h2>The Big Mistake</h2><p>Correlation does NOT equal causation.</p><h3>Example</h3><p>Ice cream sales and drowning both go up in summer. Ice cream doesn\'t cause drowning.</p>'],
        ]);
    }

    private function createCourse6(): void
    {
        $course = Course::create([
            'title' => 'Cybersecurity Fundamentals',
            'slug' => 'cybersecurity-fundamentals',
            'description' => 'Protect yourself and your organization from cyber threats. Learn essential security concepts, common attacks, and how to stay safe online.',
            'objectives' => "- Understand common cyber threats\n- Implement security best practices\n- Protect personal and work data\n- Respond to security incidents",
            'price' => 49.99,
            'is_free' => false,
            'level' => 'beginner',
            'duration_hours' => 5,
            'instructor_id' => $this->instructor?->id,
            'is_published' => true,
        ]);

        $m1 = CourseModule::create(['course_id' => $course->id, 'title' => 'Cyber Threat Landscape', 'description' => 'Understanding the dangers', 'order' => 1]);
        $this->createLessons($m1, [
            ['title' => 'Introduction to Cybersecurity', 'content' => '<h2>Why Security Matters</h2><p>Cyber attacks cost billions annually and affect everyone.</p><h3>The CIA Triad</h3><ul><li>Confidentiality</li><li>Integrity</li><li>Availability</li></ul>'],
            ['title' => 'Common Cyber Threats', 'content' => '<h2>Know Your Enemy</h2><ul><li><strong>Malware:</strong> Viruses, ransomware, spyware</li><li><strong>Phishing:</strong> Fake emails and websites</li><li><strong>Social Engineering:</strong> Manipulating people</li><li><strong>DDoS:</strong> Overwhelming systems</li></ul>'],
            ['title' => 'Who Are the Attackers?', 'content' => '<h2>Threat Actors</h2><ul><li>Cybercriminals (money)</li><li>Nation states (espionage)</li><li>Hacktivists (ideology)</li><li>Insiders (disgruntled employees)</li></ul>'],
        ]);

        $m2 = CourseModule::create(['course_id' => $course->id, 'title' => 'Personal Security', 'description' => 'Protecting yourself online', 'order' => 2]);
        $this->createLessons($m2, [
            ['title' => 'Password Security', 'content' => '<h2>Strong Passwords</h2><h3>Best Practices</h3><ul><li>Use a password manager</li><li>Unique password per site</li><li>Long > complex</li><li>Enable 2FA everywhere</li></ul>'],
            ['title' => 'Recognizing Phishing', 'content' => '<h2>Spot the Fake</h2><h3>Red Flags</h3><ul><li>Urgent language</li><li>Suspicious sender</li><li>Generic greeting</li><li>Hover over links first</li></ul>'],
            ['title' => 'Safe Browsing Habits', 'content' => '<h2>Stay Safe Online</h2><ul><li>Use HTTPS sites</li><li>Be careful on public WiFi</li><li>Keep software updated</li><li>Use a VPN when needed</li></ul>'],
            ['title' => 'Mobile Security', 'content' => '<h2>Secure Your Phone</h2><ul><li>Use screen lock</li><li>Download from official stores</li><li>Review app permissions</li><li>Enable remote wipe</li></ul>'],
        ]);

        $m3 = CourseModule::create(['course_id' => $course->id, 'title' => 'Workplace Security', 'description' => 'Protecting your organization', 'order' => 3]);
        $this->createLessons($m3, [
            ['title' => 'Security Awareness at Work', 'content' => '<h2>Be the Human Firewall</h2><ul><li>Report suspicious emails</li><li>Lock your computer</li><li>Don\'t share credentials</li><li>Follow company policies</li></ul>'],
            ['title' => 'Data Protection Basics', 'content' => '<h2>Protect Sensitive Data</h2><h3>Data Classification</h3><ul><li>Public</li><li>Internal</li><li>Confidential</li><li>Restricted</li></ul>'],
            ['title' => 'Incident Response', 'content' => '<h2>When Things Go Wrong</h2><ol><li>Don\'t panic</li><li>Report immediately</li><li>Don\'t try to fix it yourself</li><li>Document what happened</li><li>Follow IT guidance</li></ol>'],
        ]);
    }

    private function createCourse7(): void
    {
        $course = Course::create([
            'title' => 'Excel for Data Analysis',
            'slug' => 'excel-for-data-analysis',
            'description' => 'Master Excel for data analysis. Learn pivot tables, advanced formulas, data visualization, and Power Query to analyze data like a pro.',
            'objectives' => "- Master essential Excel formulas\n- Create pivot tables and charts\n- Clean and transform data\n- Build professional dashboards",
            'price' => 39.99, 'is_free' => false, 'level' => 'intermediate', 'duration_hours' => 6,
            'instructor_id' => $this->instructor?->id, 'is_published' => true,
        ]);
        $m1 = CourseModule::create(['course_id' => $course->id, 'title' => 'Essential Formulas', 'description' => 'Must-know Excel functions', 'order' => 1]);
        $this->createLessons($m1, [
            ['title' => 'VLOOKUP and XLOOKUP', 'content' => '<h2>Lookup Functions</h2><p>Find data across tables.</p><h3>VLOOKUP</h3><p>=VLOOKUP(value, table, column, FALSE)</p><h3>XLOOKUP</h3><p>Modern replacement: more flexible and powerful.</p>'],
            ['title' => 'IF and Nested IFs', 'content' => '<h2>Conditional Logic</h2><p>=IF(condition, true_value, false_value)</p><h3>Nested IFs</h3><p>Multiple conditions with IFS function.</p>'],
            ['title' => 'SUMIFS and COUNTIFS', 'content' => '<h2>Conditional Aggregation</h2><p>Sum or count with multiple criteria.</p>'],
        ]);
        $m2 = CourseModule::create(['course_id' => $course->id, 'title' => 'Pivot Tables', 'description' => 'Summarize data instantly', 'order' => 2]);
        $this->createLessons($m2, [
            ['title' => 'Creating Pivot Tables', 'content' => '<h2>Instant Analysis</h2><p>Select data > Insert > Pivot Table</p><h3>Drag and drop fields</h3><ul><li>Rows</li><li>Columns</li><li>Values</li><li>Filters</li></ul>'],
            ['title' => 'Pivot Table Calculations', 'content' => '<h2>Beyond Sums</h2><ul><li>Count, Average, Max, Min</li><li>% of total</li><li>Running totals</li><li>Calculated fields</li></ul>'],
            ['title' => 'Slicers and Timelines', 'content' => '<h2>Interactive Filtering</h2><p>Visual filters for pivot tables and charts.</p>'],
        ]);
        $m3 = CourseModule::create(['course_id' => $course->id, 'title' => 'Data Visualization', 'description' => 'Charts and dashboards', 'order' => 3]);
        $this->createLessons($m3, [
            ['title' => 'Choosing the Right Chart', 'content' => '<h2>Chart Selection</h2><ul><li>Comparison: Bar/Column</li><li>Trend: Line</li><li>Composition: Pie/Stacked</li><li>Relationship: Scatter</li></ul>'],
            ['title' => 'Building Dashboards', 'content' => '<h2>Executive Dashboards</h2><ol><li>Plan your layout</li><li>Create pivot charts</li><li>Add slicers</li><li>Format professionally</li></ol>'],
        ]);
    }

    private function createCourse8(): void
    {
        $course = Course::create([
            'title' => 'Digital Marketing Fundamentals',
            'slug' => 'digital-marketing-fundamentals',
            'description' => 'Learn modern digital marketing. Master SEO, social media, content marketing, email campaigns, and paid advertising to grow any business online.',
            'objectives' => "- Create effective marketing strategies\n- Optimize for search engines (SEO)\n- Run social media campaigns\n- Measure marketing ROI",
            'price' => 49.99, 'is_free' => false, 'level' => 'beginner', 'duration_hours' => 5,
            'instructor_id' => $this->instructor?->id, 'is_published' => true,
        ]);
        $m1 = CourseModule::create(['course_id' => $course->id, 'title' => 'Digital Marketing Basics', 'description' => 'Foundation concepts', 'order' => 1]);
        $this->createLessons($m1, [
            ['title' => 'The Digital Marketing Landscape', 'content' => '<h2>Marketing Channels</h2><ul><li>Search (SEO & SEM)</li><li>Social Media</li><li>Email</li><li>Content</li><li>Paid Advertising</li></ul>'],
            ['title' => 'Understanding Your Audience', 'content' => '<h2>Know Your Customer</h2><h3>Buyer Personas</h3><p>Create detailed profiles of ideal customers.</p>'],
            ['title' => 'The Marketing Funnel', 'content' => '<h2>AIDA Model</h2><ul><li>Awareness</li><li>Interest</li><li>Desire</li><li>Action</li></ul>'],
        ]);
        $m2 = CourseModule::create(['course_id' => $course->id, 'title' => 'SEO Essentials', 'description' => 'Get found on Google', 'order' => 2]);
        $this->createLessons($m2, [
            ['title' => 'How Search Engines Work', 'content' => '<h2>SEO Basics</h2><h3>Google\'s Process</h3><ol><li>Crawling</li><li>Indexing</li><li>Ranking</li></ol>'],
            ['title' => 'On-Page SEO', 'content' => '<h2>Optimize Your Pages</h2><ul><li>Title tags</li><li>Meta descriptions</li><li>Headers (H1, H2)</li><li>Content quality</li></ul>'],
            ['title' => 'Off-Page SEO', 'content' => '<h2>Building Authority</h2><ul><li>Backlinks</li><li>Social signals</li><li>Brand mentions</li></ul>'],
        ]);
        $m3 = CourseModule::create(['course_id' => $course->id, 'title' => 'Social Media Marketing', 'description' => 'Build your presence', 'order' => 3]);
        $this->createLessons($m3, [
            ['title' => 'Choosing Platforms', 'content' => '<h2>Where to Focus</h2><ul><li>LinkedIn: B2B, professionals</li><li>Instagram: Visual, younger</li><li>Facebook: Broad reach</li><li>TikTok: Gen Z, viral</li></ul>'],
            ['title' => 'Content Strategy', 'content' => '<h2>What to Post</h2><ul><li>Educational content</li><li>Behind-the-scenes</li><li>User-generated content</li><li>Promotional (20% rule)</li></ul>'],
            ['title' => 'Measuring Success', 'content' => '<h2>Key Metrics</h2><ul><li>Reach & Impressions</li><li>Engagement rate</li><li>Click-through rate</li><li>Conversions</li></ul>'],
        ]);
    }

    private function createCourse9(): void
    {
        $course = Course::create([
            'title' => 'Project Management with Agile',
            'slug' => 'project-management-with-agile',
            'description' => 'Master Agile project management. Learn Scrum, Kanban, and modern PM techniques to deliver projects on time and delight stakeholders.',
            'objectives' => "- Apply Agile principles\n- Run effective Scrum sprints\n- Use Kanban for workflow\n- Lead project teams",
            'price' => 59.99, 'is_free' => false, 'level' => 'intermediate', 'duration_hours' => 6,
            'instructor_id' => $this->instructor?->id, 'is_published' => true,
        ]);
        $m1 = CourseModule::create(['course_id' => $course->id, 'title' => 'Agile Foundations', 'description' => 'Core Agile concepts', 'order' => 1]);
        $this->createLessons($m1, [
            ['title' => 'What is Agile?', 'content' => '<h2>The Agile Mindset</h2><h3>Agile Values</h3><ul><li>Individuals over processes</li><li>Working software over documentation</li><li>Customer collaboration over contracts</li><li>Responding to change over following a plan</li></ul>'],
            ['title' => 'Agile vs Waterfall', 'content' => '<h2>Different Approaches</h2><h3>Waterfall</h3><p>Linear, sequential phases</p><h3>Agile</h3><p>Iterative, flexible, adaptive</p>'],
            ['title' => 'The Agile Manifesto', 'content' => '<h2>12 Principles</h2><p>Customer satisfaction, welcome change, deliver frequently, collaborate daily, trust teams.</p>'],
        ]);
        $m2 = CourseModule::create(['course_id' => $course->id, 'title' => 'Scrum Framework', 'description' => 'The most popular Agile method', 'order' => 2]);
        $this->createLessons($m2, [
            ['title' => 'Scrum Roles', 'content' => '<h2>The Scrum Team</h2><ul><li><strong>Product Owner:</strong> Vision, backlog</li><li><strong>Scrum Master:</strong> Process, obstacles</li><li><strong>Development Team:</strong> Build product</li></ul>'],
            ['title' => 'Scrum Events', 'content' => '<h2>Scrum Ceremonies</h2><ul><li>Sprint Planning</li><li>Daily Standup</li><li>Sprint Review</li><li>Retrospective</li></ul>'],
            ['title' => 'Scrum Artifacts', 'content' => '<h2>Key Documents</h2><ul><li>Product Backlog</li><li>Sprint Backlog</li><li>Increment</li></ul>'],
            ['title' => 'Running a Sprint', 'content' => '<h2>2-Week Cycle</h2><ol><li>Plan: What can we deliver?</li><li>Execute: Daily standups</li><li>Review: Demo to stakeholders</li><li>Retro: How can we improve?</li></ol>'],
        ]);
        $m3 = CourseModule::create(['course_id' => $course->id, 'title' => 'Kanban Method', 'description' => 'Visual workflow management', 'order' => 3]);
        $this->createLessons($m3, [
            ['title' => 'Kanban Basics', 'content' => '<h2>Visual Management</h2><h3>Core Practices</h3><ul><li>Visualize work</li><li>Limit WIP</li><li>Manage flow</li><li>Make policies explicit</li></ul>'],
            ['title' => 'Building a Kanban Board', 'content' => '<h2>Board Structure</h2><p>Columns: To Do → In Progress → Review → Done</p><h3>WIP Limits</h3><p>Limit work in progress to improve flow.</p>'],
            ['title' => 'Kanban Metrics', 'content' => '<h2>Measuring Performance</h2><ul><li>Lead time</li><li>Cycle time</li><li>Throughput</li><li>Cumulative flow</li></ul>'],
        ]);
    }

    private function createCourse10(): void
    {
        $course = Course::create([
            'title' => 'Cloud Computing Fundamentals',
            'slug' => 'cloud-computing-fundamentals',
            'description' => 'Understand cloud computing. Learn AWS, Azure, and GCP basics, cloud architecture, and how to leverage the cloud for your business.',
            'objectives' => "- Understand cloud service models\n- Compare major cloud providers\n- Design basic cloud architectures\n- Make informed cloud decisions",
            'price' => 49.99, 'is_free' => false, 'level' => 'beginner', 'duration_hours' => 5,
            'instructor_id' => $this->instructor?->id, 'is_published' => true,
        ]);
        $m1 = CourseModule::create(['course_id' => $course->id, 'title' => 'Cloud Basics', 'description' => 'What is cloud computing', 'order' => 1]);
        $this->createLessons($m1, [
            ['title' => 'Introduction to Cloud', 'content' => '<h2>What is Cloud Computing?</h2><p>On-demand delivery of IT resources over the internet with pay-as-you-go pricing.</p><h3>Benefits</h3><ul><li>No upfront costs</li><li>Scale instantly</li><li>Global reach</li><li>High availability</li></ul>'],
            ['title' => 'Service Models: IaaS, PaaS, SaaS', 'content' => '<h2>Cloud Service Types</h2><ul><li><strong>IaaS:</strong> Rent infrastructure (VMs, storage)</li><li><strong>PaaS:</strong> Platform for developers</li><li><strong>SaaS:</strong> Ready-to-use software</li></ul>'],
            ['title' => 'Public vs Private vs Hybrid', 'content' => '<h2>Deployment Models</h2><ul><li><strong>Public:</strong> Shared infrastructure</li><li><strong>Private:</strong> Dedicated to one org</li><li><strong>Hybrid:</strong> Best of both</li></ul>'],
        ]);
        $m2 = CourseModule::create(['course_id' => $course->id, 'title' => 'Major Cloud Providers', 'description' => 'AWS, Azure, GCP overview', 'order' => 2]);
        $this->createLessons($m2, [
            ['title' => 'Amazon Web Services (AWS)', 'content' => '<h2>The Market Leader</h2><h3>Key Services</h3><ul><li>EC2: Virtual servers</li><li>S3: Object storage</li><li>RDS: Databases</li><li>Lambda: Serverless</li></ul>'],
            ['title' => 'Microsoft Azure', 'content' => '<h2>Enterprise Favorite</h2><h3>Strengths</h3><ul><li>Microsoft integration</li><li>Hybrid cloud</li><li>Enterprise tools</li></ul>'],
            ['title' => 'Google Cloud Platform', 'content' => '<h2>Innovation Leader</h2><h3>Strengths</h3><ul><li>Data analytics</li><li>Machine learning</li><li>Kubernetes (GKE)</li></ul>'],
        ]);
        $m3 = CourseModule::create(['course_id' => $course->id, 'title' => 'Cloud Architecture', 'description' => 'Design principles', 'order' => 3]);
        $this->createLessons($m3, [
            ['title' => 'Cloud Design Principles', 'content' => '<h2>Well-Architected Framework</h2><ul><li>Security</li><li>Reliability</li><li>Performance</li><li>Cost optimization</li><li>Operational excellence</li></ul>'],
            ['title' => 'Common Cloud Patterns', 'content' => '<h2>Architecture Patterns</h2><ul><li>Microservices</li><li>Serverless</li><li>Event-driven</li><li>Multi-tier</li></ul>'],
            ['title' => 'Cloud Migration Strategies', 'content' => '<h2>The 6 Rs</h2><ul><li>Rehost (lift and shift)</li><li>Replatform</li><li>Repurchase</li><li>Refactor</li><li>Retain</li><li>Retire</li></ul>'],
        ]);
    }

    private function createCourse11(): void
    {
        $course = Course::create([
            'title' => 'SQL Database Fundamentals', 'slug' => 'sql-database-fundamentals',
            'description' => 'Learn SQL from scratch. Query databases, join tables, aggregate data, and write efficient queries for data analysis.',
            'objectives' => "- Write SQL queries\n- Join multiple tables\n- Aggregate and filter data\n- Create and modify tables",
            'price' => 39.99, 'is_free' => false, 'level' => 'beginner', 'duration_hours' => 5,
            'instructor_id' => $this->instructor?->id, 'is_published' => true,
        ]);
        $m1 = CourseModule::create(['course_id' => $course->id, 'title' => 'SQL Basics', 'order' => 1]);
        $this->createLessons($m1, [
            ['title' => 'Introduction to Databases', 'content' => '<h2>What is SQL?</h2><p>Structured Query Language for managing relational databases.</p>'],
            ['title' => 'SELECT Statements', 'content' => '<h2>Retrieving Data</h2><p>SELECT column FROM table WHERE condition;</p>'],
            ['title' => 'Filtering with WHERE', 'content' => '<h2>Filter Results</h2><p>Use WHERE, AND, OR, IN, BETWEEN, LIKE</p>'],
        ]);
        $m2 = CourseModule::create(['course_id' => $course->id, 'title' => 'Joins and Relationships', 'order' => 2]);
        $this->createLessons($m2, [
            ['title' => 'Understanding JOINs', 'content' => '<h2>Combining Tables</h2><ul><li>INNER JOIN</li><li>LEFT JOIN</li><li>RIGHT JOIN</li><li>FULL JOIN</li></ul>'],
            ['title' => 'Aggregation Functions', 'content' => '<h2>Summarize Data</h2><p>COUNT, SUM, AVG, MIN, MAX with GROUP BY</p>'],
        ]);
    }

    private function createCourse12(): void
    {
        $course = Course::create([
            'title' => 'Python for Beginners', 'slug' => 'python-for-beginners',
            'description' => 'Start your programming journey with Python. Learn the most versatile language used in AI, data science, web development, and automation.',
            'objectives' => "- Write Python programs\n- Use variables and data types\n- Control flow and loops\n- Functions and modules",
            'price' => 0, 'is_free' => true, 'level' => 'beginner', 'duration_hours' => 6,
            'instructor_id' => $this->instructor?->id, 'is_published' => true,
        ]);
        $m1 = CourseModule::create(['course_id' => $course->id, 'title' => 'Python Basics', 'order' => 1]);
        $this->createLessons($m1, [
            ['title' => 'Introduction to Python', 'content' => '<h2>Why Python?</h2><p>Simple syntax, vast libraries, huge community. Used in AI, web, automation, data science.</p>'],
            ['title' => 'Variables and Data Types', 'content' => '<h2>Storing Data</h2><p>Strings, integers, floats, booleans, lists, dictionaries</p>'],
            ['title' => 'Basic Operations', 'content' => '<h2>Working with Data</h2><p>Arithmetic, string manipulation, type conversion</p>'],
        ]);
        $m2 = CourseModule::create(['course_id' => $course->id, 'title' => 'Control Flow', 'order' => 2]);
        $this->createLessons($m2, [
            ['title' => 'If Statements', 'content' => '<h2>Making Decisions</h2><p>if, elif, else for conditional logic</p>'],
            ['title' => 'Loops', 'content' => '<h2>Repetition</h2><p>for loops, while loops, break, continue</p>'],
            ['title' => 'Functions', 'content' => '<h2>Reusable Code</h2><p>def function_name(parameters): return result</p>'],
        ]);
    }

    private function createCourse13(): void
    {
        $course = Course::create([
            'title' => 'HTML & CSS Fundamentals', 'slug' => 'html-css-fundamentals',
            'description' => 'Build beautiful websites from scratch. Learn HTML structure and CSS styling to create professional web pages.',
            'objectives' => "- Structure web pages with HTML\n- Style with CSS\n- Create responsive layouts\n- Build real projects",
            'price' => 0, 'is_free' => true, 'level' => 'beginner', 'duration_hours' => 5,
            'instructor_id' => $this->instructor?->id, 'is_published' => true,
        ]);
        $m1 = CourseModule::create(['course_id' => $course->id, 'title' => 'HTML Basics', 'order' => 1]);
        $this->createLessons($m1, [
            ['title' => 'Introduction to HTML', 'content' => '<h2>Web Page Structure</h2><p>HTML = HyperText Markup Language. Tags define structure.</p>'],
            ['title' => 'Common HTML Elements', 'content' => '<h2>Building Blocks</h2><p>headings, paragraphs, links, images, lists, divs</p>'],
            ['title' => 'Forms and Inputs', 'content' => '<h2>User Input</h2><p>text, email, password, checkbox, radio, submit</p>'],
        ]);
        $m2 = CourseModule::create(['course_id' => $course->id, 'title' => 'CSS Styling', 'order' => 2]);
        $this->createLessons($m2, [
            ['title' => 'CSS Basics', 'content' => '<h2>Adding Style</h2><p>Selectors, properties, values. Inline, internal, external CSS.</p>'],
            ['title' => 'Box Model and Layout', 'content' => '<h2>Positioning Elements</h2><p>margin, padding, border, display, flexbox</p>'],
            ['title' => 'Responsive Design', 'content' => '<h2>Mobile-Friendly</h2><p>Media queries, viewport, flexible grids</p>'],
        ]);
    }

    private function createCourse14(): void
    {
        $course = Course::create([
            'title' => 'JavaScript Essentials', 'slug' => 'javascript-essentials',
            'description' => 'Add interactivity to websites. Learn JavaScript fundamentals to create dynamic, interactive web experiences.',
            'objectives' => "- JavaScript syntax and concepts\n- DOM manipulation\n- Event handling\n- Async programming basics",
            'price' => 29.99, 'is_free' => false, 'level' => 'beginner', 'duration_hours' => 6,
            'instructor_id' => $this->instructor?->id, 'is_published' => true,
        ]);
        $m1 = CourseModule::create(['course_id' => $course->id, 'title' => 'JS Fundamentals', 'order' => 1]);
        $this->createLessons($m1, [
            ['title' => 'Introduction to JavaScript', 'content' => '<h2>The Language of the Web</h2><p>JavaScript runs in browsers and servers (Node.js).</p>'],
            ['title' => 'Variables and Types', 'content' => '<h2>Storing Data</h2><p>let, const, var. Strings, numbers, booleans, arrays, objects.</p>'],
            ['title' => 'Functions', 'content' => '<h2>Reusable Code</h2><p>Function declarations, expressions, arrow functions.</p>'],
        ]);
        $m2 = CourseModule::create(['course_id' => $course->id, 'title' => 'DOM Manipulation', 'order' => 2]);
        $this->createLessons($m2, [
            ['title' => 'The DOM Explained', 'content' => '<h2>Document Object Model</h2><p>HTML as a tree of objects JavaScript can modify.</p>'],
            ['title' => 'Selecting Elements', 'content' => '<h2>Find Elements</h2><p>getElementById, querySelector, querySelectorAll</p>'],
            ['title' => 'Events and Listeners', 'content' => '<h2>Respond to Users</h2><p>click, submit, keydown, addEventListener</p>'],
        ]);
    }

    private function createCourse15(): void
    {
        $course = Course::create([
            'title' => 'Remote Work & Productivity', 'slug' => 'remote-work-productivity',
            'description' => 'Thrive working from anywhere. Master remote collaboration tools, time management, and work-life balance strategies.',
            'objectives' => "- Set up productive workspace\n- Master remote tools\n- Communicate effectively\n- Maintain work-life balance",
            'price' => 19.99, 'is_free' => false, 'level' => 'beginner', 'duration_hours' => 3,
            'instructor_id' => $this->instructor?->id, 'is_published' => true,
        ]);
        $m1 = CourseModule::create(['course_id' => $course->id, 'title' => 'Remote Work Setup', 'order' => 1]);
        $this->createLessons($m1, [
            ['title' => 'Creating Your Workspace', 'content' => '<h2>Physical Setup</h2><ul><li>Dedicated space</li><li>Ergonomics</li><li>Lighting</li><li>Minimize distractions</li></ul>'],
            ['title' => 'Essential Tools', 'content' => '<h2>Software Stack</h2><ul><li>Slack/Teams</li><li>Zoom/Meet</li><li>Notion/Asana</li><li>Cloud storage</li></ul>'],
        ]);
        $m2 = CourseModule::create(['course_id' => $course->id, 'title' => 'Productivity Strategies', 'order' => 2]);
        $this->createLessons($m2, [
            ['title' => 'Time Management', 'content' => '<h2>Stay Productive</h2><ul><li>Time blocking</li><li>Pomodoro technique</li><li>Deep work sessions</li></ul>'],
            ['title' => 'Async Communication', 'content' => '<h2>Work Across Time Zones</h2><p>Document everything, clear written communication, reduce meetings.</p>'],
            ['title' => 'Work-Life Balance', 'content' => '<h2>Avoid Burnout</h2><ul><li>Set boundaries</li><li>Regular breaks</li><li>Disconnect rituals</li></ul>'],
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
