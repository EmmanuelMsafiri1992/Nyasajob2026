/**
 * Unsaved Form Guard
 *
 * Prevents users from accidentally navigating away from forms with unsaved changes.
 * Shows a confirmation dialog when trying to leave a page with modified form data.
 *
 * Usage:
 * 1. Add the class "form-guard" to any form you want to protect
 * 2. Add data-form-guard-message="Your custom message" for custom warning message
 *
 * Example:
 * <form class="form-guard" data-form-guard-message="You have unsaved changes!">
 *   ...
 * </form>
 */

(function() {
    'use strict';

    // Configuration
    const CONFIG = {
        formSelector: '.form-guard',
        defaultMessage: 'You have unsaved changes. Are you sure you want to leave this page?',
        ignoreFields: ['input[type="hidden"]', 'input[type="submit"]', 'button'],
        storageKey: 'formGuardState'
    };

    // Track form states
    const formStates = new Map();

    /**
     * Initialize form guard on a form element
     * @param {HTMLFormElement} form
     */
    function initForm(form) {
        if (formStates.has(form)) return;

        const initialState = getFormState(form);
        formStates.set(form, {
            initial: initialState,
            isDirty: false
        });

        // Listen for input changes
        form.addEventListener('input', function(e) {
            checkFormDirty(form);
        });

        form.addEventListener('change', function(e) {
            checkFormDirty(form);
        });

        // Mark form as clean when submitted
        form.addEventListener('submit', function(e) {
            const state = formStates.get(form);
            if (state) {
                state.isDirty = false;
            }
        });

        // Mark form as clean when reset
        form.addEventListener('reset', function(e) {
            setTimeout(function() {
                const state = formStates.get(form);
                if (state) {
                    state.isDirty = false;
                    state.initial = getFormState(form);
                }
            }, 0);
        });
    }

    /**
     * Get the current state of a form (values of all fields)
     * @param {HTMLFormElement} form
     * @returns {Object}
     */
    function getFormState(form) {
        const state = {};
        const formData = new FormData(form);

        formData.forEach((value, key) => {
            if (!shouldIgnoreField(form.querySelector(`[name="${key}"]`))) {
                if (state[key]) {
                    // Handle multiple values (checkboxes, multi-selects)
                    if (!Array.isArray(state[key])) {
                        state[key] = [state[key]];
                    }
                    state[key].push(value);
                } else {
                    state[key] = value;
                }
            }
        });

        return state;
    }

    /**
     * Check if a field should be ignored
     * @param {HTMLElement} field
     * @returns {boolean}
     */
    function shouldIgnoreField(field) {
        if (!field) return true;

        return CONFIG.ignoreFields.some(selector => {
            return field.matches && field.matches(selector);
        });
    }

    /**
     * Check if form has unsaved changes
     * @param {HTMLFormElement} form
     */
    function checkFormDirty(form) {
        const state = formStates.get(form);
        if (!state) return;

        const currentState = getFormState(form);
        state.isDirty = !isEqual(state.initial, currentState);

        // Trigger custom event
        form.dispatchEvent(new CustomEvent('formguard:change', {
            detail: { isDirty: state.isDirty }
        }));
    }

    /**
     * Deep equality check for objects
     * @param {Object} obj1
     * @param {Object} obj2
     * @returns {boolean}
     */
    function isEqual(obj1, obj2) {
        const keys1 = Object.keys(obj1);
        const keys2 = Object.keys(obj2);

        if (keys1.length !== keys2.length) return false;

        return keys1.every(key => {
            const val1 = obj1[key];
            const val2 = obj2[key];

            if (Array.isArray(val1) && Array.isArray(val2)) {
                return val1.length === val2.length &&
                    val1.every((v, i) => v === val2[i]);
            }

            return val1 === val2;
        });
    }

    /**
     * Check if any form has unsaved changes
     * @returns {boolean}
     */
    function hasUnsavedChanges() {
        for (const [form, state] of formStates) {
            if (state.isDirty && document.body.contains(form)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Get the warning message for a form
     * @param {HTMLFormElement} form
     * @returns {string}
     */
    function getWarningMessage(form) {
        return form.dataset.formGuardMessage || CONFIG.defaultMessage;
    }

    /**
     * Handle beforeunload event
     * @param {BeforeUnloadEvent} e
     */
    function handleBeforeUnload(e) {
        if (hasUnsavedChanges()) {
            e.preventDefault();
            e.returnValue = CONFIG.defaultMessage;
            return CONFIG.defaultMessage;
        }
    }

    /**
     * Initialize all forms with form-guard class
     */
    function init() {
        // Initialize existing forms
        document.querySelectorAll(CONFIG.formSelector).forEach(initForm);

        // Watch for dynamically added forms
        const observer = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                mutation.addedNodes.forEach(function(node) {
                    if (node.nodeType === 1) {
                        if (node.matches && node.matches(CONFIG.formSelector)) {
                            initForm(node);
                        }
                        node.querySelectorAll && node.querySelectorAll(CONFIG.formSelector).forEach(initForm);
                    }
                });
            });
        });

        observer.observe(document.body, {
            childList: true,
            subtree: true
        });

        // Add beforeunload handler
        window.addEventListener('beforeunload', handleBeforeUnload);
    }

    // Public API
    window.FormGuard = {
        init: init,
        initForm: initForm,
        hasUnsavedChanges: hasUnsavedChanges,
        markClean: function(form) {
            const state = formStates.get(form);
            if (state) {
                state.isDirty = false;
                state.initial = getFormState(form);
            }
        },
        markDirty: function(form) {
            const state = formStates.get(form);
            if (state) {
                state.isDirty = true;
            }
        },
        isDirty: function(form) {
            const state = formStates.get(form);
            return state ? state.isDirty : false;
        }
    };

    // Auto-initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
