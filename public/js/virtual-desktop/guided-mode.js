/**
 * GuidedMode - Interactive lesson guidance system for Windows 11 Virtual Desktop
 * Handles step-by-step instruction, action validation, and progress tracking
 */
class GuidedMode {
    constructor() {
        this.steps = [];
        this.currentStepIndex = 0;
        this.lessonId = null;
        this.startTime = null;
        this.isActive = false;
        this.completedSteps = [];
        this.totalPoints = 0;
        this.hintShown = false;
        this.actionListeners = [];
        this.highlightTimeout = null;

        // DOM Elements
        this.elements = {
            overlay: null,
            stepIndicator: null,
            instructionPanel: null,
            instructionTitle: null,
            instructionText: null,
            hintButton: null,
            hintContent: null,
            hintText: null,
            skipButton: null,
            highlightElement: null,
            arrowPointer: null,
            successAnimation: null,
            lessonCompleteModal: null,
            currentStepNumber: null,
            totalSteps: null,
            stepProgressFill: null
        };
    }

    /**
     * Initialize guided mode with configuration
     */
    init(config) {
        if (!config || !config.steps || config.steps.length === 0) {
            console.warn('GuidedMode: No steps provided');
            return;
        }

        this.steps = config.steps;
        this.lessonId = config.lessonId;
        this.startTime = Date.now();
        this.isActive = true;

        // Cache DOM elements
        this.cacheElements();

        // Set total steps
        if (this.elements.totalSteps) {
            this.elements.totalSteps.textContent = this.steps.length;
        }

        // Start first step
        this.loadStep(0);

        // Setup global action listeners
        this.setupActionListeners();

        console.log('GuidedMode initialized with', this.steps.length, 'steps');
    }

    /**
     * Cache DOM elements for performance
     */
    cacheElements() {
        this.elements = {
            overlay: document.getElementById('guidedOverlay'),
            stepIndicator: document.getElementById('stepIndicator'),
            instructionPanel: document.getElementById('instructionPanel'),
            instructionTitle: document.getElementById('instructionTitle'),
            instructionText: document.getElementById('instructionText'),
            hintButton: document.getElementById('hintButton'),
            hintContent: document.getElementById('hintContent'),
            hintText: document.getElementById('hintText'),
            skipButton: document.getElementById('skipButton'),
            highlightElement: document.getElementById('highlightElement'),
            arrowPointer: document.getElementById('arrowPointer'),
            successAnimation: document.getElementById('successAnimation'),
            lessonCompleteModal: document.getElementById('lessonCompleteModal'),
            currentStepNumber: document.getElementById('currentStepNumber'),
            totalSteps: document.getElementById('totalSteps'),
            stepProgressFill: document.getElementById('stepProgressFill'),
            completedStepsCount: document.getElementById('completedStepsCount'),
            earnedPoints: document.getElementById('earnedPoints'),
            timeSpent: document.getElementById('timeSpent')
        };
    }

    /**
     * Load a specific step
     */
    loadStep(index) {
        if (index < 0 || index >= this.steps.length) {
            this.completeLesson();
            return;
        }

        this.currentStepIndex = index;
        const step = this.steps[index];
        this.hintShown = false;

        // Update UI
        this.updateStepUI(step);

        // Highlight target element
        if (step.target_element) {
            this.highlightTarget(step.target_element);
        } else {
            this.clearHighlight();
        }

        // Setup action listener for this step
        this.setupStepListener(step);

        // Hide hint
        if (this.elements.hintContent) {
            this.elements.hintContent.style.display = 'none';
        }

        console.log(`Loaded step ${index + 1}:`, step.title);
    }

    /**
     * Update UI for current step
     */
    updateStepUI(step) {
        if (this.elements.currentStepNumber) {
            this.elements.currentStepNumber.textContent = this.currentStepIndex + 1;
        }

        if (this.elements.instructionTitle) {
            this.elements.instructionTitle.textContent = step.title || `Step ${this.currentStepIndex + 1}`;
        }

        if (this.elements.instructionText) {
            this.elements.instructionText.textContent = step.instruction || 'Complete this step to continue.';
        }

        // Update progress bar
        const progress = ((this.currentStepIndex) / this.steps.length) * 100;
        if (this.elements.stepProgressFill) {
            this.elements.stepProgressFill.style.width = `${progress}%`;
        }

        // Show/hide hint button based on availability
        if (this.elements.hintButton) {
            this.elements.hintButton.style.display = step.hint ? 'flex' : 'none';
        }

        // Show/hide skip button for non-required steps
        if (this.elements.skipButton) {
            this.elements.skipButton.style.display = step.is_required ? 'none' : 'flex';
        }
    }

    /**
     * Highlight target element
     */
    highlightTarget(targetSelector) {
        this.clearHighlight();

        // Find target element
        const target = this.findTargetElement(targetSelector);
        if (!target) {
            console.warn('Target element not found:', targetSelector);
            return;
        }

        const rect = target.getBoundingClientRect();
        const wrapper = document.getElementById('win11DesktopWrapper');
        const wrapperRect = wrapper ? wrapper.getBoundingClientRect() : { left: 0, top: 0 };

        // Position highlight
        if (this.elements.highlightElement) {
            const highlight = this.elements.highlightElement;
            highlight.style.display = 'block';
            highlight.style.left = `${rect.left - wrapperRect.left - 5}px`;
            highlight.style.top = `${rect.top - wrapperRect.top - 5}px`;
            highlight.style.width = `${rect.width + 10}px`;
            highlight.style.height = `${rect.height + 10}px`;
        }

        // Position arrow pointer
        if (this.elements.arrowPointer) {
            const arrow = this.elements.arrowPointer;
            arrow.style.display = 'block';
            arrow.style.left = `${rect.left - wrapperRect.left + rect.width / 2}px`;
            arrow.style.top = `${rect.top - wrapperRect.top - 40}px`;
        }

        // Add pulse animation to target
        target.classList.add('win11-target-pulse');
    }

    /**
     * Find target element by selector
     */
    findTargetElement(selector) {
        // Try direct selector first
        let element = document.querySelector(selector);
        if (element) return element;

        // Try by ID
        element = document.getElementById(selector);
        if (element) return element;

        // Try data-element-id
        element = document.querySelector(`[data-element-id="${selector}"]`);
        if (element) return element;

        // Try data-app for taskbar/desktop icons
        element = document.querySelector(`[data-app="${selector}"]`);
        if (element) return element;

        // Try by window title
        element = document.querySelector(`.win11-window[data-app="${selector}"]`);
        if (element) return element;

        // Try common class variations
        element = document.querySelector(`.${selector}`);
        if (element) return element;

        // Try win11-desktop for desktop clicks
        if (selector === 'win11Desktop') {
            element = document.querySelector('.win11-desktop');
            if (element) return element;
        }

        return null;
    }

    /**
     * Clear highlight
     */
    clearHighlight() {
        if (this.elements.highlightElement) {
            this.elements.highlightElement.style.display = 'none';
        }
        if (this.elements.arrowPointer) {
            this.elements.arrowPointer.style.display = 'none';
        }

        // Remove pulse from all elements
        document.querySelectorAll('.win11-target-pulse').forEach(el => {
            el.classList.remove('win11-target-pulse');
        });
    }

    /**
     * Setup action listeners for step validation
     */
    setupActionListeners() {
        // Listen for virtual desktop events
        document.addEventListener('virtualdesktop:action', (e) => {
            this.handleAction(e.detail);
        });

        // Click listener
        document.addEventListener('click', (e) => {
            if (!this.isActive) return;
            this.handleClick(e);
        }, true);

        // Double-click listener
        document.addEventListener('dblclick', (e) => {
            if (!this.isActive) return;
            this.handleDoubleClick(e);
        }, true);

        // Right-click listener
        document.addEventListener('contextmenu', (e) => {
            if (!this.isActive) return;
            this.handleRightClick(e);
        }, true);

        // Keyboard listener for type actions
        document.addEventListener('keydown', (e) => {
            if (!this.isActive) return;
            this.handleKeyDown(e);
        });
    }

    /**
     * Setup listener for current step
     */
    setupStepListener(step) {
        // Clear previous timeout
        if (this.highlightTimeout) {
            clearTimeout(this.highlightTimeout);
        }

        // Set timeout if specified
        if (step.timeout_seconds && step.timeout_seconds > 0) {
            this.highlightTimeout = setTimeout(() => {
                this.showHint();
            }, step.timeout_seconds * 1000);
        }
    }

    /**
     * Handle click actions
     */
    handleClick(e) {
        const step = this.steps[this.currentStepIndex];
        if (!step || step.action_type !== 'click') return;

        const target = this.findTargetElement(step.target_element);
        if (target && (target === e.target || target.contains(e.target))) {
            this.validateAndCompleteStep(step, { type: 'click', target: e.target });
        }
    }

    /**
     * Handle double-click actions
     */
    handleDoubleClick(e) {
        const step = this.steps[this.currentStepIndex];
        if (!step || step.action_type !== 'double_click') return;

        const target = this.findTargetElement(step.target_element);
        if (target && (target === e.target || target.contains(e.target))) {
            this.validateAndCompleteStep(step, { type: 'double_click', target: e.target });
        }
    }

    /**
     * Handle right-click actions
     */
    handleRightClick(e) {
        const step = this.steps[this.currentStepIndex];
        if (!step || step.action_type !== 'right_click') return;

        const target = this.findTargetElement(step.target_element);
        if (target && (target === e.target || target.contains(e.target))) {
            this.validateAndCompleteStep(step, { type: 'right_click', target: e.target });
        }
    }

    /**
     * Handle keyboard input
     */
    handleKeyDown(e) {
        const step = this.steps[this.currentStepIndex];
        if (!step || step.action_type !== 'type') return;

        // Store typed characters if needed
        // Validation will be done when user completes typing
    }

    /**
     * Handle virtual desktop action events
     */
    handleAction(detail) {
        const step = this.steps[this.currentStepIndex];
        if (!step) return;

        // Match action type
        if (detail.action === step.action_type) {
            // Validate action data if specified
            if (this.validateActionData(step, detail)) {
                this.validateAndCompleteStep(step, detail);
            }
        }
    }

    /**
     * Validate action data against step requirements
     */
    validateActionData(step, actionDetail) {
        if (!step.action_data) return true;

        const data = step.action_data;

        // Check app name for open_app action
        if (step.action_type === 'open_app' && data.app) {
            return actionDetail.app === data.app;
        }

        // Check window action for close/minimize/maximize
        if (['close_window', 'minimize_window', 'maximize_window'].includes(step.action_type)) {
            if (data.windowId && actionDetail.windowId !== data.windowId) {
                return false;
            }
        }

        // Check text input for type action
        if (step.action_type === 'type' && data.text) {
            return actionDetail.text && actionDetail.text.toLowerCase().includes(data.text.toLowerCase());
        }

        return true;
    }

    /**
     * Validate and complete current step
     */
    validateAndCompleteStep(step, actionDetail) {
        // Run validation rules if any
        if (step.validation_rules && !this.runValidationRules(step.validation_rules, actionDetail)) {
            this.showValidationError(step);
            return;
        }

        // Mark step as completed
        this.completedSteps.push({
            stepId: step.id,
            stepNumber: step.step_number,
            completedAt: Date.now(),
            hintUsed: this.hintShown,
            points: this.hintShown ? Math.floor(step.points / 2) : step.points
        });

        this.totalPoints += this.completedSteps[this.completedSteps.length - 1].points;

        // Show success animation
        this.showSuccessAnimation();

        // Save progress to server
        this.saveProgress(step);

        // Move to next step after animation
        setTimeout(() => {
            this.loadStep(this.currentStepIndex + 1);
        }, 1500);
    }

    /**
     * Run validation rules
     */
    runValidationRules(rules, actionDetail) {
        if (!rules || !Array.isArray(rules)) return true;

        for (const rule of rules) {
            switch (rule.type) {
                case 'exact_match':
                    if (actionDetail.text !== rule.value) return false;
                    break;
                case 'contains':
                    if (!actionDetail.text || !actionDetail.text.includes(rule.value)) return false;
                    break;
                case 'app_opened':
                    if (actionDetail.app !== rule.value) return false;
                    break;
                case 'element_clicked':
                    if (!actionDetail.target || !actionDetail.target.matches(rule.value)) return false;
                    break;
            }
        }

        return true;
    }

    /**
     * Show validation error
     */
    showValidationError(step) {
        // Shake the instruction panel
        if (this.elements.instructionPanel) {
            this.elements.instructionPanel.classList.add('shake');
            setTimeout(() => {
                this.elements.instructionPanel.classList.remove('shake');
            }, 500);
        }

        // Show hint automatically on error
        if (!this.hintShown && step.hint) {
            this.showHint();
        }
    }

    /**
     * Show hint for current step
     */
    showHint() {
        const step = this.steps[this.currentStepIndex];
        if (!step || !step.hint) return;

        this.hintShown = true;

        if (this.elements.hintContent && this.elements.hintText) {
            this.elements.hintText.textContent = step.hint;
            this.elements.hintContent.style.display = 'flex';
        }

        // Animate hint appearance
        if (this.elements.hintContent) {
            this.elements.hintContent.classList.add('hint-appear');
            setTimeout(() => {
                this.elements.hintContent.classList.remove('hint-appear');
            }, 300);
        }
    }

    /**
     * Skip current step (only for non-required steps)
     */
    skipStep() {
        const step = this.steps[this.currentStepIndex];
        if (!step || step.is_required) return;

        // Record skipped step
        this.completedSteps.push({
            stepId: step.id,
            stepNumber: step.step_number,
            completedAt: Date.now(),
            skipped: true,
            points: 0
        });

        // Move to next step
        this.loadStep(this.currentStepIndex + 1);
    }

    /**
     * Show success animation
     */
    showSuccessAnimation() {
        if (!this.elements.successAnimation) return;

        this.elements.successAnimation.style.display = 'flex';
        this.elements.successAnimation.classList.add('success-appear');

        setTimeout(() => {
            this.elements.successAnimation.style.display = 'none';
            this.elements.successAnimation.classList.remove('success-appear');
        }, 1200);
    }

    /**
     * Complete the lesson
     */
    completeLesson() {
        this.isActive = false;
        this.clearHighlight();

        const timeSpent = Math.floor((Date.now() - this.startTime) / 1000);

        // Update completion modal stats
        if (this.elements.completedStepsCount) {
            this.elements.completedStepsCount.textContent = this.completedSteps.filter(s => !s.skipped).length;
        }
        if (this.elements.earnedPoints) {
            this.elements.earnedPoints.textContent = this.totalPoints;
        }
        if (this.elements.timeSpent) {
            const minutes = Math.floor(timeSpent / 60);
            const seconds = timeSpent % 60;
            this.elements.timeSpent.textContent = `${minutes}:${seconds.toString().padStart(2, '0')}`;
        }

        // Show completion modal
        if (this.elements.lessonCompleteModal) {
            this.elements.lessonCompleteModal.style.display = 'flex';
        }

        // Hide guided overlay elements
        if (this.elements.instructionPanel) {
            this.elements.instructionPanel.style.display = 'none';
        }
        if (this.elements.stepIndicator) {
            this.elements.stepIndicator.style.display = 'none';
        }

        // Save final progress
        this.saveFinalProgress(timeSpent);

        console.log('Lesson completed! Total points:', this.totalPoints);
    }

    /**
     * Restart the lesson
     */
    restartLesson() {
        // Reset state
        this.currentStepIndex = 0;
        this.completedSteps = [];
        this.totalPoints = 0;
        this.startTime = Date.now();
        this.isActive = true;

        // Hide completion modal
        if (this.elements.lessonCompleteModal) {
            this.elements.lessonCompleteModal.style.display = 'none';
        }

        // Show guided elements
        if (this.elements.instructionPanel) {
            this.elements.instructionPanel.style.display = 'flex';
        }
        if (this.elements.stepIndicator) {
            this.elements.stepIndicator.style.display = 'flex';
        }

        // Reset virtual desktop
        if (window.virtualDesktop) {
            virtualDesktop.resetDesktop();
        }

        // Load first step
        this.loadStep(0);
    }

    /**
     * Toggle instruction panel visibility
     */
    toggleInstructionPanel() {
        if (!this.elements.instructionPanel) return;

        this.elements.instructionPanel.classList.toggle('minimized');
    }

    /**
     * Save progress to server
     */
    saveProgress(step) {
        if (!this.lessonId) return;

        const progressData = {
            lesson_id: this.lessonId,
            step_id: step.id,
            completed: true,
            attempts: 1,
            hint_used: this.hintShown,
            points_earned: this.completedSteps[this.completedSteps.length - 1].points,
            time_spent: Math.floor((Date.now() - this.startTime) / 1000)
        };

        fetch('/api/interactive-progress/step', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': window.virtualDesktopConfig?.csrfToken || ''
            },
            body: JSON.stringify(progressData)
        }).catch(err => console.warn('Failed to save progress:', err));
    }

    /**
     * Save final lesson progress
     */
    saveFinalProgress(timeSpent) {
        if (!this.lessonId) return;

        const finalData = {
            lesson_id: this.lessonId,
            completed: true,
            total_points: this.totalPoints,
            time_spent: timeSpent,
            completed_steps: this.completedSteps.length,
            total_steps: this.steps.length
        };

        fetch('/api/interactive-progress/complete', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': window.virtualDesktopConfig?.csrfToken || ''
            },
            body: JSON.stringify(finalData)
        }).catch(err => console.warn('Failed to save final progress:', err));
    }
}

// Create global instance
const guidedMode = new GuidedMode();

// Auto-initialize when config is available
document.addEventListener('DOMContentLoaded', function() {
    if (window.virtualDesktopConfig && window.virtualDesktopConfig.mode === 'guided') {
        // Wait for virtual desktop to initialize first
        setTimeout(() => {
            guidedMode.init(window.virtualDesktopConfig);
        }, 500);
    }
});

// Export for external use
if (typeof module !== 'undefined' && module.exports) {
    module.exports = GuidedMode;
}
