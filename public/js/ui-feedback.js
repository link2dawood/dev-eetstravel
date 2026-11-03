(function ($, window) {
    'use strict';

    if (!$ || $.tmsLoading) {
        return;
    }

    var activeButtons = new Set();
    var activeForms = new Set();

    function getState(button) {
        return $(button).data('tmsLoadingState');
    }

    function stop(button) {
        var $button = $(button);
        var state = getState(button);

        if (!state) {
            return;
        }

        if (state.submitterValue) {
            state.submitterValue.remove();
        }

        if (button.tagName === 'INPUT') {
            button.value = state.value;
        } else {
            button.innerHTML = state.html;
        }

        button.style.minWidth = state.minWidth;
        if (button.tagName === 'A') {
            button.style.pointerEvents = state.pointerEvents;
            if (state.tabIndex === null) {
                button.removeAttribute('tabindex');
            } else {
                button.setAttribute('tabindex', state.tabIndex);
            }
            button.classList.remove('tms-loading');
        } else {
            $button.prop('disabled', state.disabled);
        }

        if (state.ariaDisabled === null) {
            $button.removeAttr('aria-disabled');
        } else {
            $button.attr('aria-disabled', state.ariaDisabled);
        }

        if (state.ariaBusy === null) {
            $button.removeAttr('aria-busy');
        } else {
            $button.attr('aria-busy', state.ariaBusy);
        }

        $button.removeData('tmsLoadingState');
        activeButtons.delete(button);
    }

    $.tmsLoading = {
        start: function (button, label) {
            button = $(button).get(0);

            if (!button || getState(button) || button.disabled || button.getAttribute('aria-disabled') === 'true') {
                return false;
            }

            var $button = $(button);
            var state = {
                html: button.innerHTML,
                value: button.value,
                minWidth: button.style.minWidth,
                disabled: button.disabled,
                ariaBusy: button.getAttribute('aria-busy'),
                ariaDisabled: button.getAttribute('aria-disabled'),
                pointerEvents: button.style.pointerEvents,
                tabIndex: button.getAttribute('tabindex'),
                submitterValue: null
            };
            var text = label || $button.attr('data-loading-text') || 'Submitting...';
            var width = Math.ceil(button.getBoundingClientRect().width);

            if (button.form && button.name) {
                state.submitterValue = document.createElement('input');
                state.submitterValue.type = 'hidden';
                state.submitterValue.name = button.name;
                state.submitterValue.value = button.value || '';
                button.form.appendChild(state.submitterValue);
            }

            $button.data('tmsLoadingState', state);
            if (width > 0) {
                button.style.minWidth = Math.max(width, button.offsetWidth) + 'px';
            }

            if (button.tagName === 'INPUT') {
                button.value = text;
            } else {
                button.textContent = '';
                var spinner = document.createElement('span');
                spinner.className = 'tms-spinner';
                spinner.setAttribute('aria-hidden', 'true');
                button.appendChild(spinner);

                var loadingText = document.createElement('span');
                loadingText.textContent = text;
                button.appendChild(loadingText);
            }

            if (button.tagName === 'A') {
                button.style.pointerEvents = 'none';
                button.setAttribute('tabindex', '-1');
                button.classList.add('tms-loading');
            } else {
                $button.prop('disabled', true);
            }
            $button.attr('aria-busy', 'true').attr('aria-disabled', 'true');
            activeButtons.add(button);
            return true;
        },

        stop: stop,

        ajax: function (button, options, label) {
            if (button && !this.start(button, label)) {
                return null;
            }

            var settings = $.extend({}, options);
            var complete = settings.complete;
            settings.complete = function (xhr, status) {
                try {
                    if (typeof complete === 'function') {
                        complete.call(this, xhr, status);
                    }
                } finally {
                    if (button) {
                        stop($(button).get(0));
                    }
                }
            };

            try {
                return $.ajax(settings);
            } catch (error) {
                if (button) {
                    stop($(button).get(0));
                }
                throw error;
            }
        },

        debounce: function (callback, delay) {
            var timer;
            return function () {
                var context = this;
                var args = arguments;
                window.clearTimeout(timer);
                timer = window.setTimeout(function () {
                    callback.apply(context, args);
                }, delay);
            };
        }
    };

    window.addEventListener('submit', function (event) {
        var form = event.target;
        if (event.defaultPrevented || !form.hasAttribute('data-loading-form') || form.hasAttribute('data-async-form') || form.hasAttribute('data-no-loading')) {
            return;
        }

        if (activeForms.has(form)) {
            event.preventDefault();
            event.stopImmediatePropagation();
            return;
        }

        var submitter = event.submitter;
        if (!submitter || submitter.form !== form) {
            submitter = form.querySelector('button[type="submit"]:not(:disabled), button:not([type]):not(:disabled), input[type="submit"]:not(:disabled)');
        }
        if (submitter && $.tmsLoading.start(submitter)) {
            activeForms.add(form);
        }
    });

    document.addEventListener('click', function (event) {
        var target = event.target.closest('.tms-loading[aria-disabled="true"]');
        if (target) {
            event.preventDefault();
            event.stopImmediatePropagation();
        }
    }, true);

    function restoreButtons() {
        Array.from(activeButtons).forEach(stop);
        activeForms.clear();
    }

    window.addEventListener('pageshow', restoreButtons);
    window.addEventListener('pagehide', restoreButtons);
})(window.jQuery, window);
