@extends('layouts.master')

@section('content')
<style>
    :root {
        --primary-color: #673AB7;
        --secondary-color: #9c27b0;
        --accent-color: #03a9f4;
        --text-color: #1f2937;
        --light-bg: #f3f4f6;
        --card-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    }

    body {
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
        margin: 0;
        padding: 0;
        background-color: #1a1a2e;
        color: var(--text-color);
        line-height: 1.7;
    }

    .interactive-lesson-container {
        min-height: 100vh;
        display: flex;
        flex-direction: column;
    }

    /* Header Bar */
    .interactive-header {
        background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
        color: white;
        padding: 0.75rem 1.5rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
        z-index: 100;
    }

    .header-left {
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .back-button {
        background: rgba(255, 255, 255, 0.2);
        border: none;
        color: white;
        padding: 0.5rem 1rem;
        border-radius: 6px;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.9rem;
        transition: background 0.2s;
        text-decoration: none;
    }

    .back-button:hover {
        background: rgba(255, 255, 255, 0.3);
        color: white;
    }

    .lesson-info h1 {
        margin: 0;
        font-size: 1.1rem;
        font-weight: 600;
    }

    .lesson-info p {
        margin: 0.25rem 0 0 0;
        font-size: 0.8rem;
        opacity: 0.9;
    }

    .header-right {
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .mode-switch {
        display: flex;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 8px;
        padding: 0.25rem;
    }

    .mode-btn {
        background: transparent;
        border: none;
        color: rgba(255, 255, 255, 0.7);
        padding: 0.5rem 1rem;
        border-radius: 6px;
        cursor: pointer;
        font-size: 0.85rem;
        transition: all 0.2s;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .mode-btn:hover {
        color: white;
    }

    .mode-btn.active {
        background: white;
        color: var(--primary-color);
    }

    .help-button {
        background: rgba(255, 255, 255, 0.2);
        border: none;
        color: white;
        width: 36px;
        height: 36px;
        border-radius: 50%;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1rem;
        transition: background 0.2s;
    }

    .help-button:hover {
        background: rgba(255, 255, 255, 0.3);
    }

    /* Main Desktop Area */
    .desktop-wrapper {
        flex: 1;
        display: flex;
        position: relative;
        overflow: hidden;
    }

    /* Progress Sidebar */
    .progress-sidebar {
        width: 320px;
        background: white;
        border-right: 1px solid #e5e7eb;
        display: flex;
        flex-direction: column;
        overflow: hidden;
        transition: transform 0.3s ease;
    }

    .progress-sidebar.collapsed {
        transform: translateX(-100%);
        position: absolute;
        left: 0;
        top: 0;
        bottom: 0;
        z-index: 50;
    }

    .progress-header {
        padding: 1.5rem;
        border-bottom: 1px solid #e5e7eb;
        background: var(--light-bg);
    }

    .progress-header h3 {
        margin: 0 0 0.5rem 0;
        font-size: 1rem;
        color: var(--primary-color);
    }

    .progress-bar-container {
        background: #e5e7eb;
        border-radius: 10px;
        height: 10px;
        overflow: hidden;
    }

    .progress-bar-fill {
        height: 100%;
        background: linear-gradient(90deg, var(--primary-color), var(--secondary-color));
        transition: width 0.5s ease;
        border-radius: 10px;
    }

    .progress-text {
        margin: 0.5rem 0 0 0;
        font-size: 0.85rem;
        color: #6b7280;
    }

    .steps-list {
        flex: 1;
        overflow-y: auto;
        padding: 1rem;
    }

    .step-item {
        display: flex;
        align-items: flex-start;
        gap: 0.75rem;
        padding: 1rem;
        background: var(--light-bg);
        border-radius: 8px;
        margin-bottom: 0.5rem;
        transition: all 0.2s;
        cursor: pointer;
    }

    .step-item:hover {
        background: #e5e7eb;
    }

    .step-item.active {
        background: #ede7f6;
        border-left: 3px solid var(--primary-color);
    }

    .step-item.completed {
        background: #d1fae5;
    }

    .step-number {
        width: 28px;
        height: 28px;
        border-radius: 50%;
        background: var(--primary-color);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.8rem;
        font-weight: 600;
        flex-shrink: 0;
    }

    .step-item.completed .step-number {
        background: #10b981;
    }

    .step-content h4 {
        margin: 0 0 0.25rem 0;
        font-size: 0.9rem;
        font-weight: 600;
    }

    .step-content p {
        margin: 0;
        font-size: 0.8rem;
        color: #6b7280;
    }

    .step-item.completed .step-content h4,
    .step-item.completed .step-content p {
        text-decoration: line-through;
        color: #6b7280;
    }

    /* Toggle Sidebar Button */
    .toggle-sidebar {
        position: absolute;
        left: 0;
        top: 50%;
        transform: translateY(-50%);
        background: white;
        border: 1px solid #e5e7eb;
        border-left: none;
        padding: 1rem 0.5rem;
        border-radius: 0 8px 8px 0;
        cursor: pointer;
        z-index: 60;
        box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
    }

    .toggle-sidebar:hover {
        background: var(--light-bg);
    }

    /* Help Modal */
    .help-modal {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.6);
        display: none;
        align-items: center;
        justify-content: center;
        z-index: 1000;
    }

    .help-modal.show {
        display: flex;
    }

    .help-content {
        background: white;
        border-radius: 12px;
        padding: 2rem;
        max-width: 600px;
        width: 90%;
        max-height: 80vh;
        overflow-y: auto;
    }

    .help-content h2 {
        margin: 0 0 1.5rem 0;
        color: var(--primary-color);
    }

    .help-section {
        margin-bottom: 1.5rem;
    }

    .help-section h4 {
        margin: 0 0 0.5rem 0;
        color: var(--text-color);
    }

    .help-section p {
        margin: 0;
        color: #6b7280;
        font-size: 0.9rem;
    }

    .shortcut-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .shortcut-list li {
        display: flex;
        justify-content: space-between;
        padding: 0.5rem 0;
        border-bottom: 1px solid #e5e7eb;
    }

    .shortcut-list kbd {
        background: var(--light-bg);
        padding: 0.25rem 0.5rem;
        border-radius: 4px;
        font-family: monospace;
        font-size: 0.85rem;
    }

    .close-help {
        background: var(--primary-color);
        color: white;
        border: none;
        padding: 0.75rem 2rem;
        border-radius: 8px;
        cursor: pointer;
        font-size: 1rem;
        margin-top: 1rem;
    }

    .close-help:hover {
        background: var(--secondary-color);
    }

    /* Responsive */
    @media (max-width: 1024px) {
        .progress-sidebar {
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            z-index: 50;
            transform: translateX(-100%);
        }

        .progress-sidebar.show {
            transform: translateX(0);
        }

        .toggle-sidebar {
            display: block;
        }
    }

    @media (max-width: 768px) {
        .interactive-header {
            flex-direction: column;
            gap: 1rem;
            padding: 1rem;
        }

        .mode-switch {
            width: 100%;
            justify-content: center;
        }

        .progress-sidebar {
            width: 100%;
        }
    }
</style>

<div class="interactive-lesson-container">
    {{-- Header --}}
    <header class="interactive-header">
        <div class="header-left">
            <a href="{{ route('courses.show', $course->slug) }}" class="back-button">
                <i class="fas fa-arrow-left"></i>
                <span>Back</span>
            </a>
            <div class="lesson-info">
                <h1>{{ $lesson->title }}</h1>
                <p>{{ $course->title }} - {{ $lesson->module->title }}</p>
            </div>
        </div>
        <div class="header-right">
            <div class="mode-switch">
                <button class="mode-btn {{ $mode === 'guided' ? 'active' : '' }}" onclick="switchMode('guided')">
                    <i class="fas fa-graduation-cap"></i>
                    Guided
                </button>
                <button class="mode-btn {{ $mode === 'free' ? 'active' : '' }}" onclick="switchMode('free')">
                    <i class="fas fa-mouse-pointer"></i>
                    Free Explore
                </button>
            </div>
            <button class="help-button" onclick="showHelp()">
                <i class="fas fa-question"></i>
            </button>
        </div>
    </header>

    {{-- Main Area --}}
    <div class="desktop-wrapper">
        {{-- Progress Sidebar (Guided Mode Only) --}}
        @if($mode === 'guided')
            <aside class="progress-sidebar" id="progressSidebar">
                <div class="progress-header">
                    <h3><i class="fas fa-tasks"></i> Lesson Progress</h3>
                    <div class="progress-bar-container">
                        <div class="progress-bar-fill" id="progressBarFill" style="width: 0%;"></div>
                    </div>
                    <p class="progress-text">
                        <span id="completedCount">0</span> / {{ count($steps) }} steps completed
                    </p>
                </div>
                <div class="steps-list" id="stepsList">
                    @foreach($steps as $index => $step)
                        <div class="step-item {{ $index === 0 ? 'active' : '' }}" data-step="{{ $index }}">
                            <div class="step-number">{{ $index + 1 }}</div>
                            <div class="step-content">
                                <h4>{{ $step->title }}</h4>
                                <p>{{ Str::limit($step->instruction, 60) }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </aside>
        @endif

        {{-- Toggle Sidebar Button --}}
        @if($mode === 'guided')
            <button class="toggle-sidebar" id="toggleSidebar" onclick="toggleSidebar()">
                <i class="fas fa-chevron-right" id="toggleIcon"></i>
            </button>
        @endif

        {{-- Virtual Desktop --}}
        @include('components.virtual-desktop-win11', [
            'lesson' => $lesson,
            'steps' => $steps,
            'mode' => $mode,
            'desktopConfig' => $desktopConfig,
        ])
    </div>
</div>

{{-- Help Modal --}}
<div class="help-modal" id="helpModal">
    <div class="help-content">
        <h2><i class="fas fa-question-circle"></i> Interactive Lesson Help</h2>

        <div class="help-section">
            <h4>Guided Mode</h4>
            <p>Follow step-by-step instructions to complete the lesson. Each step will highlight the element you need to interact with and guide you through the action.</p>
        </div>

        <div class="help-section">
            <h4>Free Explore Mode</h4>
            <p>Explore the virtual desktop freely without guidance. Practice what you've learned at your own pace.</p>
        </div>

        <div class="help-section">
            <h4>Virtual Desktop Tips</h4>
            <ul style="margin: 0.5rem 0; padding-left: 1.25rem; color: #6b7280;">
                <li>Double-click desktop icons to open applications</li>
                <li>Click the Start button to access all applications</li>
                <li>Drag windows by their title bars to move them</li>
                <li>Use window controls to minimize, maximize, or close</li>
                <li>Right-click on the desktop for context menu</li>
            </ul>
        </div>

        <div class="help-section">
            <h4>Keyboard Shortcuts</h4>
            <ul class="shortcut-list">
                <li><span>Show Hint</span><kbd>H</kbd></li>
                <li><span>Skip Step</span><kbd>S</kbd></li>
                <li><span>Toggle Sidebar</span><kbd>Tab</kbd></li>
                <li><span>Fullscreen</span><kbd>F11</kbd></li>
            </ul>
        </div>

        <button class="close-help" onclick="hideHelp()">Got it!</button>
    </div>
</div>

<script>
// Mode switching
function switchMode(mode) {
    const url = new URL(window.location.href);
    url.searchParams.set('mode', mode);
    window.location.href = url.toString();
}

// Toggle sidebar
function toggleSidebar() {
    const sidebar = document.getElementById('progressSidebar');
    const icon = document.getElementById('toggleIcon');

    sidebar.classList.toggle('collapsed');

    if (sidebar.classList.contains('collapsed')) {
        icon.classList.remove('fa-chevron-left');
        icon.classList.add('fa-chevron-right');
    } else {
        icon.classList.remove('fa-chevron-right');
        icon.classList.add('fa-chevron-left');
    }
}

// Help modal
function showHelp() {
    document.getElementById('helpModal').classList.add('show');
}

function hideHelp() {
    document.getElementById('helpModal').classList.remove('show');
}

// Close help modal on outside click
document.getElementById('helpModal').addEventListener('click', function(e) {
    if (e.target === this) {
        hideHelp();
    }
});

// Keyboard shortcuts
document.addEventListener('keydown', function(e) {
    if (e.key === 'h' || e.key === 'H') {
        if (window.guidedMode) {
            guidedMode.showHint();
        }
    }
    if (e.key === 's' || e.key === 'S') {
        if (window.guidedMode) {
            guidedMode.skipStep();
        }
    }
    if (e.key === 'Tab' && !e.shiftKey) {
        e.preventDefault();
        toggleSidebar();
    }
    if (e.key === 'Escape') {
        hideHelp();
    }
});

// Update sidebar when step changes
document.addEventListener('guidedmode:stepchange', function(e) {
    const { currentStep, completedSteps, totalSteps } = e.detail;

    // Update progress bar
    const progress = (completedSteps / totalSteps) * 100;
    document.getElementById('progressBarFill').style.width = progress + '%';
    document.getElementById('completedCount').textContent = completedSteps;

    // Update step items
    document.querySelectorAll('.step-item').forEach((item, index) => {
        item.classList.remove('active', 'completed');
        if (index < completedSteps) {
            item.classList.add('completed');
        }
        if (index === currentStep) {
            item.classList.add('active');
        }
    });
});

// Initialize on load
document.addEventListener('DOMContentLoaded', function() {
    // Show first step as active
    const firstStep = document.querySelector('.step-item[data-step="0"]');
    if (firstStep) {
        firstStep.classList.add('active');
    }
});
</script>
@endsection
