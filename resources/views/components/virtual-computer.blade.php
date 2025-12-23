<div class="virtual-computer-container">
    <div class="virtual-computer">
        {{-- Virtual Computer Taskbar --}}
        <div class="vc-taskbar">
            <div class="vc-start-menu">
                <button class="vc-start-button" onclick="toggleStartMenu()">
                    <i class="fab fa-windows"></i>
                    <span>Start</span>
                </button>
                <div class="vc-start-menu-panel" id="startMenuPanel" style="display: none;">
                    <div class="vc-start-apps">
                        <div class="vc-start-app" onclick="openApp('file-explorer')">
                            <i class="fas fa-folder"></i>
                            <span>File Explorer</span>
                        </div>
                        <div class="vc-start-app" onclick="openApp('notepad')">
                            <i class="fas fa-file-alt"></i>
                            <span>Notepad</span>
                        </div>
                        <div class="vc-start-app" onclick="openApp('cmd')">
                            <i class="fas fa-terminal"></i>
                            <span>Command Prompt</span>
                        </div>
                        <div class="vc-start-app" onclick="openApp('settings')">
                            <i class="fas fa-cog"></i>
                            <span>Settings</span>
                        </div>
                        <div class="vc-start-app" onclick="openApp('browser')">
                            <i class="fas fa-globe"></i>
                            <span>Web Browser</span>
                        </div>
                        <div class="vc-start-app" onclick="openApp('calculator')">
                            <i class="fas fa-calculator"></i>
                            <span>Calculator</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="vc-taskbar-apps" id="taskbarApps">
                {{-- Open apps will appear here --}}
            </div>
            <div class="vc-system-tray">
                <span class="vc-tray-icon" title="Network"><i class="fas fa-wifi"></i></span>
                <span class="vc-tray-icon" title="Volume"><i class="fas fa-volume-up"></i></span>
                <span class="vc-clock" id="vcClock"></span>
            </div>
        </div>

        {{-- Virtual Desktop Area --}}
        <div class="vc-desktop" id="vcDesktop">
            <div class="vc-desktop-icons">
                <div class="vc-desktop-icon" ondblclick="openApp('file-explorer')">
                    <i class="fas fa-folder"></i>
                    <span>My Computer</span>
                </div>
                <div class="vc-desktop-icon" ondblclick="openApp('notepad')">
                    <i class="fas fa-file-alt"></i>
                    <span>Notepad</span>
                </div>
                <div class="vc-desktop-icon" ondblclick="openApp('browser')">
                    <i class="fas fa-globe"></i>
                    <span>Browser</span>
                </div>
            </div>

            {{-- Windows will be rendered here dynamically --}}
        </div>

        {{-- Task Checklist Overlay --}}
        <div class="vc-task-panel" id="taskPanel">
            <div class="vc-task-header">
                <h4><i class="fas fa-tasks"></i> Exercise Tasks</h4>
                <button onclick="toggleTaskPanel()" class="vc-task-close">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="vc-task-list" id="taskList">
                {{-- Tasks will be loaded here dynamically --}}
            </div>
            <div class="vc-task-progress">
                <div class="vc-progress-bar">
                    <div class="vc-progress-fill" id="taskProgress" style="width: 0%"></div>
                </div>
                <p class="vc-progress-text" id="taskProgressText">0% Complete</p>
            </div>
        </div>

        <button class="vc-task-toggle" onclick="toggleTaskPanel()" title="Show/Hide Tasks">
            <i class="fas fa-clipboard-list"></i>
        </button>
    </div>
</div>

<style>
.virtual-computer-container {
    max-width: 1400px;
    margin: 2rem auto;
    background: white;
    border-radius: 12px;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    overflow: hidden;
}

.virtual-computer {
    position: relative;
    width: 100%;
    height: 700px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    display: flex;
    flex-direction: column;
}

/* Taskbar */
.vc-taskbar {
    height: 48px;
    background: rgba(0, 0, 0, 0.8);
    backdrop-filter: blur(10px);
    display: flex;
    align-items: center;
    padding: 0 0.5rem;
    gap: 0.5rem;
    position: relative;
    z-index: 1000;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.vc-start-menu {
    position: relative;
}

.vc-start-button {
    background: rgba(255, 255, 255, 0.1);
    border: none;
    color: white;
    padding: 0.5rem 1rem;
    border-radius: 4px;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-weight: 600;
    transition: background 0.2s;
}

.vc-start-button:hover {
    background: rgba(255, 255, 255, 0.2);
}

.vc-start-menu-panel {
    position: absolute;
    bottom: 52px;
    left: 0;
    width: 320px;
    background: rgba(30, 30, 30, 0.95);
    backdrop-filter: blur(20px);
    border-radius: 8px;
    padding: 1rem;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.5);
}

.vc-start-apps {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 0.5rem;
}

.vc-start-app {
    padding: 1rem;
    background: rgba(255, 255, 255, 0.05);
    border-radius: 6px;
    cursor: pointer;
    transition: all 0.2s;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.5rem;
    color: white;
}

.vc-start-app:hover {
    background: rgba(255, 255, 255, 0.1);
    transform: translateY(-2px);
}

.vc-start-app i {
    font-size: 1.5rem;
}

.vc-start-app span {
    font-size: 0.85rem;
}

.vc-taskbar-apps {
    flex: 1;
    display: flex;
    gap: 0.25rem;
    overflow-x: auto;
}

.vc-taskbar-app {
    background: rgba(255, 255, 255, 0.1);
    border: none;
    color: white;
    padding: 0.5rem 1rem;
    border-radius: 4px;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    min-width: 150px;
    transition: background 0.2s;
}

.vc-taskbar-app:hover {
    background: rgba(255, 255, 255, 0.15);
}

.vc-taskbar-app.active {
    background: rgba(255, 255, 255, 0.2);
    border-bottom: 2px solid #3b82f6;
}

.vc-system-tray {
    display: flex;
    align-items: center;
    gap: 1rem;
    color: white;
    padding: 0 0.5rem;
}

.vc-tray-icon {
    cursor: pointer;
    transition: opacity 0.2s;
}

.vc-tray-icon:hover {
    opacity: 0.7;
}

.vc-clock {
    font-size: 0.85rem;
    font-weight: 500;
    min-width: 60px;
}

/* Desktop */
.vc-desktop {
    flex: 1;
    position: relative;
    overflow: hidden;
    padding: 1rem;
}

.vc-desktop-icons {
    display: grid;
    grid-template-columns: repeat(auto-fill, 80px);
    gap: 1rem;
    padding: 1rem;
}

.vc-desktop-icon {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.5rem;
    padding: 0.75rem;
    border-radius: 6px;
    cursor: pointer;
    transition: background 0.2s;
    color: white;
    text-align: center;
}

.vc-desktop-icon:hover {
    background: rgba(255, 255, 255, 0.1);
}

.vc-desktop-icon i {
    font-size: 2rem;
    filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.3));
}

.vc-desktop-icon span {
    font-size: 0.75rem;
    text-shadow: 0 1px 3px rgba(0, 0, 0, 0.5);
}

/* Window */
.vc-window {
    position: absolute;
    background: white;
    border-radius: 8px;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
    min-width: 400px;
    min-height: 300px;
    display: flex;
    flex-direction: column;
    z-index: 10;
}

.vc-window.maximized {
    top: 0 !important;
    left: 0 !important;
    width: 100% !important;
    height: calc(100% - 48px) !important;
    border-radius: 0;
}

.vc-window-titlebar {
    background: linear-gradient(to bottom, #f0f0f0, #e0e0e0);
    padding: 0.5rem 1rem;
    border-radius: 8px 8px 0 0;
    display: flex;
    align-items: center;
    justify-content: space-between;
    cursor: move;
    user-select: none;
    border-bottom: 1px solid #ccc;
}

.vc-window-title {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-weight: 600;
    font-size: 0.9rem;
    color: #333;
}

.vc-window-controls {
    display: flex;
    gap: 0.5rem;
}

.vc-window-control {
    width: 28px;
    height: 28px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: background 0.2s;
    background: rgba(0, 0, 0, 0.05);
}

.vc-window-control:hover {
    background: rgba(0, 0, 0, 0.1);
}

.vc-window-control.close:hover {
    background: #e74c3c;
    color: white;
}

.vc-window-control.maximize:hover {
    background: #3498db;
    color: white;
}

.vc-window-control.minimize:hover {
    background: #f39c12;
    color: white;
}

.vc-window-content {
    flex: 1;
    overflow: auto;
    background: white;
    padding: 1rem;
    border-radius: 0 0 8px 8px;
}

/* Task Panel */
.vc-task-panel {
    position: absolute;
    right: 1rem;
    top: 1rem;
    width: 320px;
    background: rgba(255, 255, 255, 0.98);
    border-radius: 12px;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
    z-index: 999;
    max-height: calc(100% - 100px);
    display: flex;
    flex-direction: column;
}

.vc-task-header {
    padding: 1rem;
    border-bottom: 2px solid #e5e7eb;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.vc-task-header h4 {
    margin: 0;
    font-size: 1rem;
    color: #673AB7;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.vc-task-close {
    background: none;
    border: none;
    font-size: 1.2rem;
    cursor: pointer;
    color: #6b7280;
    transition: color 0.2s;
}

.vc-task-close:hover {
    color: #ef4444;
}

.vc-task-list {
    flex: 1;
    overflow-y: auto;
    padding: 1rem;
}

.vc-task-item {
    padding: 0.75rem;
    background: #f3f4f6;
    border-radius: 8px;
    margin-bottom: 0.5rem;
    display: flex;
    align-items: flex-start;
    gap: 0.75rem;
    transition: all 0.2s;
}

.vc-task-item.completed {
    background: #d1fae5;
    opacity: 0.7;
}

.vc-task-checkbox {
    width: 20px;
    height: 20px;
    border: 2px solid #673AB7;
    border-radius: 4px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    margin-top: 2px;
}

.vc-task-checkbox.checked {
    background: #673AB7;
    color: white;
}

.vc-task-text {
    flex: 1;
    font-size: 0.9rem;
    line-height: 1.4;
}

.vc-task-item.completed .vc-task-text {
    text-decoration: line-through;
    color: #6b7280;
}

.vc-task-progress {
    padding: 1rem;
    border-top: 2px solid #e5e7eb;
}

.vc-progress-bar {
    width: 100%;
    height: 8px;
    background: #e5e7eb;
    border-radius: 4px;
    overflow: hidden;
    margin-bottom: 0.5rem;
}

.vc-progress-fill {
    height: 100%;
    background: linear-gradient(to right, #673AB7, #9c27b0);
    transition: width 0.3s ease;
}

.vc-progress-text {
    text-align: center;
    font-size: 0.85rem;
    font-weight: 600;
    color: #673AB7;
    margin: 0;
}

.vc-task-toggle {
    position: absolute;
    right: 1rem;
    top: 1rem;
    width: 50px;
    height: 50px;
    background: rgba(255, 255, 255, 0.95);
    border: none;
    border-radius: 50%;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.3rem;
    color: #673AB7;
    transition: all 0.3s;
    z-index: 998;
}

.vc-task-toggle:hover {
    transform: scale(1.1);
    box-shadow: 0 6px 16px rgba(0, 0, 0, 0.3);
}

/* Responsive */
@media (max-width: 768px) {
    .virtual-computer {
        height: 500px;
    }

    .vc-task-panel {
        width: 280px;
    }

    .vc-window {
        min-width: 300px;
    }
}
</style>

<script>
// Virtual Computer State
const vcState = {
    windows: [],
    nextWindowId: 1,
    taskPanelVisible: true,
    tasks: @json($tasks ?? []),
    zIndex: 10
};

// Initialize clock
function updateClock() {
    const now = new Date();
    const hours = String(now.getHours()).padStart(2, '0');
    const minutes = String(now.getMinutes()).padStart(2, '0');
    document.getElementById('vcClock').textContent = `${hours}:${minutes}`;
}
updateClock();
setInterval(updateClock, 1000);

// Start Menu
function toggleStartMenu() {
    const panel = document.getElementById('startMenuPanel');
    panel.style.display = panel.style.display === 'none' ? 'block' : 'none';
}

// Close start menu when clicking outside
document.addEventListener('click', function(e) {
    const startMenu = document.querySelector('.vc-start-menu');
    const panel = document.getElementById('startMenuPanel');
    if (!startMenu.contains(e.target) && panel.style.display !== 'none') {
        panel.style.display = 'none';
    }
});

// Open Application
function openApp(appType) {
    toggleStartMenu();

    const appConfig = {
        'file-explorer': {
            title: 'File Explorer',
            icon: 'fas fa-folder',
            content: generateFileExplorer()
        },
        'notepad': {
            title: 'Notepad',
            icon: 'fas fa-file-alt',
            content: '<textarea style="width: 100%; height: 100%; border: none; font-family: monospace; resize: none;" placeholder="Type here..."></textarea>'
        },
        'cmd': {
            title: 'Command Prompt',
            icon: 'fas fa-terminal',
            content: generateCommandPrompt()
        },
        'settings': {
            title: 'Settings',
            icon: 'fas fa-cog',
            content: generateSettings()
        },
        'browser': {
            title: 'Web Browser',
            icon: 'fas fa-globe',
            content: generateBrowser()
        },
        'calculator': {
            title: 'Calculator',
            icon: 'fas fa-calculator',
            content: generateCalculator()
        }
    };

    const config = appConfig[appType];
    if (!config) return;

    createWindow(config.title, config.icon, config.content);
}

// Create Window
function createWindow(title, icon, content) {
    const windowId = vcState.nextWindowId++;
    const desktop = document.getElementById('vcDesktop');

    const windowEl = document.createElement('div');
    windowEl.className = 'vc-window';
    windowEl.id = `window-${windowId}`;
    windowEl.style.left = `${50 + (windowId * 20)}px`;
    windowEl.style.top = `${50 + (windowId * 20)}px`;
    windowEl.style.width = '600px';
    windowEl.style.height = '450px';
    windowEl.style.zIndex = ++vcState.zIndex;

    windowEl.innerHTML = `
        <div class="vc-window-titlebar" onmousedown="startDrag(event, ${windowId})">
            <div class="vc-window-title">
                <i class="${icon}"></i>
                <span>${title}</span>
            </div>
            <div class="vc-window-controls">
                <button class="vc-window-control minimize" onclick="minimizeWindow(${windowId})">
                    <i class="fas fa-window-minimize"></i>
                </button>
                <button class="vc-window-control maximize" onclick="maximizeWindow(${windowId})">
                    <i class="fas fa-window-maximize"></i>
                </button>
                <button class="vc-window-control close" onclick="closeWindow(${windowId})">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
        <div class="vc-window-content">${content}</div>
    `;

    desktop.appendChild(windowEl);
    vcState.windows.push({ id: windowId, title, icon });
    updateTaskbar();

    // Focus window on click
    windowEl.addEventListener('mousedown', () => {
        windowEl.style.zIndex = ++vcState.zIndex;
    });
}

// Window Controls
function closeWindow(windowId) {
    const window = document.getElementById(`window-${windowId}`);
    if (window) {
        window.remove();
        vcState.windows = vcState.windows.filter(w => w.id !== windowId);
        updateTaskbar();
    }
}

function minimizeWindow(windowId) {
    const window = document.getElementById(`window-${windowId}`);
    if (window) {
        window.style.display = 'none';
    }
}

function maximizeWindow(windowId) {
    const window = document.getElementById(`window-${windowId}`);
    if (window) {
        window.classList.toggle('maximized');
    }
}

// Window Dragging
let dragState = { active: false, windowId: null, offsetX: 0, offsetY: 0 };

function startDrag(e, windowId) {
    const window = document.getElementById(`window-${windowId}`);
    if (window.classList.contains('maximized')) return;

    dragState = {
        active: true,
        windowId: windowId,
        offsetX: e.clientX - window.offsetLeft,
        offsetY: e.clientY - window.offsetTop
    };

    window.style.zIndex = ++vcState.zIndex;
}

document.addEventListener('mousemove', function(e) {
    if (!dragState.active) return;

    const window = document.getElementById(`window-${dragState.windowId}`);
    if (window) {
        window.style.left = `${e.clientX - dragState.offsetX}px`;
        window.style.top = `${e.clientY - dragState.offsetY}px`;
    }
});

document.addEventListener('mouseup', function() {
    dragState.active = false;
});

// Update Taskbar
function updateTaskbar() {
    const taskbarApps = document.getElementById('taskbarApps');
    taskbarApps.innerHTML = vcState.windows.map(w => `
        <button class="vc-taskbar-app" onclick="focusWindow(${w.id})">
            <i class="${w.icon}"></i>
            <span>${w.title}</span>
        </button>
    `).join('');
}

function focusWindow(windowId) {
    const window = document.getElementById(`window-${windowId}`);
    if (window) {
        window.style.display = 'flex';
        window.style.zIndex = ++vcState.zIndex;
    }
}

// Task Panel
function toggleTaskPanel() {
    const panel = document.getElementById('taskPanel');
    const toggle = document.querySelector('.vc-task-toggle');
    vcState.taskPanelVisible = !vcState.taskPanelVisible;
    panel.style.display = vcState.taskPanelVisible ? 'flex' : 'none';
    toggle.style.display = vcState.taskPanelVisible ? 'none' : 'flex';
}

function loadTasks() {
    const taskList = document.getElementById('taskList');
    if (!vcState.tasks || vcState.tasks.length === 0) {
        taskList.innerHTML = '<p style="text-align: center; color: #6b7280;">No tasks available</p>';
        return;
    }

    taskList.innerHTML = vcState.tasks.map((task, index) => `
        <div class="vc-task-item ${task.completed ? 'completed' : ''}" data-task-id="${index}">
            <div class="vc-task-checkbox ${task.completed ? 'checked' : ''}" onclick="toggleTask(${index})">
                ${task.completed ? '<i class="fas fa-check"></i>' : ''}
            </div>
            <div class="vc-task-text">${task.text}</div>
        </div>
    `).join('');

    updateProgress();
}

function toggleTask(taskId) {
    vcState.tasks[taskId].completed = !vcState.tasks[taskId].completed;
    loadTasks();

    // Save progress to server
    saveTaskProgress();
}

function updateProgress() {
    const completed = vcState.tasks.filter(t => t.completed).length;
    const total = vcState.tasks.length;
    const percentage = total > 0 ? Math.round((completed / total) * 100) : 0;

    document.getElementById('taskProgress').style.width = `${percentage}%`;
    document.getElementById('taskProgressText').textContent = `${percentage}% Complete (${completed}/${total})`;
}

function saveTaskProgress() {
    // This would save to the server
    console.log('Saving task progress:', vcState.tasks);
}

// App Content Generators
function generateFileExplorer() {
    return `
        <div style="display: flex; flex-direction: column; height: 100%;">
            <div style="background: #f3f4f6; padding: 0.5rem; border-bottom: 1px solid #e5e7eb;">
                <div style="display: flex; gap: 0.5rem; align-items: center;">
                    <button style="padding: 0.25rem 0.5rem; background: white; border: 1px solid #d1d5db; border-radius: 4px; cursor: pointer;">
                        <i class="fas fa-arrow-left"></i>
                    </button>
                    <button style="padding: 0.25rem 0.5rem; background: white; border: 1px solid #d1d5db; border-radius: 4px; cursor: pointer;">
                        <i class="fas fa-arrow-right"></i>
                    </button>
                    <input type="text" value="C:\\Users\\Student\\Documents" style="flex: 1; padding: 0.25rem 0.5rem; border: 1px solid #d1d5db; border-radius: 4px;">
                </div>
            </div>
            <div style="flex: 1; padding: 1rem;">
                <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(80px, 1fr)); gap: 1rem;">
                    <div style="text-align: center; cursor: pointer;">
                        <i class="fas fa-folder" style="font-size: 3rem; color: #fbbf24;"></i>
                        <div style="font-size: 0.85rem; margin-top: 0.25rem;">Documents</div>
                    </div>
                    <div style="text-align: center; cursor: pointer;">
                        <i class="fas fa-folder" style="font-size: 3rem; color: #fbbf24;"></i>
                        <div style="font-size: 0.85rem; margin-top: 0.25rem;">Pictures</div>
                    </div>
                    <div style="text-align: center; cursor: pointer;">
                        <i class="fas fa-file-word" style="font-size: 3rem; color: #3b82f6;"></i>
                        <div style="font-size: 0.85rem; margin-top: 0.25rem;">Report.docx</div>
                    </div>
                </div>
            </div>
        </div>
    `;
}

function generateCommandPrompt() {
    return `
        <div style="background: #000; color: #0f0; font-family: 'Courier New', monospace; padding: 1rem; height: 100%; overflow-y: auto;">
            <div>Microsoft Windows [Version 10.0.19041.1234]</div>
            <div>(c) Microsoft Corporation. All rights reserved.</div>
            <div style="margin-top: 1rem;">C:\\Users\\Student&gt;<span style="animation: blink 1s infinite;">_</span></div>
        </div>
    `;
}

function generateSettings() {
    return `
        <div style="padding: 1rem;">
            <h3 style="margin-top: 0;"><i class="fas fa-cog"></i> System Settings</h3>
            <div style="display: flex; flex-direction: column; gap: 1rem;">
                <div style="padding: 1rem; background: #f3f4f6; border-radius: 8px;">
                    <div style="font-weight: 600; margin-bottom: 0.5rem;">Display</div>
                    <div style="font-size: 0.9rem; color: #6b7280;">Adjust screen resolution and brightness</div>
                </div>
                <div style="padding: 1rem; background: #f3f4f6; border-radius: 8px;">
                    <div style="font-weight: 600; margin-bottom: 0.5rem;">Network & Internet</div>
                    <div style="font-size: 0.9rem; color: #6b7280;">Wi-Fi, airplane mode, VPN</div>
                </div>
                <div style="padding: 1rem; background: #f3f4f6; border-radius: 8px;">
                    <div style="font-weight: 600; margin-bottom: 0.5rem;">Personalization</div>
                    <div style="font-size: 0.9rem; color: #6b7280;">Background, colors, themes</div>
                </div>
            </div>
        </div>
    `;
}

function generateBrowser() {
    return `
        <div style="display: flex; flex-direction: column; height: 100%;">
            <div style="background: #f3f4f6; padding: 0.5rem; border-bottom: 1px solid #e5e7eb;">
                <div style="display: flex; gap: 0.5rem; align-items: center;">
                    <button style="padding: 0.25rem 0.5rem; background: white; border: 1px solid #d1d5db; border-radius: 4px;">
                        <i class="fas fa-arrow-left"></i>
                    </button>
                    <button style="padding: 0.25rem 0.5rem; background: white; border: 1px solid #d1d5db; border-radius: 4px;">
                        <i class="fas fa-arrow-right"></i>
                    </button>
                    <button style="padding: 0.25rem 0.5rem; background: white; border: 1px solid #d1d5db; border-radius: 4px;">
                        <i class="fas fa-sync-alt"></i>
                    </button>
                    <input type="text" value="https://www.example.com" style="flex: 1; padding: 0.25rem 0.5rem; border: 1px solid #d1d5db; border-radius: 4px;">
                </div>
            </div>
            <div style="flex: 1; background: white; padding: 2rem; text-align: center;">
                <h2>Welcome to Web Browser</h2>
                <p style="color: #6b7280;">This is a virtual web browser for learning purposes.</p>
            </div>
        </div>
    `;
}

function generateCalculator() {
    return `
        <div style="display: flex; flex-direction: column; height: 100%; max-width: 320px; margin: auto;">
            <div style="background: #1f2937; color: white; padding: 1rem; text-align: right; font-size: 2rem; font-family: monospace; border-bottom: 2px solid #374151;">
                0
            </div>
            <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 1px; background: #374151; flex: 1;">
                ${['7','8','9','/','4','5','6','*','1','2','3','-','0','.','=','+'].map(btn =>
                    `<button style="background: ${btn === '=' ? '#3b82f6' : '#1f2937'}; color: white; border: none; font-size: 1.5rem; cursor: pointer; transition: opacity 0.2s;" onmouseover="this.style.opacity='0.8'" onmouseout="this.style.opacity='1'">${btn}</button>`
                ).join('')}
            </div>
        </div>
    `;
}

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    loadTasks();
});
</script>
