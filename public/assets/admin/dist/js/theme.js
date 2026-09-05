/* ==========================================================================
   Theme switcher — vanilla JS, no dependencies.
   - Reads/writes localStorage under STORAGE_KEY
   - Applies <html data-theme="..."> which themes.css uses to restyle
   - Wires up any .theme-pick element (works for any UI: dropdown, buttons, etc.)

   Usage:
     <a href="#" class="theme-pick" data-theme="dark">Dark</a>
   ========================================================================== */
(function () {
    'use strict';

    var STORAGE_KEY = 'dms-theme';
    var THEMES = ['light', 'dark', 'purple', 'green'];
    var DEFAULT_THEME = 'light';

    function getStored() {
        try {
            return localStorage.getItem(STORAGE_KEY);
        } catch (e) {
            return null;
        }
    }

    function store(theme) {
        try {
            localStorage.setItem(STORAGE_KEY, theme);
        } catch (e) {
            // localStorage might be disabled (private mode, etc.) — silently ignore
        }
    }

    function apply(theme) {
        if (THEMES.indexOf(theme) === -1) {
            theme = DEFAULT_THEME;
        }
        if (theme === DEFAULT_THEME) {
            // No data-theme attribute = default light, simplest markup
            document.documentElement.removeAttribute('data-theme');
        } else {
            document.documentElement.setAttribute('data-theme', theme);
        }
        // Update active state in any theme picker UI on the page
        var picks = document.querySelectorAll('.theme-pick');
        for (var i = 0; i < picks.length; i++) {
            var p = picks[i];
            if (p.getAttribute('data-theme') === theme) {
                p.classList.add('active');
            } else {
                p.classList.remove('active');
            }
        }
    }

    function onClick(e) {
        var t = e.target.closest('.theme-pick');
        if (!t) return;
        e.preventDefault();
        var theme = t.getAttribute('data-theme');
        apply(theme);
        store(theme);
    }

    function init() {
        // Apply stored theme (also handled by inline <head> script for no-FOUC,
        // but we re-apply here to keep the active-state indicator in sync)
        var stored = getStored() || DEFAULT_THEME;
        apply(stored);

        // Bind clicks (event delegation on document — works for AJAX-injected UI)
        document.addEventListener('click', onClick);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
