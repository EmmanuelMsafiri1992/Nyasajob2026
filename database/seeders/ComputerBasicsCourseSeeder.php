<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\CourseModule;
use App\Models\CourseLesson;
use App\Models\LessonExercise;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ComputerBasicsCourseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Create the Computer Basics course
        $course = Course::create([
            'title' => 'Computer Basics - From Beginner to Advanced',
            'slug' => 'computer-basics-beginner-to-advanced',
            'description' => 'A comprehensive course covering computer fundamentals from basic concepts to advanced topics. Perfect for anyone starting their journey in computing or looking to strengthen their foundational knowledge.',
            'objectives' => '- Understand computer hardware and software components
- Master operating system basics
- Learn file management and organization
- Understand networking fundamentals
- Practice keyboard shortcuts and productivity tips
- Explore advanced topics like command line and automation',
            'price' => 0,
            'is_free' => true,
            'level' => 'beginner',
            'duration_hours' => 20,
            'is_published' => true,
            'instructor_id' => 1, // Admin user
            'enrollment_count' => 0,
            'rating' => 0
        ]);

        // Module 1: Introduction to Computers (Beginner)
        $module1 = CourseModule::create([
            'course_id' => $course->id,
            'title' => 'Introduction to Computers',
            'description' => 'Learn the basic components of a computer system and understand how they work together.',
            'order' => 1
        ]);

        CourseLesson::create([
            'module_id' => $module1->id,
            'title' => 'What is a Computer?',
            'content' => '<h2>What is a Computer?</h2>
<p>A computer is an electronic device that processes data according to a set of instructions called a program. It can store, retrieve, and process data.</p>

<h3>Key Characteristics:</h3>
<ul>
    <li><strong>Speed:</strong> Computers can perform millions of calculations per second</li>
    <li><strong>Accuracy:</strong> Computers are highly accurate when given correct instructions</li>
    <li><strong>Storage:</strong> Can store vast amounts of data</li>
    <li><strong>Automation:</strong> Can execute tasks automatically</li>
</ul>

<h3>Types of Computers:</h3>
<ol>
    <li>Desktop Computers</li>
    <li>Laptop Computers</li>
    <li>Tablets</li>
    <li>Smartphones</li>
    <li>Servers</li>
</ol>',
            'type' => 'text',
            'order' => 1,
            'is_free_preview' => true
        ]);

        CourseLesson::create([
            'module_id' => $module1->id,
            'title' => 'Computer Hardware Components',
            'content' => '<h2>Computer Hardware Components</h2>
<p>Hardware refers to the physical components of a computer that you can touch and see.</p>

<h3>Main Components:</h3>
<ul>
    <li><strong>CPU (Central Processing Unit):</strong> The brain of the computer that executes instructions</li>
    <li><strong>RAM (Random Access Memory):</strong> Temporary memory used while programs are running</li>
    <li><strong>Hard Drive/SSD:</strong> Permanent storage for files and programs</li>
    <li><strong>Motherboard:</strong> Main circuit board connecting all components</li>
    <li><strong>Power Supply:</strong> Converts electrical power for the computer</li>
    <li><strong>Graphics Card:</strong> Processes and displays visual information</li>
</ul>

<h3>Input Devices:</h3>
<p>Keyboard, Mouse, Scanner, Microphone, Webcam</p>

<h3>Output Devices:</h3>
<p>Monitor, Printer, Speakers, Headphones</p>',
            'type' => 'text',
            'order' => 2,
            'is_free_preview' => true
        ]);

        CourseLesson::create([
            'module_id' => $module1->id,
            'title' => 'Software and Operating Systems',
            'content' => '<h2>Software and Operating Systems</h2>
<p>Software is the set of instructions that tells the computer hardware what to do.</p>

<h3>Types of Software:</h3>
<ol>
    <li><strong>System Software:</strong>
        <ul>
            <li>Operating Systems (Windows, macOS, Linux)</li>
            <li>Device Drivers</li>
            <li>Utilities</li>
        </ul>
    </li>
    <li><strong>Application Software:</strong>
        <ul>
            <li>Word processors (Microsoft Word, Google Docs)</li>
            <li>Web browsers (Chrome, Firefox, Edge)</li>
            <li>Media players</li>
            <li>Games</li>
        </ul>
    </li>
</ol>

<h3>What is an Operating System?</h3>
<p>An operating system (OS) is software that manages computer hardware and software resources. It provides a user interface and allows you to run applications.</p>

<h4>Popular Operating Systems:</h4>
<ul>
    <li>Windows 10/11</li>
    <li>macOS</li>
    <li>Linux (Ubuntu, Fedora)</li>
    <li>Android</li>
    <li>iOS</li>
</ul>',
            'type' => 'text',
            'order' => 3
        ]);

        // Module 2: Using Your Computer (Beginner to Intermediate)
        $module2 = CourseModule::create([
            'course_id' => $course->id,
            'title' => 'Using Your Computer Effectively',
            'description' => 'Master the essential skills for daily computer use, file management, and productivity.',
            'order' => 2
        ]);

        CourseLesson::create([
            'module_id' => $module2->id,
            'title' => 'The Desktop and User Interface',
            'content' => '<h2>Understanding the Desktop</h2>
<p>The desktop is the main screen you see after logging into your computer.</p>

<h3>Desktop Elements:</h3>
<ul>
    <li><strong>Icons:</strong> Small pictures representing files, folders, or programs</li>
    <li><strong>Taskbar:</strong> Bar at the bottom showing open programs and system tray</li>
    <li><strong>Start Menu:</strong> Access to programs and settings</li>
    <li><strong>System Tray:</strong> Shows running background programs and system status</li>
</ul>

<h3>Window Management:</h3>
<ul>
    <li>Minimize: Hides window to taskbar</li>
    <li>Maximize: Expands window to full screen</li>
    <li>Close: Exits the program</li>
    <li>Resize: Drag edges to change window size</li>
</ul>

<h3>Basic Navigation:</h3>
<ol>
    <li>Click to select</li>
    <li>Double-click to open</li>
    <li>Right-click for context menu</li>
    <li>Drag and drop to move</li>
</ol>',
            'type' => 'text',
            'order' => 1
        ]);

        $lesson = CourseLesson::create([
            'module_id' => $module2->id,
            'title' => 'File Management Basics',
            'content' => '<h2>Working with Files and Folders</h2>
<p>Learn how to organize, create, and manage your files effectively.</p>

<h3>What are Files and Folders?</h3>
<ul>
    <li><strong>File:</strong> A document, image, video, or program stored on your computer</li>
    <li><strong>Folder:</strong> A container for organizing files</li>
</ul>

<h3>Common File Operations:</h3>
<pre><code>
1. Creating a new folder:
   - Right-click on desktop or in File Explorer
   - Select "New" → "Folder"
   - Type a name and press Enter

2. Copying files:
   - Select file
   - Press Ctrl+C (Copy)
   - Navigate to destination
   - Press Ctrl+V (Paste)

3. Moving files:
   - Select file
   - Press Ctrl+X (Cut)
   - Navigate to destination
   - Press Ctrl+V (Paste)

4. Deleting files:
   - Select file
   - Press Delete key
   - Files go to Recycle Bin
   - Can be restored if needed
</code></pre>

<h3>File Extensions:</h3>
<ul>
    <li>.txt - Text file</li>
    <li>.docx - Word document</li>
    <li>.xlsx - Excel spreadsheet</li>
    <li>.jpg, .png - Image files</li>
    <li>.mp4 - Video file</li>
    <li>.mp3 - Audio file</li>
</ul>',
            'type' => 'text',
            'order' => 2
        ]);

        // Add exercise for file management
        LessonExercise::create([
            'lesson_id' => $lesson->id,
            'title' => 'File Organization Challenge',
            'question' => 'Practice organizing files by creating a proper folder structure. Which folder structure is best for organizing personal documents?',
            'code_template' => '',
            'solution' => 'Documents/
  Personal/
    Finance/
    Health/
    Education/
  Work/
    Projects/
    Reports/
  Media/
    Photos/
    Videos/',
            'test_cases' => json_encode([
                'Structure should have main categories',
                'Subcategories should be logical',
                'Naming should be clear and consistent'
            ]),
            'difficulty' => 'easy',
            'points' => 10
        ]);

        CourseLesson::create([
            'module_id' => $module2->id,
            'title' => 'Keyboard Shortcuts and Productivity',
            'content' => '<h2>Essential Keyboard Shortcuts</h2>
<p>Learn keyboard shortcuts to work faster and more efficiently.</p>

<h3>Universal Shortcuts (Windows):</h3>
<pre><code>
Ctrl + C = Copy
Ctrl + X = Cut
Ctrl + V = Paste
Ctrl + Z = Undo
Ctrl + Y = Redo
Ctrl + A = Select All
Ctrl + F = Find
Ctrl + S = Save
Ctrl + P = Print
Alt + Tab = Switch between programs
Windows + D = Show/Hide Desktop
Windows + E = Open File Explorer
Windows + L = Lock computer
</code></pre>

<h3>Text Editing Shortcuts:</h3>
<pre><code>
Ctrl + B = Bold
Ctrl + I = Italic
Ctrl + U = Underline
Ctrl + Left/Right Arrow = Move cursor by word
Shift + Arrow keys = Select text
Home = Go to beginning of line
End = Go to end of line
Ctrl + Home = Go to beginning of document
Ctrl + End = Go to end of document
</code></pre>

<h3>Productivity Tips:</h3>
<ul>
    <li>Practice shortcuts daily until they become habit</li>
    <li>Start with 5-10 shortcuts you use most</li>
    <li>Use Alt+Tab to switch between programs quickly</li>
    <li>Learn your frequently-used program\'s shortcuts</li>
</ul>',
            'type' => 'text',
            'order' => 3
        ]);

        // Module 3: Internet and Email (Intermediate)
        $module3 = CourseModule::create([
            'course_id' => $course->id,
            'title' => 'Internet and Email Basics',
            'description' => 'Learn how to browse the internet safely and use email effectively.',
            'order' => 3
        ]);

        CourseLesson::create([
            'module_id' => $module3->id,
            'title' => 'Web Browsing Fundamentals',
            'content' => '<h2>Using Web Browsers</h2>
<p>A web browser is software that lets you access websites on the internet.</p>

<h3>Popular Web Browsers:</h3>
<ul>
    <li>Google Chrome</li>
    <li>Mozilla Firefox</li>
    <li>Microsoft Edge</li>
    <li>Safari (macOS)</li>
</ul>

<h3>Browser Basics:</h3>
<ul>
    <li><strong>Address Bar:</strong> Type website URLs (e.g., www.google.com)</li>
    <li><strong>Tabs:</strong> Open multiple websites in one window</li>
    <li><strong>Bookmarks:</strong> Save favorite websites for quick access</li>
    <li><strong>History:</strong> View previously visited websites</li>
    <li><strong>Private/Incognito Mode:</strong> Browse without saving history</li>
</ul>

<h3>Useful Browser Shortcuts:</h3>
<pre><code>
Ctrl + T = Open new tab
Ctrl + W = Close current tab
Ctrl + Shift + T = Reopen closed tab
Ctrl + Tab = Switch between tabs
Ctrl + L = Focus address bar
Ctrl + D = Bookmark current page
Ctrl + H = View history
F5 = Refresh page
</code></pre>',
            'type' => 'text',
            'order' => 1
        ]);

        CourseLesson::create([
            'module_id' => $module3->id,
            'title' => 'Internet Safety and Security',
            'content' => '<h2>Staying Safe Online</h2>
<p>Learn how to protect yourself while using the internet.</p>

<h3>Password Security:</h3>
<ul>
    <li>Use strong passwords (12+ characters)</li>
    <li>Mix uppercase, lowercase, numbers, and symbols</li>
    <li>Don\'t reuse passwords across sites</li>
    <li>Consider using a password manager</li>
    <li>Enable two-factor authentication (2FA)</li>
</ul>

<h3>Recognizing Threats:</h3>
<ul>
    <li><strong>Phishing:</strong> Fake emails pretending to be legitimate companies</li>
    <li><strong>Malware:</strong> Harmful software that can damage your computer</li>
    <li><strong>Scams:</strong> Fraudulent schemes to steal money or information</li>
</ul>

<h3>Safety Tips:</h3>
<ol>
    <li>Don\'t click suspicious links in emails</li>
    <li>Verify website URLs before entering personal info</li>
    <li>Look for HTTPS and padlock icon for secure sites</li>
    <li>Keep software and operating system updated</li>
    <li>Use antivirus software</li>
    <li>Be careful what you download</li>
    <li>Don\'t share personal information publicly</li>
</ol>',
            'type' => 'text',
            'order' => 2
        ]);

        // Module 4: Advanced Topics
        $module4 = CourseModule::create([
            'course_id' => $course->id,
            'title' => 'Advanced Computer Skills',
            'description' => 'Explore advanced topics like command line, troubleshooting, and system optimization.',
            'order' => 4
        ]);

        $lesson = CourseLesson::create([
            'module_id' => $module4->id,
            'title' => 'Introduction to Command Line',
            'content' => '<h2>Command Line Basics</h2>
<p>The command line (also called terminal or command prompt) allows you to interact with your computer using text commands.</p>

<h3>Why Learn Command Line?</h3>
<ul>
    <li>More powerful than graphical interface</li>
    <li>Automate repetitive tasks</li>
    <li>Essential for programming and IT work</li>
    <li>Troubleshooting and system administration</li>
</ul>

<h3>Opening Command Prompt (Windows):</h3>
<pre><code>
1. Press Windows + R
2. Type "cmd"
3. Press Enter
</code></pre>

<h3>Basic Commands:</h3>
<pre><code>
dir          - List files in current directory
cd           - Change directory
cd ..        - Go up one directory
mkdir        - Create new folder
del          - Delete file
copy         - Copy file
move         - Move file
cls          - Clear screen
exit         - Close command prompt
</code></pre>

<h3>Example Usage:</h3>
<pre><code>
C:\\Users\\YourName> dir
C:\\Users\\YourName> cd Documents
C:\\Users\\YourName\\Documents> mkdir MyFolder
C:\\Users\\YourName\\Documents> cd MyFolder
C:\\Users\\YourName\\Documents\\MyFolder>
</code></pre>',
            'type' => 'text',
            'order' => 1
        ]);

        // Add command line exercise
        LessonExercise::create([
            'lesson_id' => $lesson->id,
            'title' => 'Command Line Practice',
            'question' => 'What command would you use to list all files in the current directory in Windows Command Prompt?',
            'code_template' => '# Type the command here
',
            'solution' => 'dir',
            'test_cases' => json_encode([
                'Command should list directory contents',
                'Works in Windows Command Prompt',
                'Shows files and folders'
            ]),
            'difficulty' => 'medium',
            'points' => 15
        ]);

        CourseLesson::create([
            'module_id' => $module4->id,
            'title' => 'Computer Maintenance and Troubleshooting',
            'content' => '<h2>Keeping Your Computer Running Smoothly</h2>
<p>Learn basic maintenance and troubleshooting techniques.</p>

<h3>Regular Maintenance:</h3>
<ul>
    <li><strong>Updates:</strong> Keep OS and software updated</li>
    <li><strong>Disk Cleanup:</strong> Remove temporary files regularly</li>
    <li><strong>Defragmentation:</strong> Optimize hard drive (not needed for SSDs)</li>
    <li><strong>Backups:</strong> Regularly backup important files</li>
    <li><strong>Antivirus Scans:</strong> Run periodic security scans</li>
</ul>

<h3>Common Problems and Solutions:</h3>
<table border="1">
    <tr>
        <th>Problem</th>
        <th>Solution</th>
    </tr>
    <tr>
        <td>Computer running slow</td>
        <td>Close unnecessary programs, run disk cleanup, check for malware</td>
    </tr>
    <tr>
        <td>Program not responding</td>
        <td>Press Ctrl+Alt+Delete, open Task Manager, end task</td>
    </tr>
    <tr>
        <td>Can\'t connect to internet</td>
        <td>Restart router, check Wi-Fi connection, troubleshoot network adapter</td>
    </tr>
    <tr>
        <td>Computer won\'t start</td>
        <td>Check power connections, try safe mode, check for hardware issues</td>
    </tr>
</table>

<h3>Task Manager (Ctrl+Shift+Esc):</h3>
<ul>
    <li>View running programs and processes</li>
    <li>Check CPU, memory, and disk usage</li>
    <li>End unresponsive programs</li>
    <li>Monitor system performance</li>
</ul>',
            'type' => 'text',
            'order' => 2
        ]);

        CourseLesson::create([
            'module_id' => $module4->id,
            'title' => 'Cloud Computing Basics',
            'content' => '<h2>Introduction to Cloud Computing</h2>
<p>Cloud computing allows you to store files and run applications over the internet instead of on your local computer.</p>

<h3>Benefits of Cloud Computing:</h3>
<ul>
    <li><strong>Accessibility:</strong> Access files from any device with internet</li>
    <li><strong>Backup:</strong> Automatic backups protect against data loss</li>
    <li><strong>Collaboration:</strong> Easy sharing and real-time collaboration</li>
    <li><strong>Storage:</strong> Large storage capacity without using local space</li>
    <li><strong>Automatic Updates:</strong> Software always up to date</li>
</ul>

<h3>Popular Cloud Services:</h3>
<ul>
    <li><strong>Storage:</strong>
        <ul>
            <li>Google Drive</li>
            <li>Dropbox</li>
            <li>OneDrive (Microsoft)</li>
            <li>iCloud (Apple)</li>
        </ul>
    </li>
    <li><strong>Productivity:</strong>
        <ul>
            <li>Google Workspace (Docs, Sheets, Slides)</li>
            <li>Microsoft 365</li>
        </ul>
    </li>
    <li><strong>Communication:</strong>
        <ul>
            <li>Gmail</li>
            <li>Zoom</li>
            <li>Slack</li>
        </ul>
    </li>
</ul>

<h3>Cloud Security Tips:</h3>
<ol>
    <li>Use strong, unique passwords</li>
    <li>Enable two-factor authentication</li>
    <li>Be careful with file sharing settings</li>
    <li>Regularly review account activity</li>
    <li>Understand privacy policies</li>
</ol>',
            'type' => 'text',
            'order' => 3
        ]);

        echo "Computer Basics course seeded successfully!\n";
        echo "Course ID: {$course->id}\n";
        echo "Modules created: 4\n";
        echo "Lessons created: 10\n";
        echo "Exercises created: 2\n";
    }
}
