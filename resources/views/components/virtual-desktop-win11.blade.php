@props([
    'lesson' => null,
    'steps' => [],
    'mode' => 'guided', // 'guided' or 'free'
    'desktopConfig' => null,
])

@php
    $config = $desktopConfig ?? new \App\Models\DesktopConfig();
    $desktopIcons = $config->desktop_icons ?? \App\Models\DesktopConfig::DEFAULT_DESKTOP_ICONS;
    $taskbarApps = $config->taskbar_apps ?? \App\Models\DesktopConfig::DEFAULT_TASKBAR_APPS;
    $wallpaper = $config->wallpaper ?? 'default';
    $filesystem = $config->filesystem ?? \App\Models\DesktopConfig::DEFAULT_FILESYSTEM;
@endphp

<div class="win11-desktop-wrapper" id="win11DesktopWrapper">
    {{-- Desktop Area --}}
    <div class="win11-desktop" id="win11Desktop" data-wallpaper="{{ $wallpaper }}">
        {{-- Desktop Icons --}}
        <div class="win11-desktop-icons" id="desktopIcons">
            @foreach($desktopIcons as $icon)
                <div class="win11-desktop-icon"
                     data-app="{{ $icon['app'] ?? 'file-explorer' }}"
                     data-element-id="desktop-icon-{{ $icon['app'] ?? 'unknown' }}"
                     ondblclick="virtualDesktop.openApp('{{ $icon['app'] ?? 'file-explorer' }}')">
                    <div class="win11-icon-image">
                        <i class="{{ $icon['icon'] ?? 'fas fa-folder' }}"></i>
                    </div>
                    <span class="win11-icon-label">{{ $icon['name'] ?? 'Unknown' }}</span>
                </div>
            @endforeach
        </div>

        {{-- Windows Container --}}
        <div class="win11-windows-container" id="windowsContainer">
            {{-- Windows will be created dynamically --}}
        </div>

        {{-- Context Menu --}}
        <div class="win11-context-menu" id="contextMenu" style="display: none;">
            <div class="win11-context-item" data-action="view">
                <i class="fas fa-th-large"></i> View
            </div>
            <div class="win11-context-item" data-action="sort">
                <i class="fas fa-sort"></i> Sort by
            </div>
            <div class="win11-context-divider"></div>
            <div class="win11-context-item" data-action="refresh">
                <i class="fas fa-sync-alt"></i> Refresh
            </div>
            <div class="win11-context-divider"></div>
            <div class="win11-context-item" data-action="new">
                <i class="fas fa-plus"></i> New
                <i class="fas fa-chevron-right" style="margin-left: auto;"></i>
            </div>
            <div class="win11-context-divider"></div>
            <div class="win11-context-item" data-action="display">
                <i class="fas fa-desktop"></i> Display settings
            </div>
            <div class="win11-context-item" data-action="personalize">
                <i class="fas fa-palette"></i> Personalize
            </div>
        </div>
    </div>

    {{-- Taskbar --}}
    <div class="win11-taskbar" id="win11Taskbar">
        {{-- Start Button --}}
        <button class="win11-start-button" id="startButton" data-element-id="start-button">
            <svg viewBox="0 0 24 24" width="24" height="24">
                <path fill="currentColor" d="M3,3H11V11H3V3M3,13H11V21H3V13M13,3H21V11H13V3M13,13H21V21H13V13Z"/>
            </svg>
        </button>

        {{-- Search --}}
        <button class="win11-taskbar-button" id="searchButton" data-element-id="search-button">
            <i class="fas fa-search"></i>
        </button>

        {{-- Pinned Apps --}}
        <div class="win11-taskbar-apps" id="taskbarApps">
            @foreach($taskbarApps as $app)
                <button class="win11-taskbar-app"
                        data-app="{{ $app }}"
                        data-element-id="taskbar-{{ $app }}"
                        onclick="virtualDesktop.openApp('{{ $app }}')">
                    @switch($app)
                        @case('file-explorer')
                            <i class="fas fa-folder"></i>
                            @break
                        @case('browser')
                            <i class="fas fa-globe"></i>
                            @break
                        @case('settings')
                            <i class="fas fa-cog"></i>
                            @break
                        @case('notepad')
                            <i class="fas fa-file-alt"></i>
                            @break
                        @default
                            <i class="fas fa-window-maximize"></i>
                    @endswitch
                </button>
            @endforeach
        </div>

        {{-- Running Apps (dynamic) --}}
        <div class="win11-taskbar-running" id="taskbarRunning">
            {{-- Running apps will appear here --}}
        </div>

        {{-- System Tray --}}
        <div class="win11-system-tray" id="systemTray">
            <button class="win11-tray-button" id="trayButton">
                <i class="fas fa-chevron-up"></i>
            </button>
            <div class="win11-tray-icons">
                <span class="win11-tray-icon" title="Network">
                    <i class="fas fa-wifi"></i>
                </span>
                <span class="win11-tray-icon" title="Sound">
                    <i class="fas fa-volume-up"></i>
                </span>
                <span class="win11-tray-icon" title="Battery">
                    <i class="fas fa-battery-three-quarters"></i>
                </span>
            </div>
            <div class="win11-datetime" id="dateTime">
                <span class="win11-time" id="clockTime">12:00</span>
                <span class="win11-date" id="clockDate">1/1/2024</span>
            </div>
            <button class="win11-notification-button" id="notificationButton">
                <i class="fas fa-comment"></i>
            </button>
        </div>
    </div>

    {{-- Start Menu --}}
    <div class="win11-start-menu" id="startMenu" style="display: none;">
        <div class="win11-start-search">
            <i class="fas fa-search"></i>
            <input type="text" placeholder="Type here to search" id="startSearchInput">
        </div>
        <div class="win11-start-section">
            <div class="win11-start-section-header">
                <span>Pinned</span>
                <button class="win11-start-all-apps">All apps <i class="fas fa-chevron-right"></i></button>
            </div>
            <div class="win11-start-pinned" id="startPinned">
                @foreach(\App\Models\DesktopConfig::AVAILABLE_APPS as $appId => $appData)
                    <div class="win11-start-app" onclick="virtualDesktop.openApp('{{ $appId }}'); virtualDesktop.toggleStartMenu();">
                        <div class="win11-start-app-icon">
                            <i class="{{ $appData['icon'] }}"></i>
                        </div>
                        <span>{{ $appData['name'] }}</span>
                    </div>
                @endforeach
            </div>
        </div>
        <div class="win11-start-section">
            <div class="win11-start-section-header">
                <span>Recommended</span>
                <button class="win11-start-more">More <i class="fas fa-chevron-right"></i></button>
            </div>
            <div class="win11-start-recommended" id="startRecommended">
                <div class="win11-start-recent-item">
                    <i class="fas fa-file-word"></i>
                    <div class="win11-recent-info">
                        <span class="win11-recent-name">Document.docx</span>
                        <span class="win11-recent-time">Recently opened</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="win11-start-footer">
            <div class="win11-start-user">
                <div class="win11-user-avatar">
                    <i class="fas fa-user"></i>
                </div>
                <span>Student</span>
            </div>
            <button class="win11-power-button" title="Power">
                <i class="fas fa-power-off"></i>
            </button>
        </div>
    </div>

    {{-- Guided Mode Overlay --}}
    @if($mode === 'guided' && count($steps) > 0)
        <div class="win11-guided-overlay" id="guidedOverlay">
            {{-- Step Indicator --}}
            <div class="win11-step-indicator" id="stepIndicator">
                <div class="win11-step-progress">
                    <span id="currentStepNumber">1</span> / <span id="totalSteps">{{ count($steps) }}</span>
                </div>
                <div class="win11-step-progress-bar">
                    <div class="win11-step-progress-fill" id="stepProgressFill" style="width: 0%;"></div>
                </div>
            </div>

            {{-- Instruction Panel --}}
            <div class="win11-instruction-panel" id="instructionPanel">
                <div class="win11-instruction-header">
                    <div class="win11-instruction-icon">
                        <i class="fas fa-lightbulb"></i>
                    </div>
                    <h3 id="instructionTitle">Getting Started</h3>
                    <button class="win11-instruction-minimize" onclick="guidedMode.toggleInstructionPanel()">
                        <i class="fas fa-chevron-down"></i>
                    </button>
                </div>
                <div class="win11-instruction-content" id="instructionContent">
                    <p id="instructionText">Follow the instructions to complete this lesson.</p>
                </div>
                <div class="win11-instruction-actions">
                    <button class="win11-hint-button" id="hintButton" onclick="guidedMode.showHint()">
                        <i class="fas fa-question-circle"></i> Hint
                    </button>
                    <button class="win11-skip-button" id="skipButton" onclick="guidedMode.skipStep()">
                        Skip <i class="fas fa-forward"></i>
                    </button>
                </div>
                <div class="win11-hint-content" id="hintContent" style="display: none;">
                    <i class="fas fa-info-circle"></i>
                    <span id="hintText"></span>
                </div>
            </div>

            {{-- Highlight Element (for targeting) --}}
            <div class="win11-highlight" id="highlightElement" style="display: none;"></div>

            {{-- Arrow Pointer --}}
            <div class="win11-arrow-pointer" id="arrowPointer" style="display: none;">
                <i class="fas fa-hand-pointer"></i>
            </div>

            {{-- Success Animation --}}
            <div class="win11-success-animation" id="successAnimation" style="display: none;">
                <div class="win11-success-check">
                    <i class="fas fa-check-circle"></i>
                </div>
                <span>Great job!</span>
            </div>
        </div>

        {{-- Lesson Complete Modal --}}
        <div class="win11-lesson-complete" id="lessonCompleteModal" style="display: none;">
            <div class="win11-modal-content">
                <div class="win11-complete-icon">
                    <i class="fas fa-trophy"></i>
                </div>
                <h2>Lesson Complete!</h2>
                <p>Congratulations! You've completed this interactive lesson.</p>
                <div class="win11-complete-stats">
                    <div class="win11-stat">
                        <span class="win11-stat-value" id="completedStepsCount">0</span>
                        <span class="win11-stat-label">Steps Completed</span>
                    </div>
                    <div class="win11-stat">
                        <span class="win11-stat-value" id="earnedPoints">0</span>
                        <span class="win11-stat-label">Points Earned</span>
                    </div>
                    <div class="win11-stat">
                        <span class="win11-stat-value" id="timeSpent">0:00</span>
                        <span class="win11-stat-label">Time Spent</span>
                    </div>
                </div>
                <div class="win11-complete-actions">
                    <button class="win11-btn-secondary" onclick="guidedMode.restartLesson()">
                        <i class="fas fa-redo"></i> Try Again
                    </button>
                    <a href="{{ $lesson ? url('courses/' . ($lesson->module->course->slug ?? 'course')) : '#' }}" class="win11-btn-primary">
                        <i class="fas fa-arrow-right"></i> Continue
                    </a>
                </div>
            </div>
        </div>
    @endif

    {{-- Free Mode Controls --}}
    @if($mode === 'free')
        <div class="win11-free-mode-controls" id="freeModeControls">
            <button class="win11-free-btn" onclick="virtualDesktop.resetDesktop()">
                <i class="fas fa-undo"></i> Reset
            </button>
            <button class="win11-free-btn" onclick="toggleFullscreen()">
                <i class="fas fa-expand"></i> Fullscreen
            </button>
        </div>
    @endif
</div>

{{-- Pass data to JavaScript --}}
<script>
    window.virtualDesktopConfig = {
        mode: '{{ $mode }}',
        lessonId: {{ $lesson->id ?? 'null' }},
        steps: @json($steps),
        filesystem: @json($filesystem),
        desktopIcons: @json($desktopIcons),
        csrfToken: '{{ csrf_token() }}'
    };
</script>

{{-- Include CSS --}}
<link rel="stylesheet" href="{{ asset('css/virtual-desktop/win11-theme.css') }}">

{{-- Include JS --}}
<script src="{{ asset('js/virtual-desktop/desktop-core.js') }}"></script>
@if($mode === 'guided')
<script src="{{ asset('js/virtual-desktop/guided-mode.js') }}"></script>
@endif
