<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\CourseModule;
use App\Models\CourseLesson;
use App\Models\InteractiveStep;
use App\Models\DesktopConfig;
use App\Models\User;
use Illuminate\Database\Seeder;

class InteractiveCoursesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get or create an instructor
        $instructor = User::where('is_admin', 1)->first() ?? User::first();

        // Create the Interactive Windows Basics course
        $course = Course::create([
            'title' => 'Windows 11 Interactive Tutorial',
            'slug' => 'windows-11-interactive-tutorial',
            'description' => 'Learn to use Windows 11 through hands-on interactive lessons. Practice navigating the desktop, managing files, and using applications in our virtual Windows environment.',
            'objectives' => '- Navigate the Windows 11 desktop
- Use the Start Menu and Taskbar
- Open and manage application windows
- Organize files and folders
- Customize desktop settings',
            'price' => 0,
            'is_free' => true,
            'level' => 'beginner',
            'duration_hours' => 2,
            'instructor_id' => $instructor?->id,
            'is_published' => true,
            'enrollment_count' => 0,
        ]);

        $this->command->info("Created course: {$course->title}");

        // Module 1: Getting Started with Windows 11
        $module1 = CourseModule::create([
            'course_id' => $course->id,
            'title' => 'Getting Started with Windows 11',
            'description' => 'Learn the basics of the Windows 11 desktop, including the Start Menu, Taskbar, and desktop icons.',
            'order' => 1,
        ]);

        // Lesson 1.1: Your First Look at the Desktop
        $lesson1_1 = $this->createInteractiveLesson($module1, [
            'title' => 'Your First Look at the Desktop',
            'content' => '<h2>Welcome to Windows 11!</h2>
<p>The Windows desktop is like your virtual workspace. Everything you need to do on your computer starts here.</p>
<h3>In this lesson, you will learn:</h3>
<ul>
<li>What the desktop is</li>
<li>How to use desktop icons</li>
<li>Where to find the Start button</li>
<li>What the Taskbar does</li>
</ul>
<p><strong>Follow the step-by-step instructions to practice!</strong></p>',
            'order' => 1,
            'duration_minutes' => 8,
            'is_free_preview' => true,
        ]);

        // Desktop config for Lesson 1.1
        DesktopConfig::create([
            'lesson_id' => $lesson1_1->id,
            'desktop_icons' => [
                ['name' => 'This PC', 'icon' => 'fas fa-desktop', 'app' => 'file-explorer'],
                ['name' => 'Recycle Bin', 'icon' => 'fas fa-trash', 'app' => 'recycle-bin'],
                ['name' => 'Documents', 'icon' => 'fas fa-folder', 'app' => 'file-explorer'],
            ],
            'taskbar_apps' => ['file-explorer', 'browser', 'settings'],
            'wallpaper' => 'default',
            'mode' => 'guided',
        ]);

        // Steps for Lesson 1.1
        $this->createSteps($lesson1_1->id, [
            [
                'title' => 'Look at the Desktop',
                'instruction' => 'Take a moment to look at your screen. The large area with the background image is called the Desktop. This is where you can place shortcuts to your favorite programs and files.',
                'action_type' => 'click',
                'target_element' => 'win11Desktop',
                'hint' => 'Click anywhere on the desktop background to continue.',
                'points' => 5,
            ],
            [
                'title' => 'Find the Start Button',
                'instruction' => 'Look at the bottom of your screen. There\'s a bar called the Taskbar. In the center, you\'ll see the Start button - it looks like four small squares (the Windows logo). Click on it!',
                'action_type' => 'click',
                'target_element' => 'start-button',
                'hint' => 'The Start button is in the center of the Taskbar. It has the Windows logo (four squares).',
                'points' => 10,
            ],
            [
                'title' => 'Explore the Start Menu',
                'instruction' => 'Great job! The Start Menu is now open. Here you can find all your apps and programs. You can also search for things using the search box at the top.',
                'action_type' => 'click',
                'target_element' => 'startSearchInput',
                'hint' => 'Click on the search box at the top of the Start Menu.',
                'points' => 10,
            ],
            [
                'title' => 'Close the Start Menu',
                'instruction' => 'Now click anywhere outside the Start Menu (on the desktop) to close it.',
                'action_type' => 'click',
                'target_element' => 'win11Desktop',
                'hint' => 'Click on the desktop background to close the Start Menu.',
                'points' => 5,
            ],
            [
                'title' => 'Find a Desktop Icon',
                'instruction' => 'Look at the desktop icons in the upper-left area. These are shortcuts to important places on your computer. Double-click on "This PC" to open it.',
                'action_type' => 'double_click',
                'target_element' => 'desktop-icon-file-explorer',
                'hint' => 'Double-click (click twice quickly) on the "This PC" icon.',
                'points' => 15,
            ],
        ]);

        // Lesson 1.2: Using the Taskbar
        $lesson1_2 = $this->createInteractiveLesson($module1, [
            'title' => 'Using the Taskbar',
            'content' => '<h2>The Taskbar - Your Command Center</h2>
<p>The Taskbar at the bottom of your screen helps you quickly access your apps and see what\'s running.</p>
<h3>Key features:</h3>
<ul>
<li><strong>Start Button:</strong> Opens the Start Menu</li>
<li><strong>Search:</strong> Find files, apps, and settings</li>
<li><strong>Pinned Apps:</strong> Your favorite apps for quick access</li>
<li><strong>System Tray:</strong> Shows time, WiFi, volume, and more</li>
</ul>',
            'order' => 2,
            'duration_minutes' => 10,
        ]);

        DesktopConfig::create([
            'lesson_id' => $lesson1_2->id,
            'taskbar_apps' => ['file-explorer', 'browser', 'notepad', 'calculator', 'settings'],
            'mode' => 'guided',
        ]);

        $this->createSteps($lesson1_2->id, [
            [
                'title' => 'Open File Explorer from Taskbar',
                'instruction' => 'In the Taskbar, find the File Explorer icon (it looks like a folder). Click on it to open File Explorer.',
                'action_type' => 'click',
                'target_element' => 'taskbar-file-explorer',
                'hint' => 'Look for the yellow folder icon in the Taskbar.',
                'points' => 10,
            ],
            [
                'title' => 'Open Another App',
                'instruction' => 'Without closing File Explorer, click on the Calculator icon in the Taskbar to open Calculator.',
                'action_type' => 'click',
                'target_element' => 'taskbar-calculator',
                'hint' => 'The Calculator icon looks like a small calculator.',
                'points' => 10,
            ],
            [
                'title' => 'Check the Time',
                'instruction' => 'Look at the right side of the Taskbar. Click on the date and time to see more details.',
                'action_type' => 'click',
                'target_element' => 'dateTime',
                'hint' => 'The time and date are displayed in the bottom-right corner.',
                'points' => 10,
            ],
            [
                'title' => 'Use Search',
                'instruction' => 'Click the Search button (magnifying glass icon) to open Windows Search. You can search for anything on your computer!',
                'action_type' => 'click',
                'target_element' => 'search-button',
                'hint' => 'The search icon is next to the Start button.',
                'points' => 10,
            ],
        ]);

        // Module 2: Working with Windows
        $module2 = CourseModule::create([
            'course_id' => $course->id,
            'title' => 'Working with Windows',
            'description' => 'Learn to open, close, move, and resize application windows.',
            'order' => 2,
        ]);

        // Lesson 2.1: Opening and Closing Apps
        $lesson2_1 = $this->createInteractiveLesson($module2, [
            'title' => 'Opening and Closing Apps',
            'content' => '<h2>Opening and Closing Applications</h2>
<p>In Windows, programs run in "windows" - rectangular areas on your screen. You can open, close, and arrange these windows.</p>
<h3>Ways to open apps:</h3>
<ul>
<li>From the Start Menu</li>
<li>From the Taskbar</li>
<li>By double-clicking desktop icons</li>
</ul>
<h3>Closing apps:</h3>
<ul>
<li>Click the X button in the top-right corner</li>
<li>Right-click the app in taskbar and select "Close"</li>
</ul>',
            'order' => 1,
            'duration_minutes' => 12,
        ]);

        DesktopConfig::create([
            'lesson_id' => $lesson2_1->id,
            'desktop_icons' => [
                ['name' => 'Notepad', 'icon' => 'fas fa-file-alt', 'app' => 'notepad'],
                ['name' => 'Calculator', 'icon' => 'fas fa-calculator', 'app' => 'calculator'],
                ['name' => 'Browser', 'icon' => 'fas fa-globe', 'app' => 'browser'],
            ],
            'taskbar_apps' => ['file-explorer', 'browser', 'notepad', 'calculator'],
            'mode' => 'guided',
        ]);

        $this->createSteps($lesson2_1->id, [
            [
                'title' => 'Double-click to Open',
                'instruction' => 'Double-click on the Notepad icon on the desktop. Double-clicking is how you open most things in Windows!',
                'action_type' => 'double_click',
                'target_element' => 'desktop-icon-notepad',
                'hint' => 'Click twice quickly on the Notepad icon. If it doesn\'t work, try clicking faster.',
                'points' => 15,
            ],
            [
                'title' => 'See the Window',
                'instruction' => 'Notepad is now open! Notice the window has a title bar at the top with the app name. Click inside the white area where you can type.',
                'action_type' => 'click',
                'target_element' => '.win11-window[data-app="notepad"]',
                'hint' => 'Click anywhere inside the Notepad window.',
                'points' => 5,
            ],
            [
                'title' => 'Close the Window',
                'instruction' => 'Now let\'s close Notepad. Click the red X button in the top-right corner of the Notepad window.',
                'action_type' => 'close_window',
                'target_element' => '.win11-close-btn',
                'hint' => 'Look for the X button in the top-right corner of the window.',
                'points' => 10,
            ],
            [
                'title' => 'Open from Taskbar',
                'instruction' => 'You can also open apps from the Taskbar. Click on the Calculator icon in the Taskbar.',
                'action_type' => 'click',
                'target_element' => 'taskbar-calculator',
                'hint' => 'Find the Calculator icon in the Taskbar at the bottom.',
                'points' => 10,
            ],
        ]);

        // Lesson 2.2: Window Controls
        $lesson2_2 = $this->createInteractiveLesson($module2, [
            'title' => 'Window Controls: Minimize, Maximize, Close',
            'content' => '<h2>Controlling Your Windows</h2>
<p>Every window has three buttons in the top-right corner:</p>
<ul>
<li><strong>Minimize (−):</strong> Hides the window but keeps the app running</li>
<li><strong>Maximize (□):</strong> Makes the window fill the whole screen</li>
<li><strong>Close (×):</strong> Closes the app completely</li>
</ul>
<p>Minimized windows can be found in the Taskbar.</p>',
            'order' => 2,
            'duration_minutes' => 10,
        ]);

        DesktopConfig::create([
            'lesson_id' => $lesson2_2->id,
            'taskbar_apps' => ['file-explorer', 'calculator', 'notepad'],
            'mode' => 'guided',
        ]);

        $this->createSteps($lesson2_2->id, [
            [
                'title' => 'Open Calculator',
                'instruction' => 'First, click on the Calculator icon in the Taskbar to open it.',
                'action_type' => 'click',
                'target_element' => 'taskbar-calculator',
                'hint' => 'Find Calculator in the Taskbar.',
                'points' => 5,
            ],
            [
                'title' => 'Minimize the Window',
                'instruction' => 'Now click the Minimize button (−) in the top-right corner. The window will disappear but Calculator is still running!',
                'action_type' => 'minimize_window',
                'target_element' => '.win11-minimize-btn',
                'hint' => 'The minimize button looks like a minus sign or a small line.',
                'points' => 10,
            ],
            [
                'title' => 'Restore from Taskbar',
                'instruction' => 'The Calculator is minimized to the Taskbar. Click on the Calculator icon in the Taskbar to bring it back.',
                'action_type' => 'click',
                'target_element' => 'taskbar-calculator',
                'hint' => 'Click the Calculator icon in the Taskbar to restore the window.',
                'points' => 10,
            ],
            [
                'title' => 'Maximize the Window',
                'instruction' => 'Click the Maximize button (□) to make Calculator fill your entire screen.',
                'action_type' => 'maximize_window',
                'target_element' => '.win11-maximize-btn',
                'hint' => 'The maximize button looks like a square.',
                'points' => 10,
            ],
            [
                'title' => 'Restore Window Size',
                'instruction' => 'Click the same button again (it now shows two overlapping squares) to restore the window to its original size.',
                'action_type' => 'maximize_window',
                'target_element' => '.win11-maximize-btn',
                'hint' => 'Click the restore button to make the window smaller again.',
                'points' => 10,
            ],
        ]);

        // Module 3: File Management
        $module3 = CourseModule::create([
            'course_id' => $course->id,
            'title' => 'Managing Your Files',
            'description' => 'Learn to navigate folders, create files, and organize your documents.',
            'order' => 3,
        ]);

        // Lesson 3.1: File Explorer Basics
        $lesson3_1 = $this->createInteractiveLesson($module3, [
            'title' => 'Introduction to File Explorer',
            'content' => '<h2>File Explorer - Your File Manager</h2>
<p>File Explorer is like a filing cabinet for your computer. It helps you find, organize, and manage all your files and folders.</p>
<h3>Key parts of File Explorer:</h3>
<ul>
<li><strong>Navigation pane:</strong> Quick links on the left side</li>
<li><strong>Address bar:</strong> Shows where you are</li>
<li><strong>Content area:</strong> Shows files and folders</li>
<li><strong>Toolbar:</strong> Buttons for common actions</li>
</ul>',
            'order' => 1,
            'duration_minutes' => 15,
        ]);

        DesktopConfig::create([
            'lesson_id' => $lesson3_1->id,
            'desktop_icons' => [
                ['name' => 'This PC', 'icon' => 'fas fa-desktop', 'app' => 'file-explorer'],
                ['name' => 'Documents', 'icon' => 'fas fa-folder', 'app' => 'file-explorer'],
            ],
            'filesystem' => [
                'Documents' => [
                    'type' => 'folder',
                    'children' => [
                        'My Resume.docx' => ['type' => 'file', 'icon' => 'fas fa-file-word'],
                        'Work' => [
                            'type' => 'folder',
                            'children' => [
                                'Report.docx' => ['type' => 'file', 'icon' => 'fas fa-file-word'],
                            ],
                        ],
                        'Personal' => [
                            'type' => 'folder',
                            'children' => [],
                        ],
                    ],
                ],
                'Pictures' => [
                    'type' => 'folder',
                    'children' => [
                        'Vacation.jpg' => ['type' => 'file', 'icon' => 'fas fa-file-image'],
                    ],
                ],
                'Downloads' => ['type' => 'folder', 'children' => []],
            ],
            'mode' => 'guided',
        ]);

        $this->createSteps($lesson3_1->id, [
            [
                'title' => 'Open File Explorer',
                'instruction' => 'Double-click on "This PC" on the desktop to open File Explorer.',
                'action_type' => 'double_click',
                'target_element' => 'desktop-icon-file-explorer',
                'hint' => 'Find the "This PC" icon on the desktop and double-click it.',
                'points' => 10,
            ],
            [
                'title' => 'Navigate to Documents',
                'instruction' => 'In the left panel, click on "Documents" to see your document files.',
                'action_type' => 'click',
                'target_element' => 'nav-documents',
                'hint' => 'Look for "Documents" in the navigation pane on the left side.',
                'points' => 10,
            ],
            [
                'title' => 'Open a Folder',
                'instruction' => 'Double-click on the "Work" folder to see what\'s inside.',
                'action_type' => 'double_click',
                'target_element' => 'folder-Work',
                'hint' => 'Find the folder called "Work" and double-click it.',
                'points' => 10,
            ],
            [
                'title' => 'Go Back',
                'instruction' => 'Click the Back button (left arrow) to return to the Documents folder.',
                'action_type' => 'click',
                'target_element' => 'back-button',
                'hint' => 'The back arrow is at the top-left of the File Explorer window.',
                'points' => 10,
            ],
        ]);

        $this->command->info('');
        $this->command->info('Interactive Windows 11 Tutorial course created!');
        $this->command->info("Course ID: {$course->id}");
        $this->command->info("Modules: 3");
        $this->command->info("Interactive Lessons: 5");
        $this->command->info('');
        $this->command->info('To access the interactive lessons:');
        $this->command->info("URL: /courses/{$course->slug}/lessons/{lesson_id}/interactive");
    }

    /**
     * Create an interactive lesson
     */
    private function createInteractiveLesson(CourseModule $module, array $data): CourseLesson
    {
        return CourseLesson::create([
            'module_id' => $module->id,
            'title' => $data['title'],
            'content' => $data['content'],
            'type' => 'interactive',
            'order' => $data['order'],
            'duration_minutes' => $data['duration_minutes'] ?? 10,
            'is_free_preview' => $data['is_free_preview'] ?? false,
        ]);
    }

    /**
     * Create multiple interactive steps for a lesson
     */
    private function createSteps(int $lessonId, array $stepsData): void
    {
        foreach ($stepsData as $index => $step) {
            InteractiveStep::create([
                'lesson_id' => $lessonId,
                'step_number' => $index + 1,
                'title' => $step['title'],
                'instruction' => $step['instruction'],
                'action_type' => $step['action_type'],
                'target_element' => $step['target_element'] ?? null,
                'action_data' => $step['action_data'] ?? null,
                'validation_rules' => $step['validation_rules'] ?? null,
                'hint' => $step['hint'] ?? null,
                'points' => $step['points'] ?? 10,
                'is_required' => $step['is_required'] ?? true,
                'timeout_seconds' => $step['timeout_seconds'] ?? 30,
            ]);
        }
    }
}
