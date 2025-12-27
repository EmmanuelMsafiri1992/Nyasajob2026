/**
 * Windows 11 Virtual Desktop Core
 * Handles window management, app launching, taskbar, and desktop interactions
 */

class VirtualDesktop {
    constructor(config = {}) {
        this.config = {
            desktopIcons: config.desktopIcons || [],
            taskbarApps: config.taskbarApps || [],
            startMenuApps: config.startMenuApps || [],
            filesystem: config.filesystem || {},
            mode: config.mode || 'guided',
            showTaskbar: config.showTaskbar !== false,
            showStartMenu: config.showStartMenu !== false,
            showDesktopIcons: config.showDesktopIcons !== false,
            allowWindowResize: config.allowWindowResize !== false,
            allowWindowMove: config.allowWindowMove !== false,
            disabledApps: config.disabledApps || [],
            onAction: config.onAction || null,
            ...config
        };

        this.state = {
            windows: [],
            nextWindowId: 1,
            focusedWindowId: null,
            startMenuOpen: false,
            selectedDesktopIcon: null,
            zIndex: 100,
            currentPath: 'C:/Users/Learner',
            clipboard: null,
        };

        this.apps = this.getAppDefinitions();
        this.init();
    }

    init() {
        this.updateClock();
        setInterval(() => this.updateClock(), 1000);
        this.bindEvents();
    }

    getAppDefinitions() {
        return {
            'file-explorer': {
                name: 'File Explorer',
                icon: 'fa-folder',
                component: 'file-explorer',
                defaultSize: { width: 800, height: 500 }
            },
            'notepad': {
                name: 'Notepad',
                icon: 'fa-file-alt',
                component: 'notepad',
                defaultSize: { width: 600, height: 400 }
            },
            'calculator': {
                name: 'Calculator',
                icon: 'fa-calculator',
                component: 'calculator',
                defaultSize: { width: 320, height: 500 }
            },
            'edge': {
                name: 'Microsoft Edge',
                icon: 'fa-globe',
                component: 'browser',
                defaultSize: { width: 900, height: 600 }
            },
            'cmd': {
                name: 'Command Prompt',
                icon: 'fa-terminal',
                component: 'cmd',
                defaultSize: { width: 700, height: 450 }
            },
            'settings': {
                name: 'Settings',
                icon: 'fa-cog',
                component: 'settings',
                defaultSize: { width: 900, height: 600 }
            },
            'recycle-bin': {
                name: 'Recycle Bin',
                icon: 'fa-trash',
                component: 'recycle-bin',
                defaultSize: { width: 600, height: 400 }
            }
        };
    }

    bindEvents() {
        // Desktop click - deselect icons
        document.querySelector('.desktop-area')?.addEventListener('click', (e) => {
            if (e.target.classList.contains('desktop-area')) {
                this.deselectAllIcons();
                this.closeStartMenu();
            }
        });

        // Close start menu when clicking outside
        document.addEventListener('click', (e) => {
            if (!e.target.closest('.start-menu') && !e.target.closest('.start-btn')) {
                this.closeStartMenu();
            }
        });

        // Keyboard shortcuts
        document.addEventListener('keydown', (e) => {
            // Windows key - toggle start menu
            if (e.key === 'Meta' || (e.ctrlKey && e.key === 'Escape')) {
                this.toggleStartMenu();
            }
            // Escape - close focused window or start menu
            if (e.key === 'Escape') {
                if (this.state.startMenuOpen) {
                    this.closeStartMenu();
                }
            }
        });
    }

    // Desktop Icons
    selectIcon(iconId) {
        this.deselectAllIcons();
        const icon = document.querySelector(`.desktop-icon[data-id="${iconId}"]`);
        if (icon) {
            icon.classList.add('selected');
            this.state.selectedDesktopIcon = iconId;
        }
        this.triggerAction('select', { target: iconId, type: 'desktop-icon' });
    }

    deselectAllIcons() {
        document.querySelectorAll('.desktop-icon').forEach(icon => {
            icon.classList.remove('selected');
        });
        this.state.selectedDesktopIcon = null;
    }

    handleIconDoubleClick(iconId, appId, path = null) {
        this.triggerAction('double_click', { target: iconId, app: appId, path });
        if (appId) {
            this.openApp(appId, { path });
        }
    }

    // Start Menu
    toggleStartMenu() {
        if (this.state.startMenuOpen) {
            this.closeStartMenu();
        } else {
            this.openStartMenu();
        }
    }

    openStartMenu() {
        const menu = document.querySelector('.start-menu');
        if (menu) {
            menu.classList.add('open');
            this.state.startMenuOpen = true;
            this.triggerAction('click', { target: '#start-button', type: 'start-menu' });
        }
    }

    closeStartMenu() {
        const menu = document.querySelector('.start-menu');
        if (menu) {
            menu.classList.remove('open');
            this.state.startMenuOpen = false;
        }
    }

    // Window Management
    openApp(appId, options = {}) {
        if (this.config.disabledApps.includes(appId)) {
            console.log('App is disabled:', appId);
            return null;
        }

        const app = this.apps[appId];
        if (!app) {
            console.error('Unknown app:', appId);
            return null;
        }

        const windowId = this.state.nextWindowId++;
        const window = {
            id: windowId,
            appId,
            title: options.title || app.name,
            icon: app.icon,
            width: options.width || app.defaultSize.width,
            height: options.height || app.defaultSize.height,
            x: options.x || 100 + (windowId * 30) % 200,
            y: options.y || 50 + (windowId * 30) % 150,
            minimized: false,
            maximized: false,
            data: options.data || {}
        };

        this.state.windows.push(window);
        this.renderWindow(window);
        this.focusWindow(windowId);
        this.updateTaskbar();
        this.closeStartMenu();

        this.triggerAction('open_app', { app: appId, windowId, options });

        return windowId;
    }

    renderWindow(window) {
        const app = this.apps[window.appId];
        const windowEl = document.createElement('div');
        windowEl.className = 'window';
        windowEl.id = `window-${window.id}`;
        windowEl.style.cssText = `
            left: ${window.x}px;
            top: ${window.y}px;
            width: ${window.width}px;
            height: ${window.height}px;
            z-index: ${++this.state.zIndex};
        `;

        windowEl.innerHTML = `
            <div class="window-titlebar" data-window-id="${window.id}">
                <div class="window-titlebar-left">
                    <div class="window-icon"><i class="fas ${app.icon}"></i></div>
                    <div class="window-title">${window.title}</div>
                </div>
                <div class="window-controls">
                    <div class="window-control minimize" data-window-id="${window.id}" onclick="virtualDesktop.minimizeWindow(${window.id})">
                        <i class="fas fa-minus"></i>
                    </div>
                    <div class="window-control maximize" data-window-id="${window.id}" onclick="virtualDesktop.toggleMaximize(${window.id})">
                        <i class="far fa-square"></i>
                    </div>
                    <div class="window-control close" data-window-id="${window.id}" onclick="virtualDesktop.closeWindow(${window.id})">
                        <i class="fas fa-times"></i>
                    </div>
                </div>
            </div>
            <div class="window-content">
                ${this.getAppContent(window)}
            </div>
        `;

        // Make window draggable
        if (this.config.allowWindowMove) {
            this.makeDraggable(windowEl, window);
        }

        // Focus on click
        windowEl.addEventListener('mousedown', () => {
            this.focusWindow(window.id);
        });

        document.querySelector('.desktop-area').appendChild(windowEl);

        // Initialize app-specific functionality
        this.initAppFunctionality(window);
    }

    getAppContent(window) {
        const app = this.apps[window.appId];

        switch (app.component) {
            case 'file-explorer':
                return this.getFileExplorerContent(window);
            case 'notepad':
                return this.getNotepadContent(window);
            case 'calculator':
                return this.getCalculatorContent(window);
            case 'browser':
                return this.getBrowserContent(window);
            case 'cmd':
                return this.getCmdContent(window);
            case 'settings':
                return this.getSettingsContent(window);
            case 'recycle-bin':
                return this.getRecycleBinContent(window);
            default:
                return '<div style="padding: 20px;">App content not available</div>';
        }
    }

    getFileExplorerContent(window) {
        return `
            <div class="file-explorer">
                <div class="file-explorer-toolbar">
                    <div class="file-explorer-toolbar-btn" onclick="virtualDesktop.fileExplorerBack(${window.id})">
                        <i class="fas fa-arrow-left"></i>
                    </div>
                    <div class="file-explorer-toolbar-btn" onclick="virtualDesktop.fileExplorerForward(${window.id})">
                        <i class="fas fa-arrow-right"></i>
                    </div>
                    <div class="file-explorer-address" id="fe-address-${window.id}">
                        <i class="fas fa-folder" style="margin-right: 8px;"></i>
                        <span>This PC</span>
                    </div>
                </div>
                <div class="file-explorer-body">
                    <div class="file-explorer-sidebar">
                        <div class="file-explorer-sidebar-item" onclick="virtualDesktop.navigateTo(${window.id}, 'desktop')">
                            <i class="fas fa-desktop"></i> Desktop
                        </div>
                        <div class="file-explorer-sidebar-item" onclick="virtualDesktop.navigateTo(${window.id}, 'documents')">
                            <i class="fas fa-file-alt"></i> Documents
                        </div>
                        <div class="file-explorer-sidebar-item" onclick="virtualDesktop.navigateTo(${window.id}, 'downloads')">
                            <i class="fas fa-download"></i> Downloads
                        </div>
                        <div class="file-explorer-sidebar-item" onclick="virtualDesktop.navigateTo(${window.id}, 'pictures')">
                            <i class="fas fa-image"></i> Pictures
                        </div>
                        <div class="file-explorer-sidebar-item active" onclick="virtualDesktop.navigateTo(${window.id}, 'this-pc')">
                            <i class="fas fa-desktop"></i> This PC
                        </div>
                    </div>
                    <div class="file-explorer-main" id="fe-main-${window.id}">
                        <div class="file-item" ondblclick="virtualDesktop.openFolder(${window.id}, 'C:')">
                            <div class="icon"><i class="fas fa-hdd"></i></div>
                            <div class="name">Local Disk (C:)</div>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }

    getNotepadContent(window) {
        return `
            <div class="notepad">
                <div class="notepad-menu">
                    <div class="notepad-menu-item">File</div>
                    <div class="notepad-menu-item">Edit</div>
                    <div class="notepad-menu-item">Format</div>
                    <div class="notepad-menu-item">View</div>
                    <div class="notepad-menu-item">Help</div>
                </div>
                <textarea class="notepad-textarea" id="notepad-${window.id}" placeholder="Start typing..."
                    oninput="virtualDesktop.triggerAction('type', {target: '#notepad-${window.id}', text: this.value})"></textarea>
            </div>
        `;
    }

    getCalculatorContent(window) {
        return `
            <div class="calculator" id="calc-${window.id}">
                <div class="calculator-display">
                    <div class="expression" id="calc-expr-${window.id}"></div>
                    <div class="result" id="calc-result-${window.id}">0</div>
                </div>
                <div class="calculator-buttons">
                    <button class="calc-btn" onclick="virtualDesktop.calcInput(${window.id}, 'C')">C</button>
                    <button class="calc-btn" onclick="virtualDesktop.calcInput(${window.id}, 'CE')">CE</button>
                    <button class="calc-btn" onclick="virtualDesktop.calcInput(${window.id}, 'DEL')"><i class="fas fa-backspace"></i></button>
                    <button class="calc-btn operator" onclick="virtualDesktop.calcInput(${window.id}, '/')">÷</button>
                    <button class="calc-btn" onclick="virtualDesktop.calcInput(${window.id}, '7')">7</button>
                    <button class="calc-btn" onclick="virtualDesktop.calcInput(${window.id}, '8')">8</button>
                    <button class="calc-btn" onclick="virtualDesktop.calcInput(${window.id}, '9')">9</button>
                    <button class="calc-btn operator" onclick="virtualDesktop.calcInput(${window.id}, '*')">×</button>
                    <button class="calc-btn" onclick="virtualDesktop.calcInput(${window.id}, '4')">4</button>
                    <button class="calc-btn" onclick="virtualDesktop.calcInput(${window.id}, '5')">5</button>
                    <button class="calc-btn" onclick="virtualDesktop.calcInput(${window.id}, '6')">6</button>
                    <button class="calc-btn operator" onclick="virtualDesktop.calcInput(${window.id}, '-')">−</button>
                    <button class="calc-btn" onclick="virtualDesktop.calcInput(${window.id}, '1')">1</button>
                    <button class="calc-btn" onclick="virtualDesktop.calcInput(${window.id}, '2')">2</button>
                    <button class="calc-btn" onclick="virtualDesktop.calcInput(${window.id}, '3')">3</button>
                    <button class="calc-btn operator" onclick="virtualDesktop.calcInput(${window.id}, '+')">+</button>
                    <button class="calc-btn" onclick="virtualDesktop.calcInput(${window.id}, '+/-')">±</button>
                    <button class="calc-btn" onclick="virtualDesktop.calcInput(${window.id}, '0')">0</button>
                    <button class="calc-btn" onclick="virtualDesktop.calcInput(${window.id}, '.')">.</button>
                    <button class="calc-btn equals" onclick="virtualDesktop.calcInput(${window.id}, '=')">=</button>
                </div>
            </div>
        `;
    }

    getBrowserContent(window) {
        return `
            <div class="browser">
                <div class="browser-toolbar">
                    <div class="browser-nav-btn"><i class="fas fa-arrow-left"></i></div>
                    <div class="browser-nav-btn"><i class="fas fa-arrow-right"></i></div>
                    <div class="browser-nav-btn"><i class="fas fa-redo"></i></div>
                    <div class="browser-address">
                        <i class="fas fa-lock" style="margin-right: 8px; color: #10b981;"></i>
                        <input type="text" value="https://www.example.com" id="browser-url-${window.id}">
                    </div>
                </div>
                <div class="browser-content" id="browser-content-${window.id}" style="padding: 40px; text-align: center;">
                    <h2 style="color: #1a1a1a; margin-bottom: 16px;">Welcome to the Web Browser</h2>
                    <p style="color: #5c5c5c;">This is a simulated browser for learning purposes.</p>
                </div>
            </div>
        `;
    }

    getCmdContent(window) {
        return `
            <div class="cmd" id="cmd-${window.id}">
                <div class="cmd-output">Microsoft Windows [Version 10.0.22000.0]
(c) Microsoft Corporation. All rights reserved.

</div>
                <div class="cmd-input-line">
                    <span class="cmd-prompt">C:\\Users\\Learner></span>
                    <input type="text" class="cmd-input" id="cmd-input-${window.id}"
                        onkeypress="if(event.key==='Enter') virtualDesktop.executeCommand(${window.id})">
                </div>
            </div>
        `;
    }

    getSettingsContent(window) {
        return `
            <div class="settings">
                <div class="settings-sidebar">
                    <div class="settings-search">
                        <input type="text" placeholder="Find a setting">
                    </div>
                    <div class="settings-menu-item active">
                        <i class="fas fa-home"></i> System
                    </div>
                    <div class="settings-menu-item">
                        <i class="fas fa-bluetooth"></i> Bluetooth & devices
                    </div>
                    <div class="settings-menu-item">
                        <i class="fas fa-wifi"></i> Network & internet
                    </div>
                    <div class="settings-menu-item">
                        <i class="fas fa-palette"></i> Personalization
                    </div>
                    <div class="settings-menu-item">
                        <i class="fas fa-mobile-alt"></i> Apps
                    </div>
                    <div class="settings-menu-item">
                        <i class="fas fa-user"></i> Accounts
                    </div>
                    <div class="settings-menu-item">
                        <i class="fas fa-clock"></i> Time & language
                    </div>
                    <div class="settings-menu-item">
                        <i class="fas fa-gamepad"></i> Gaming
                    </div>
                    <div class="settings-menu-item">
                        <i class="fas fa-universal-access"></i> Accessibility
                    </div>
                    <div class="settings-menu-item">
                        <i class="fas fa-shield-alt"></i> Privacy & security
                    </div>
                </div>
                <div class="settings-content">
                    <div class="settings-title">System</div>
                    <p style="color: var(--win11-text-secondary);">
                        This is a simulated Settings app for learning purposes.
                    </p>
                </div>
            </div>
        `;
    }

    getRecycleBinContent(window) {
        return `
            <div class="file-explorer">
                <div class="file-explorer-toolbar">
                    <div class="file-explorer-toolbar-btn">Empty Recycle Bin</div>
                </div>
                <div class="file-explorer-body">
                    <div class="file-explorer-main" style="flex: 1; justify-content: center; align-items: center;">
                        <div style="text-align: center; color: var(--win11-text-secondary);">
                            <i class="fas fa-trash" style="font-size: 48px; margin-bottom: 16px;"></i>
                            <p>Recycle Bin is empty</p>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }

    initAppFunctionality(window) {
        // Initialize calculator state
        if (window.appId === 'calculator') {
            window.data.calcValue = '0';
            window.data.calcExpression = '';
            window.data.calcOperator = null;
            window.data.calcPrevValue = null;
        }

        // Focus CMD input
        if (window.appId === 'cmd') {
            setTimeout(() => {
                document.getElementById(`cmd-input-${window.id}`)?.focus();
            }, 100);
        }
    }

    // Window operations
    focusWindow(windowId) {
        document.querySelectorAll('.window').forEach(w => w.classList.remove('focused'));
        const windowEl = document.getElementById(`window-${windowId}`);
        if (windowEl) {
            windowEl.classList.add('focused');
            windowEl.style.zIndex = ++this.state.zIndex;
            this.state.focusedWindowId = windowId;
        }
    }

    minimizeWindow(windowId) {
        const windowEl = document.getElementById(`window-${windowId}`);
        const window = this.state.windows.find(w => w.id === windowId);
        if (windowEl && window) {
            windowEl.classList.add('minimized');
            window.minimized = true;
            this.triggerAction('minimize_window', { windowId });
        }
        this.updateTaskbar();
    }

    restoreWindow(windowId) {
        const windowEl = document.getElementById(`window-${windowId}`);
        const window = this.state.windows.find(w => w.id === windowId);
        if (windowEl && window) {
            windowEl.classList.remove('minimized');
            window.minimized = false;
            this.focusWindow(windowId);
        }
        this.updateTaskbar();
    }

    toggleMaximize(windowId) {
        const windowEl = document.getElementById(`window-${windowId}`);
        const window = this.state.windows.find(w => w.id === windowId);
        if (windowEl && window) {
            if (window.maximized) {
                windowEl.classList.remove('maximized');
                window.maximized = false;
            } else {
                windowEl.classList.add('maximized');
                window.maximized = true;
            }
            this.triggerAction('maximize_window', { windowId, maximized: window.maximized });
        }
    }

    closeWindow(windowId) {
        const windowEl = document.getElementById(`window-${windowId}`);
        if (windowEl) {
            windowEl.remove();
        }
        this.state.windows = this.state.windows.filter(w => w.id !== windowId);
        this.triggerAction('close_window', { windowId });
        this.updateTaskbar();
    }

    // Draggable windows
    makeDraggable(windowEl, window) {
        const titlebar = windowEl.querySelector('.window-titlebar');
        let isDragging = false;
        let startX, startY, initialX, initialY;

        titlebar.addEventListener('mousedown', (e) => {
            if (e.target.closest('.window-controls')) return;
            if (window.maximized) return;

            isDragging = true;
            startX = e.clientX;
            startY = e.clientY;
            initialX = window.x;
            initialY = window.y;

            document.addEventListener('mousemove', onMouseMove);
            document.addEventListener('mouseup', onMouseUp);
        });

        const onMouseMove = (e) => {
            if (!isDragging) return;
            const dx = e.clientX - startX;
            const dy = e.clientY - startY;
            window.x = initialX + dx;
            window.y = Math.max(0, initialY + dy);
            windowEl.style.left = window.x + 'px';
            windowEl.style.top = window.y + 'px';
        };

        const onMouseUp = () => {
            isDragging = false;
            document.removeEventListener('mousemove', onMouseMove);
            document.removeEventListener('mouseup', onMouseUp);
        };
    }

    // Taskbar
    updateTaskbar() {
        const container = document.querySelector('.taskbar-center');
        if (!container) return;

        // Keep pinned apps, update window indicators
        const taskbarApps = container.querySelectorAll('.taskbar-btn');
        taskbarApps.forEach(btn => {
            const appId = btn.dataset.appId;
            const hasWindow = this.state.windows.some(w => w.appId === appId && !w.minimized);
            const isMinimized = this.state.windows.some(w => w.appId === appId && w.minimized);

            btn.classList.toggle('active', hasWindow || isMinimized);
        });
    }

    handleTaskbarClick(appId) {
        const windows = this.state.windows.filter(w => w.appId === appId);

        if (windows.length === 0) {
            // No windows, open app
            this.openApp(appId);
        } else if (windows.length === 1) {
            const window = windows[0];
            if (window.minimized) {
                this.restoreWindow(window.id);
            } else if (this.state.focusedWindowId === window.id) {
                this.minimizeWindow(window.id);
            } else {
                this.focusWindow(window.id);
            }
        } else {
            // Multiple windows - focus the first non-minimized or restore first minimized
            const nonMinimized = windows.find(w => !w.minimized);
            if (nonMinimized) {
                this.focusWindow(nonMinimized.id);
            } else {
                this.restoreWindow(windows[0].id);
            }
        }

        this.triggerAction('click', { target: `.taskbar-btn[data-app-id="${appId}"]`, app: appId });
    }

    // Clock
    updateClock() {
        const clockEl = document.querySelector('.system-clock');
        if (!clockEl) return;

        const now = new Date();
        const time = now.toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit', hour12: true });
        const date = now.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });

        clockEl.innerHTML = `<div class="time">${time}</div><div class="date">${date}</div>`;
    }

    // Calculator
    calcInput(windowId, input) {
        const window = this.state.windows.find(w => w.id === windowId);
        if (!window) return;

        const resultEl = document.getElementById(`calc-result-${windowId}`);
        const exprEl = document.getElementById(`calc-expr-${windowId}`);

        let value = window.data.calcValue || '0';

        if (input === 'C') {
            window.data.calcValue = '0';
            window.data.calcExpression = '';
            window.data.calcPrevValue = null;
            window.data.calcOperator = null;
        } else if (input === 'CE') {
            window.data.calcValue = '0';
        } else if (input === 'DEL') {
            window.data.calcValue = value.length > 1 ? value.slice(0, -1) : '0';
        } else if (['+', '-', '*', '/'].includes(input)) {
            window.data.calcPrevValue = parseFloat(value);
            window.data.calcOperator = input;
            window.data.calcExpression = value + ' ' + input;
            window.data.calcValue = '0';
        } else if (input === '=') {
            if (window.data.calcPrevValue !== null && window.data.calcOperator) {
                const result = this.calculate(
                    window.data.calcPrevValue,
                    parseFloat(value),
                    window.data.calcOperator
                );
                window.data.calcExpression = window.data.calcExpression + ' ' + value + ' =';
                window.data.calcValue = String(result);
                window.data.calcPrevValue = null;
                window.data.calcOperator = null;
            }
        } else if (input === '+/-') {
            window.data.calcValue = String(-parseFloat(value));
        } else if (input === '.') {
            if (!value.includes('.')) {
                window.data.calcValue = value + '.';
            }
        } else {
            // Number
            if (value === '0') {
                window.data.calcValue = input;
            } else {
                window.data.calcValue = value + input;
            }
        }

        resultEl.textContent = window.data.calcValue;
        exprEl.textContent = window.data.calcExpression;

        this.triggerAction('click', { target: `.calc-btn`, input, windowId });
    }

    calculate(a, b, op) {
        switch (op) {
            case '+': return a + b;
            case '-': return a - b;
            case '*': return a * b;
            case '/': return b !== 0 ? a / b : 'Error';
            default: return b;
        }
    }

    // Command Prompt
    executeCommand(windowId) {
        const inputEl = document.getElementById(`cmd-input-${windowId}`);
        const cmdEl = document.getElementById(`cmd-${windowId}`);
        const outputEl = cmdEl.querySelector('.cmd-output');
        const command = inputEl.value.trim();

        if (!command) return;

        let output = '';
        const cmd = command.toLowerCase();

        if (cmd === 'help') {
            output = `Available commands:
  dir    - List directory contents
  cd     - Change directory
  cls    - Clear screen
  echo   - Display message
  date   - Show current date
  time   - Show current time
  help   - Show this help`;
        } else if (cmd === 'cls') {
            outputEl.innerHTML = '';
            inputEl.value = '';
            return;
        } else if (cmd === 'dir') {
            output = ` Volume in drive C has no label.
 Directory of C:\\Users\\Learner

01/01/2024  09:00 AM    <DIR>          .
01/01/2024  09:00 AM    <DIR>          ..
01/01/2024  09:00 AM    <DIR>          Desktop
01/01/2024  09:00 AM    <DIR>          Documents
01/01/2024  09:00 AM    <DIR>          Downloads
               0 File(s)              0 bytes
               5 Dir(s)  100,000,000 bytes free`;
        } else if (cmd === 'date') {
            output = `The current date is: ${new Date().toLocaleDateString()}`;
        } else if (cmd === 'time') {
            output = `The current time is: ${new Date().toLocaleTimeString()}`;
        } else if (cmd.startsWith('echo ')) {
            output = command.substring(5);
        } else if (cmd.startsWith('cd ')) {
            output = `Changed directory to ${command.substring(3)}`;
        } else {
            output = `'${command}' is not recognized as an internal or external command.`;
        }

        outputEl.innerHTML += `C:\\Users\\Learner>${command}\n${output}\n\n`;
        inputEl.value = '';
        cmdEl.scrollTop = cmdEl.scrollHeight;

        this.triggerAction('type', { target: `#cmd-input-${windowId}`, command });
    }

    // File Explorer
    navigateTo(windowId, location) {
        const mainEl = document.getElementById(`fe-main-${windowId}`);
        const addressEl = document.getElementById(`fe-address-${windowId}`);

        let content = '';
        let path = '';

        switch (location) {
            case 'this-pc':
                path = 'This PC';
                content = `
                    <div class="file-item" ondblclick="virtualDesktop.openFolder(${windowId}, 'C:')">
                        <div class="icon"><i class="fas fa-hdd"></i></div>
                        <div class="name">Local Disk (C:)</div>
                    </div>
                `;
                break;
            case 'desktop':
                path = 'C:\\Users\\Learner\\Desktop';
                content = '<div style="padding: 20px; color: var(--win11-text-secondary);">Desktop is empty</div>';
                break;
            case 'documents':
                path = 'C:\\Users\\Learner\\Documents';
                content = '<div style="padding: 20px; color: var(--win11-text-secondary);">Documents folder is empty</div>';
                break;
            case 'downloads':
                path = 'C:\\Users\\Learner\\Downloads';
                content = '<div style="padding: 20px; color: var(--win11-text-secondary);">Downloads folder is empty</div>';
                break;
            case 'pictures':
                path = 'C:\\Users\\Learner\\Pictures';
                content = '<div style="padding: 20px; color: var(--win11-text-secondary);">Pictures folder is empty</div>';
                break;
        }

        mainEl.innerHTML = content;
        addressEl.innerHTML = `<i class="fas fa-folder" style="margin-right: 8px;"></i><span>${path}</span>`;

        this.triggerAction('navigate', { windowId, location, path });
    }

    openFolder(windowId, folder) {
        this.navigateTo(windowId, folder);
        this.triggerAction('double_click', { target: `folder-${folder}`, windowId });
    }

    fileExplorerBack(windowId) {
        this.navigateTo(windowId, 'this-pc');
    }

    fileExplorerForward(windowId) {
        // Not implemented
    }

    // Action Trigger
    triggerAction(actionType, data = {}) {
        if (this.config.onAction) {
            this.config.onAction(actionType, data);
        }

        // Dispatch custom event for guided mode
        const event = new CustomEvent('virtualDesktopAction', {
            detail: { actionType, data }
        });
        document.dispatchEvent(event);
    }
}

// Global instance
let virtualDesktop = null;

function initVirtualDesktop(config) {
    virtualDesktop = new VirtualDesktop(config);
    return virtualDesktop;
}

// Auto-initialize when config is available
document.addEventListener('DOMContentLoaded', function() {
    if (window.virtualDesktopConfig) {
        virtualDesktop = new VirtualDesktop(window.virtualDesktopConfig);
        console.log('VirtualDesktop initialized');
    }
});
