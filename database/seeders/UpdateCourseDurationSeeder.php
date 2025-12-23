<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Course;
use App\Models\CourseModule;
use App\Models\CourseLesson;

class UpdateCourseDurationSeeder extends Seeder
{
    public function run(): void
    {
        // Update all courses to have minimum 15 hours duration
        $courses = Course::all();

        foreach ($courses as $course) {
            // Set duration based on level
            $duration = match($course->level) {
                'beginner' => rand(15, 20),
                'intermediate' => rand(18, 25),
                'advanced' => rand(22, 30),
                default => 15,
            };

            $course->update(['duration_hours' => $duration]);

            // Add additional modules if course has less than 6 modules
            $existingModules = $course->modules()->count();

            if ($existingModules < 6) {
                $this->addAdditionalModules($course, $existingModules);
            }

            $this->command->info("Updated: {$course->title} - {$duration} hours");
        }
    }

    private function addAdditionalModules(Course $course, int $existingCount): void
    {
        $additionalModules = $this->getAdditionalModulesForCourse($course->slug);

        $order = $existingCount + 1;
        foreach ($additionalModules as $moduleData) {
            if ($order > 8) break; // Max 8 modules

            $module = CourseModule::create([
                'course_id' => $course->id,
                'title' => $moduleData['title'],
                'description' => $moduleData['description'],
                'order' => $order,
            ]);

            foreach ($moduleData['lessons'] as $lessonIndex => $lesson) {
                CourseLesson::create([
                    'module_id' => $module->id,
                    'title' => $lesson['title'],
                    'content' => $lesson['content'],
                    'type' => 'text',
                    'order' => $lessonIndex + 1,
                    'duration_minutes' => rand(20, 45),
                    'is_free_preview' => false,
                ]);
            }

            $order++;
        }
    }

    private function getAdditionalModulesForCourse(string $slug): array
    {
        // Generic additional modules that can apply to most courses
        $practiceModule = [
            'title' => 'Hands-On Practice & Exercises',
            'description' => 'Apply what you learned with practical exercises',
            'lessons' => [
                ['title' => 'Practice Exercise 1: Foundation Skills', 'content' => '<h2>Foundation Skills Practice</h2><p>In this exercise, you will apply the foundational concepts learned in the previous modules.</p><h3>Exercise Instructions</h3><ol><li>Review the key concepts from modules 1-2</li><li>Complete the practice tasks below</li><li>Document your process and results</li><li>Compare with the solution guide</li></ol><h3>Practice Tasks</h3><ul><li>Task 1: Apply basic concepts to a simple scenario</li><li>Task 2: Identify and solve common problems</li><li>Task 3: Create a basic implementation</li></ul><h3>Tips for Success</h3><p>Take your time with each task. Understanding is more important than speed.</p>'],
                ['title' => 'Practice Exercise 2: Intermediate Challenges', 'content' => '<h2>Intermediate Challenges</h2><p>Build on your foundation with more complex exercises.</p><h3>Challenge Set A</h3><ul><li>Combine multiple concepts to solve problems</li><li>Work with realistic scenarios</li><li>Debug and troubleshoot issues</li></ul><h3>Challenge Set B</h3><ul><li>Optimize your solutions</li><li>Implement best practices</li><li>Handle edge cases</li></ul><h3>Self-Assessment</h3><p>Rate your confidence on each challenge and identify areas for review.</p>'],
                ['title' => 'Practice Exercise 3: Advanced Application', 'content' => '<h2>Advanced Application</h2><p>Push your skills further with advanced exercises.</p><h3>Project-Based Exercise</h3><p>Complete a mini-project that incorporates all skills learned so far.</p><h3>Requirements</h3><ol><li>Plan your approach before starting</li><li>Implement core functionality</li><li>Add error handling and validation</li><li>Test thoroughly</li><li>Document your work</li></ol><h3>Evaluation Criteria</h3><ul><li>Correctness of implementation</li><li>Code/work quality</li><li>Problem-solving approach</li><li>Documentation quality</li></ul>'],
                ['title' => 'Practice Exercise 4: Real-World Scenarios', 'content' => '<h2>Real-World Scenarios</h2><p>Apply your skills to situations you might encounter professionally.</p><h3>Scenario 1: Business Context</h3><p>You are working on a project for a client who needs...</p><h3>Scenario 2: Technical Challenge</h3><p>Your team has encountered an issue that requires...</p><h3>Scenario 3: Optimization Task</h3><p>The current solution works but needs improvement in...</p><h3>Reflection Questions</h3><ul><li>What approach did you take and why?</li><li>What challenges did you face?</li><li>How would you improve your solution?</li></ul>'],
            ],
        ];

        $advancedTopicsModule = [
            'title' => 'Advanced Topics & Techniques',
            'description' => 'Deep dive into advanced concepts and methodologies',
            'lessons' => [
                ['title' => 'Advanced Concept 1: Deep Dive', 'content' => '<h2>Deep Dive into Advanced Concepts</h2><p>This lesson explores the more sophisticated aspects of the subject matter.</p><h3>Understanding Complexity</h3><p>As you progress, the concepts become more nuanced. Here we explore the subtleties that separate beginners from experts.</p><h3>Key Advanced Principles</h3><ul><li><strong>Principle 1:</strong> Understanding the underlying mechanisms</li><li><strong>Principle 2:</strong> Recognizing patterns and anti-patterns</li><li><strong>Principle 3:</strong> Making informed trade-off decisions</li><li><strong>Principle 4:</strong> Applying context-appropriate solutions</li></ul><h3>Common Pitfalls at the Advanced Level</h3><p>Even experienced practitioners make these mistakes. Learn to avoid them.</p>'],
                ['title' => 'Advanced Concept 2: Expert Techniques', 'content' => '<h2>Expert-Level Techniques</h2><p>Techniques used by industry experts to achieve superior results.</p><h3>Technique Overview</h3><ol><li>Advanced optimization strategies</li><li>Scalability considerations</li><li>Performance tuning methods</li><li>Quality assurance approaches</li></ol><h3>When to Apply These Techniques</h3><p>Not every situation requires advanced techniques. Learn when to use them.</p><h3>Case Study</h3><p>See how a real expert approached a complex problem using these techniques.</p>'],
                ['title' => 'Advanced Concept 3: Edge Cases & Exceptions', 'content' => '<h2>Handling Edge Cases</h2><p>The mark of an expert is handling unusual situations gracefully.</p><h3>Common Edge Cases</h3><ul><li>Boundary conditions</li><li>Unexpected inputs</li><li>Resource constraints</li><li>Concurrent operations</li><li>Error cascades</li></ul><h3>Strategies for Handling Edge Cases</h3><ol><li>Anticipate potential issues</li><li>Implement defensive measures</li><li>Create fallback mechanisms</li><li>Log and monitor anomalies</li></ol><h3>Testing Edge Cases</h3><p>How to systematically test for and verify edge case handling.</p>'],
                ['title' => 'Advanced Concept 4: Integration & Ecosystem', 'content' => '<h2>Integration with the Broader Ecosystem</h2><p>Understanding how this subject fits into the larger picture.</p><h3>Ecosystem Overview</h3><p>No skill exists in isolation. See how this connects to related areas.</p><h3>Integration Points</h3><ul><li>Upstream dependencies</li><li>Downstream consumers</li><li>Parallel systems</li><li>External services</li></ul><h3>Best Practices for Integration</h3><ol><li>Define clear interfaces</li><li>Maintain compatibility</li><li>Version appropriately</li><li>Document thoroughly</li></ol>'],
            ],
        ];

        $caseStudiesModule = [
            'title' => 'Case Studies & Real-World Examples',
            'description' => 'Learn from real-world implementations and success stories',
            'lessons' => [
                ['title' => 'Case Study 1: Startup Success Story', 'content' => '<h2>Case Study: Startup Success</h2><h3>Background</h3><p>A small startup with limited resources needed to implement a solution quickly and efficiently.</p><h3>The Challenge</h3><ul><li>Limited budget and timeline</li><li>Small team with varied skill levels</li><li>Need for rapid iteration</li><li>Scalability concerns for future growth</li></ul><h3>The Approach</h3><p>The team decided to focus on core functionality first, then iterate based on user feedback.</p><h3>Results</h3><ul><li>Launched MVP in 3 months</li><li>Achieved product-market fit</li><li>Scaled to 10,000 users</li><li>Secured Series A funding</li></ul><h3>Key Lessons</h3><ol><li>Start simple and iterate</li><li>Focus on user needs</li><li>Build for scale from day one</li></ol>'],
                ['title' => 'Case Study 2: Enterprise Implementation', 'content' => '<h2>Case Study: Enterprise Scale</h2><h3>Background</h3><p>A Fortune 500 company needed to modernize their existing systems.</p><h3>The Challenge</h3><ul><li>Legacy systems in place</li><li>Thousands of users to migrate</li><li>Zero downtime requirement</li><li>Regulatory compliance needs</li></ul><h3>The Solution</h3><p>A phased migration approach with parallel systems during transition.</p><h3>Implementation Timeline</h3><ol><li>Phase 1: Assessment and planning (3 months)</li><li>Phase 2: Pilot deployment (2 months)</li><li>Phase 3: Gradual rollout (6 months)</li><li>Phase 4: Legacy decommission (3 months)</li></ol><h3>Outcomes</h3><ul><li>Successful migration with 99.9% uptime</li><li>40% cost reduction</li><li>Improved user satisfaction</li></ul>'],
                ['title' => 'Case Study 3: Problem-Solving Under Pressure', 'content' => '<h2>Case Study: Crisis Management</h2><h3>The Situation</h3><p>A critical system failure during peak business hours required immediate action.</p><h3>Initial Response</h3><ol><li>Incident declared and team assembled</li><li>Communication channels established</li><li>Initial diagnosis begun</li></ol><h3>Root Cause Analysis</h3><p>Through systematic investigation, the team discovered...</p><h3>Resolution Steps</h3><ul><li>Immediate mitigation implemented</li><li>Root cause addressed</li><li>Preventive measures added</li><li>Post-mortem conducted</li></ul><h3>Lessons Learned</h3><ol><li>Importance of monitoring and alerting</li><li>Value of documented procedures</li><li>Need for regular drills</li></ol>'],
                ['title' => 'Case Study 4: Innovation & Transformation', 'content' => '<h2>Case Study: Digital Transformation</h2><h3>Company Profile</h3><p>A traditional company looking to embrace digital innovation.</p><h3>Transformation Goals</h3><ul><li>Modernize customer experience</li><li>Streamline operations</li><li>Enable data-driven decisions</li><li>Foster innovation culture</li></ul><h3>Implementation Journey</h3><ol><li>Executive alignment and vision setting</li><li>Technology infrastructure upgrade</li><li>Process redesign</li><li>Culture and skill development</li><li>Continuous improvement programs</li></ol><h3>Transformation Results</h3><ul><li>Revenue growth of 25%</li><li>Customer satisfaction up 35%</li><li>Employee engagement improved</li><li>Time-to-market reduced by 50%</li></ul>'],
            ],
        ];

        $toolsResourcesModule = [
            'title' => 'Tools, Resources & Best Practices',
            'description' => 'Essential tools and resources for professionals',
            'lessons' => [
                ['title' => 'Essential Tools Overview', 'content' => '<h2>Essential Tools for Professionals</h2><p>A comprehensive guide to the tools you need to succeed.</p><h3>Category 1: Core Tools</h3><ul><li><strong>Tool A:</strong> Industry standard for basic tasks</li><li><strong>Tool B:</strong> Advanced capabilities for complex work</li><li><strong>Tool C:</strong> Collaboration and team features</li></ul><h3>Category 2: Productivity Tools</h3><ul><li>Project management solutions</li><li>Documentation platforms</li><li>Communication tools</li></ul><h3>Category 3: Specialized Tools</h3><ul><li>Analysis and reporting</li><li>Automation platforms</li><li>Testing and validation</li></ul><h3>Tool Selection Criteria</h3><ol><li>Feature requirements</li><li>Team size and structure</li><li>Budget considerations</li><li>Integration needs</li></ol>'],
                ['title' => 'Setting Up Your Workspace', 'content' => '<h2>Professional Workspace Setup</h2><p>Configure your environment for maximum productivity.</p><h3>Hardware Recommendations</h3><ul><li>Computing requirements</li><li>Display considerations</li><li>Input devices</li><li>Accessories</li></ul><h3>Software Configuration</h3><ol><li>Operating system setup</li><li>Essential applications</li><li>Customization and preferences</li><li>Backup and security</li></ol><h3>Environment Optimization</h3><ul><li>Ergonomic considerations</li><li>Distraction management</li><li>Workflow organization</li></ul><h3>Maintenance Schedule</h3><p>Keep your workspace running smoothly with regular maintenance.</p>'],
                ['title' => 'Industry Best Practices', 'content' => '<h2>Industry Best Practices</h2><p>Standards and practices adopted by leading professionals.</p><h3>Quality Standards</h3><ul><li>Documentation requirements</li><li>Review processes</li><li>Testing standards</li><li>Compliance considerations</li></ul><h3>Process Best Practices</h3><ol><li>Planning and estimation</li><li>Execution and tracking</li><li>Review and feedback</li><li>Continuous improvement</li></ol><h3>Communication Best Practices</h3><ul><li>Stakeholder management</li><li>Progress reporting</li><li>Issue escalation</li><li>Knowledge sharing</li></ul><h3>Professional Development</h3><p>Continuous learning and skill enhancement strategies.</p>'],
                ['title' => 'Resources for Continued Learning', 'content' => '<h2>Continued Learning Resources</h2><p>Your learning journey does not end with this course.</p><h3>Official Documentation</h3><ul><li>Reference guides and manuals</li><li>API documentation</li><li>Release notes and changelogs</li></ul><h3>Community Resources</h3><ul><li>Forums and discussion boards</li><li>User groups and meetups</li><li>Open source projects</li><li>Social media communities</li></ul><h3>Educational Resources</h3><ul><li>Advanced courses and certifications</li><li>Books and publications</li><li>Conferences and workshops</li><li>Podcasts and videos</li></ul><h3>Building Your Learning Plan</h3><ol><li>Assess your current level</li><li>Identify growth areas</li><li>Set learning goals</li><li>Create a schedule</li><li>Track progress</li></ol>'],
            ],
        ];

        $projectModule = [
            'title' => 'Capstone Project',
            'description' => 'Apply everything you learned in a comprehensive project',
            'lessons' => [
                ['title' => 'Project Introduction & Requirements', 'content' => '<h2>Capstone Project Overview</h2><p>This comprehensive project will test all the skills you have developed throughout this course.</p><h3>Project Objectives</h3><ul><li>Demonstrate mastery of course concepts</li><li>Apply skills to a realistic scenario</li><li>Create a portfolio-worthy deliverable</li><li>Practice professional workflows</li></ul><h3>Project Requirements</h3><ol><li>Complete all specified deliverables</li><li>Follow best practices covered in the course</li><li>Document your process and decisions</li><li>Submit by the deadline</li></ol><h3>Evaluation Criteria</h3><ul><li>Completeness: All requirements met</li><li>Quality: Professional standard work</li><li>Process: Proper methodology followed</li><li>Documentation: Clear and comprehensive</li></ul>'],
                ['title' => 'Project Planning & Design', 'content' => '<h2>Planning Your Capstone Project</h2><p>Good planning is essential for project success.</p><h3>Phase 1: Discovery</h3><ul><li>Understand the requirements fully</li><li>Identify constraints and assumptions</li><li>Research similar solutions</li><li>Gather necessary resources</li></ul><h3>Phase 2: Design</h3><ol><li>Create high-level architecture</li><li>Detail component specifications</li><li>Plan data structures and flows</li><li>Design user interfaces if applicable</li></ol><h3>Phase 3: Planning</h3><ul><li>Break down into tasks</li><li>Estimate effort for each task</li><li>Create timeline and milestones</li><li>Identify risks and mitigation</li></ul><h3>Deliverable: Project Plan</h3><p>Submit your project plan for review before proceeding.</p>'],
                ['title' => 'Project Implementation', 'content' => '<h2>Implementing Your Project</h2><p>Execute your plan and bring your project to life.</p><h3>Implementation Guidelines</h3><ol><li>Follow your plan but adapt as needed</li><li>Implement core functionality first</li><li>Test as you go</li><li>Document your progress</li></ol><h3>Quality Checkpoints</h3><ul><li>Checkpoint 1: Core functionality complete</li><li>Checkpoint 2: Full implementation done</li><li>Checkpoint 3: Testing and refinement</li><li>Checkpoint 4: Documentation complete</li></ul><h3>Common Challenges</h3><ul><li>Scope creep - stick to requirements</li><li>Time management - follow your schedule</li><li>Technical difficulties - seek help early</li></ul><h3>Getting Unstuck</h3><p>Strategies for overcoming obstacles during implementation.</p>'],
                ['title' => 'Project Review & Presentation', 'content' => '<h2>Finalizing Your Project</h2><p>Complete your project with professional polish.</p><h3>Final Review Checklist</h3><ul><li>All requirements implemented</li><li>Testing completed successfully</li><li>Documentation is complete</li><li>Code/work is clean and organized</li></ul><h3>Creating Your Presentation</h3><ol><li>Summarize the project and objectives</li><li>Demonstrate key features</li><li>Discuss challenges and solutions</li><li>Share lessons learned</li><li>Outline future improvements</li></ol><h3>Submission Requirements</h3><ul><li>Project deliverables</li><li>Documentation package</li><li>Presentation slides or video</li><li>Self-assessment form</li></ul><h3>Next Steps</h3><p>How to leverage this project for your portfolio and career.</p>'],
            ],
        ];

        $careerModule = [
            'title' => 'Career Development & Industry Insights',
            'description' => 'Prepare for professional success in this field',
            'lessons' => [
                ['title' => 'Career Paths & Opportunities', 'content' => '<h2>Career Opportunities in This Field</h2><p>Explore the various career paths available to you.</p><h3>Entry-Level Positions</h3><ul><li>Junior roles and responsibilities</li><li>Expected skills and qualifications</li><li>Typical salary ranges</li><li>Growth opportunities</li></ul><h3>Mid-Level Careers</h3><ul><li>Specialist positions</li><li>Team lead opportunities</li><li>Cross-functional roles</li></ul><h3>Senior & Leadership Roles</h3><ul><li>Senior individual contributor</li><li>Management track</li><li>Executive positions</li><li>Consulting and advisory</li></ul><h3>Alternative Paths</h3><ul><li>Freelancing and consulting</li><li>Entrepreneurship</li><li>Teaching and training</li><li>Research and development</li></ul>'],
                ['title' => 'Building Your Portfolio', 'content' => '<h2>Creating a Strong Portfolio</h2><p>Your portfolio is your professional calling card.</p><h3>What to Include</h3><ul><li>Your best work samples</li><li>Project descriptions and context</li><li>Your role and contributions</li><li>Results and outcomes</li></ul><h3>Portfolio Best Practices</h3><ol><li>Quality over quantity</li><li>Show variety of skills</li><li>Keep it current</li><li>Make it easy to navigate</li></ol><h3>Platform Options</h3><ul><li>Personal website</li><li>Professional networks (LinkedIn)</li><li>Industry-specific platforms</li><li>GitHub/repositories for technical work</li></ul><h3>Portfolio Review Checklist</h3><p>Ensure your portfolio makes the right impression.</p>'],
                ['title' => 'Interview Preparation', 'content' => '<h2>Ace Your Interviews</h2><p>Prepare thoroughly to make a great impression.</p><h3>Types of Interviews</h3><ul><li>Phone/video screening</li><li>Technical assessments</li><li>Behavioral interviews</li><li>Case studies and presentations</li><li>Panel interviews</li></ul><h3>Common Questions</h3><ol><li>Tell me about yourself</li><li>Why are you interested in this role?</li><li>Describe a challenging project</li><li>How do you handle conflict?</li><li>Where do you see yourself in 5 years?</li></ol><h3>Technical Preparation</h3><ul><li>Review fundamentals</li><li>Practice problem-solving</li><li>Prepare to discuss your projects</li></ul><h3>Questions to Ask</h3><p>Thoughtful questions show your interest and engagement.</p>'],
                ['title' => 'Continuing Professional Development', 'content' => '<h2>Growing Your Career</h2><p>Success requires continuous growth and learning.</p><h3>Staying Current</h3><ul><li>Industry publications and blogs</li><li>Conferences and events</li><li>Professional associations</li><li>Online communities</li></ul><h3>Certifications & Credentials</h3><ul><li>Industry certifications</li><li>Vendor certifications</li><li>Academic credentials</li><li>Micro-credentials and badges</li></ul><h3>Networking Strategies</h3><ol><li>Build genuine relationships</li><li>Contribute to communities</li><li>Attend events regularly</li><li>Maintain your network</li></ol><h3>Mentorship</h3><ul><li>Finding a mentor</li><li>Being a good mentee</li><li>Becoming a mentor yourself</li></ul>'],
            ],
        ];

        return [$practiceModule, $advancedTopicsModule, $caseStudiesModule, $toolsResourcesModule, $projectModule, $careerModule];
    }
}
