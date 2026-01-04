<?php

namespace Database\Seeders;

use App\Models\CareerTip;
use Illuminate\Database\Seeder;

class CareerTipsSeeder extends Seeder
{
    public function run(): void
    {
        $tips = [
            // CV & Resume Tips
            [
                'title' => 'How to Write a Winning CV in 2025',
                'slug' => 'how-to-write-winning-cv-2025',
                'excerpt' => 'Learn the essential elements of a modern CV that will get you noticed by employers and land you more interviews.',
                'content' => '<h2>The Modern CV: What Employers Want</h2>
<p>In today\'s competitive job market, your CV needs to stand out. Here are the key elements of a winning CV:</p>

<h3>1. Start with a Strong Personal Statement</h3>
<p>Your personal statement should be 2-3 sentences that summarize your experience, skills, and career goals. Make it specific to the role you\'re applying for.</p>

<h3>2. Highlight Achievements, Not Just Duties</h3>
<p>Instead of listing job responsibilities, focus on what you achieved. Use numbers where possible:</p>
<ul>
<li>"Increased sales by 25% in Q4 2024"</li>
<li>"Managed a team of 5 developers"</li>
<li>"Reduced customer complaints by 40%"</li>
</ul>

<h3>3. Keep It Concise</h3>
<p>Your CV should be no longer than 2 pages. Recruiters spend an average of 7 seconds on an initial CV scan.</p>

<h3>4. Use a Clean, Professional Format</h3>
<p>Choose a modern but professional template. Avoid excessive colors or graphics unless you\'re in a creative field.</p>

<h3>5. Tailor It for Each Application</h3>
<p>Customize your CV for each job by matching keywords from the job description.</p>

<h3>Pro Tip</h3>
<p>Save your CV as a PDF to preserve formatting across different devices and systems.</p>',
                'category' => 'cv',
                'reading_time' => 5,
                'is_featured' => true,
            ],
            [
                'title' => '10 Common CV Mistakes to Avoid',
                'slug' => '10-common-cv-mistakes-avoid',
                'excerpt' => 'Avoid these common CV mistakes that could be costing you job interviews.',
                'content' => '<h2>Don\'t Let These Mistakes Cost You the Job</h2>

<h3>1. Spelling and Grammar Errors</h3>
<p>Nothing says "I don\'t pay attention to detail" like a CV full of typos. Always proofread multiple times and have someone else review it.</p>

<h3>2. Using an Unprofessional Email Address</h3>
<p>coolguy2000@email.com won\'t impress recruiters. Use a professional format: firstname.lastname@email.com</p>

<h3>3. Including Irrelevant Information</h3>
<p>Your hobbies are only relevant if they relate to the job. Focus on skills and experience that matter.</p>

<h3>4. Too Long or Too Short</h3>
<p>One page is fine for entry-level; two pages maximum for experienced professionals. Never go over three pages.</p>

<h3>5. Missing Contact Information</h3>
<p>Always include your phone number, email, and LinkedIn profile (if you have one).</p>

<h3>6. Using "I" Statements</h3>
<p>Instead of "I managed a team," write "Managed a team of 5 developers."</p>

<h3>7. Lying or Exaggerating</h3>
<p>Never lie on your CV. Background checks can expose falsehoods and cost you the job.</p>

<h3>8. Poor Formatting</h3>
<p>Inconsistent fonts, spacing, and alignment make your CV look unprofessional.</p>

<h3>9. Not Quantifying Achievements</h3>
<p>Numbers make your achievements concrete: "Increased revenue by 30%" is better than "Increased revenue."</p>

<h3>10. Including References on the CV</h3>
<p>"References available upon request" is assumed. Save space for more important information.</p>',
                'category' => 'cv',
                'reading_time' => 4,
                'is_featured' => false,
            ],

            // Interview Preparation
            [
                'title' => 'How to Ace Your Job Interview: Complete Guide',
                'slug' => 'ace-job-interview-complete-guide',
                'excerpt' => 'Master the art of job interviews with these proven strategies for success.',
                'content' => '<h2>Prepare to Succeed</h2>

<h3>Before the Interview</h3>
<h4>Research the Company</h4>
<p>Know their products, services, mission, recent news, and competitors. Check their website, LinkedIn, and recent press releases.</p>

<h4>Understand the Role</h4>
<p>Review the job description carefully. Prepare examples of how your experience matches each requirement.</p>

<h4>Prepare Your Answers</h4>
<p>Practice answering common questions using the STAR method (Situation, Task, Action, Result).</p>

<h3>During the Interview</h3>
<h4>First Impressions Matter</h4>
<ul>
<li>Arrive 10-15 minutes early</li>
<li>Dress appropriately for the company culture</li>
<li>Bring copies of your CV and a notepad</li>
<li>Greet everyone with a firm handshake and smile</li>
</ul>

<h4>Body Language</h4>
<ul>
<li>Maintain eye contact</li>
<li>Sit up straight</li>
<li>Avoid crossing your arms</li>
<li>Nod to show you\'re listening</li>
</ul>

<h4>Answer Strategically</h4>
<ul>
<li>Listen to the full question before answering</li>
<li>Take a moment to think if needed</li>
<li>Keep answers concise but complete</li>
<li>Use specific examples from your experience</li>
</ul>

<h3>After the Interview</h3>
<p>Send a thank-you email within 24 hours. Briefly reiterate your interest and qualifications.</p>',
                'category' => 'interview',
                'reading_time' => 7,
                'is_featured' => true,
            ],
            [
                'title' => 'Top 20 Interview Questions and How to Answer Them',
                'slug' => 'top-20-interview-questions-answers',
                'excerpt' => 'Be prepared for the most common interview questions with these expert answers.',
                'content' => '<h2>Most Common Interview Questions</h2>

<h3>1. "Tell me about yourself"</h3>
<p>Keep it professional and relevant. Summarize your background, key achievements, and why you\'re interested in this role.</p>

<h3>2. "Why do you want this job?"</h3>
<p>Connect your skills and goals to the role and company. Show you\'ve done your research.</p>

<h3>3. "What is your greatest strength?"</h3>
<p>Choose a strength relevant to the job and provide a specific example.</p>

<h3>4. "What is your greatest weakness?"</h3>
<p>Be honest but strategic. Choose a real weakness and explain how you\'re working to improve it.</p>

<h3>5. "Where do you see yourself in 5 years?"</h3>
<p>Show ambition while demonstrating commitment to the company. Align your goals with potential growth within the organization.</p>

<h3>6. "Why are you leaving your current job?"</h3>
<p>Stay positive. Focus on seeking new challenges, growth opportunities, or better alignment with your career goals.</p>

<h3>7. "Tell me about a challenge you faced and how you overcame it"</h3>
<p>Use the STAR method: Situation, Task, Action, Result.</p>

<h3>8. "What are your salary expectations?"</h3>
<p>Research market rates beforehand. Give a range based on your research and experience.</p>

<h3>9. "Do you have any questions for us?"</h3>
<p>Always have questions prepared! Ask about team culture, growth opportunities, or current challenges the team faces.</p>

<h3>10. "Why should we hire you?"</h3>
<p>Summarize your key qualifications and what unique value you bring to the role.</p>',
                'category' => 'interview',
                'reading_time' => 8,
                'is_featured' => false,
            ],

            // Job Search Strategies
            [
                'title' => 'Job Search Strategies That Actually Work',
                'slug' => 'job-search-strategies-that-work',
                'excerpt' => 'Discover proven job search techniques to find your dream job faster.',
                'content' => '<h2>Smart Strategies for Job Seekers</h2>

<h3>1. Optimize Your Online Presence</h3>
<p>Your LinkedIn profile should be complete and keyword-optimized. Recruiters actively search for candidates on LinkedIn.</p>

<h3>2. Network Strategically</h3>
<p>80% of jobs are filled through networking. Attend industry events, join professional groups, and connect with people in your field.</p>

<h3>3. Apply Strategically, Not Randomly</h3>
<p>Quality over quantity. Tailor each application to the specific job rather than sending generic applications.</p>

<h3>4. Use Job Alerts</h3>
<p>Set up alerts on job boards to be notified immediately when relevant positions open.</p>

<h3>5. Follow Up</h3>
<p>If you haven\'t heard back after 1-2 weeks, send a polite follow-up email expressing continued interest.</p>

<h3>6. Consider Working with Recruiters</h3>
<p>Recruiters have access to jobs that aren\'t publicly advertised and can advocate for you with hiring managers.</p>

<h3>7. Track Your Applications</h3>
<p>Keep a spreadsheet of every job you apply to, including dates, contacts, and follow-up actions.</p>

<h3>8. Stay Positive and Persistent</h3>
<p>Job searching can be challenging. Set daily goals, take breaks, and celebrate small wins.</p>',
                'category' => 'job-search',
                'reading_time' => 5,
                'is_featured' => false,
            ],

            // Career Growth
            [
                'title' => 'How to Negotiate Your Salary Like a Pro',
                'slug' => 'negotiate-salary-like-pro',
                'excerpt' => 'Learn effective salary negotiation techniques to maximize your earning potential.',
                'content' => '<h2>Get Paid What You\'re Worth</h2>

<h3>Do Your Research</h3>
<p>Before any negotiation, know the market rate for your role, experience level, and location. Use salary comparison tools and industry surveys.</p>

<h3>Know Your Value</h3>
<p>List your achievements, skills, and unique qualifications. Be prepared to articulate why you deserve the salary you\'re asking for.</p>

<h3>Let Them Make the First Offer</h3>
<p>When possible, let the employer state the salary range first. This gives you a starting point for negotiation.</p>

<h3>Never Accept the First Offer</h3>
<p>Most employers expect some negotiation. A polite counter-offer is professional and expected.</p>

<h3>Consider the Whole Package</h3>
<p>Salary is just one part of compensation. Consider:</p>
<ul>
<li>Bonuses and incentives</li>
<li>Health insurance and benefits</li>
<li>Retirement contributions</li>
<li>Vacation and flexible working</li>
<li>Professional development opportunities</li>
</ul>

<h3>Practice Your Pitch</h3>
<p>Rehearse your negotiation conversation. Be confident but not aggressive. Focus on your value, not your needs.</p>

<h3>Get It in Writing</h3>
<p>Once you reach an agreement, ensure everything is documented in your offer letter before you accept.</p>',
                'category' => 'career-growth',
                'reading_time' => 6,
                'is_featured' => true,
            ],

            // Workplace Skills
            [
                'title' => 'Essential Soft Skills Every Employee Needs',
                'slug' => 'essential-soft-skills-employee-needs',
                'excerpt' => 'Develop these crucial soft skills to advance your career and stand out at work.',
                'content' => '<h2>Skills That Set You Apart</h2>

<h3>1. Communication</h3>
<p>Clear communication - both written and verbal - is essential in every role. Practice active listening and be concise in your messages.</p>

<h3>2. Teamwork</h3>
<p>Most jobs require collaboration. Be a reliable team player who supports colleagues and contributes positively to group projects.</p>

<h3>3. Problem-Solving</h3>
<p>Employers value people who can analyze problems and propose solutions. Develop your critical thinking skills.</p>

<h3>4. Adaptability</h3>
<p>The business world changes rapidly. Show you can adapt to new situations, learn new skills, and embrace change.</p>

<h3>5. Time Management</h3>
<p>Prioritize tasks, meet deadlines, and manage your workload effectively. Use tools and techniques that work for you.</p>

<h3>6. Leadership</h3>
<p>Even if you\'re not a manager, leadership skills are valuable. Take initiative, mentor others, and lead projects when appropriate.</p>

<h3>7. Emotional Intelligence</h3>
<p>Understand and manage your emotions, and be aware of how your actions affect others. EQ is often more important than IQ.</p>

<h3>8. Networking</h3>
<p>Build and maintain professional relationships. Your network can open doors throughout your career.</p>',
                'category' => 'workplace',
                'reading_time' => 5,
                'is_featured' => false,
            ],

            // Remote Work
            [
                'title' => 'Working from Home: Tips for Maximum Productivity',
                'slug' => 'working-from-home-productivity-tips',
                'excerpt' => 'Master remote work with these practical tips for staying focused and productive.',
                'content' => '<h2>Thrive in Your Home Office</h2>

<h3>Create a Dedicated Workspace</h3>
<p>Set up a specific area for work. This helps your brain switch into "work mode" and maintains work-life boundaries.</p>

<h3>Establish a Routine</h3>
<p>Start work at the same time each day. Have a morning routine that signals the start of your workday.</p>

<h3>Dress for Success</h3>
<p>While you don\'t need to wear a suit, getting dressed helps put you in a professional mindset.</p>

<h3>Take Regular Breaks</h3>
<p>Use techniques like the Pomodoro method: 25 minutes of focused work followed by a 5-minute break.</p>

<h3>Communicate Proactively</h3>
<p>Remote work requires extra effort in communication. Update your team regularly and be responsive to messages.</p>

<h3>Avoid Distractions</h3>
<ul>
<li>Turn off personal notifications during work hours</li>
<li>Use website blockers if needed</li>
<li>Set boundaries with family members</li>
</ul>

<h3>Don\'t Forget Social Interaction</h3>
<p>Combat isolation by scheduling virtual coffee chats with colleagues and maintaining social connections outside work.</p>

<h3>Know When to Stop</h3>
<p>One of the biggest challenges of remote work is overworking. Set a clear end time and stick to it.</p>',
                'category' => 'remote-work',
                'reading_time' => 5,
                'is_featured' => false,
            ],
        ];

        foreach ($tips as $tip) {
            CareerTip::updateOrCreate(
                ['slug' => $tip['slug']],
                $tip
            );
        }

        $this->command->info('Career tips seeded successfully!');
    }
}
