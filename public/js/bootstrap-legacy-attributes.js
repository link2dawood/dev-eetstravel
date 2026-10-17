// Adapt older view fragments to the Bootstrap 5 data API, including AJAX inserts.
// Explicit data-bs-* settings always take precedence. No Bootstrap plugin is
// initialized here; Bootstrap's own delegated handlers continue to own events.
(function () {
    'use strict';
    var attributes = ['toggle', 'dismiss', 'target', 'parent', 'backdrop', 'keyboard'];
    var supported = ['modal', 'dropdown', 'tab', 'pill', 'collapse', 'alert', 'offcanvas'];
    var selector = attributes.map(function (name) { return '[data-' + name + ']'; }).join(',');

    function adapt(element) {
        // Preserve readable hover help without requiring tooltip initialization.
        if (element.getAttribute('data-toggle') === 'tooltip' && !element.getAttribute('title')) {
            var title = element.getAttribute('data-original-title');
            if (title) element.setAttribute('title', title);
        }
        attributes.forEach(function (name) {
            var value = element.getAttribute('data-' + name);
            if (value === null || element.hasAttribute('data-bs-' + name)) return;
            if ((name === 'toggle' || name === 'dismiss') && supported.indexOf(value) === -1) return;
            element.setAttribute('data-bs-' + name, value);
        });
    }

    function scan(root) {
        if (root.nodeType === 1 && root.matches(selector)) adapt(root);
        if (root.querySelectorAll) root.querySelectorAll(selector).forEach(adapt);
    }

    scan(document);
    new MutationObserver(function (mutations) {
        mutations.forEach(function (mutation) {
            if (mutation.type === 'attributes') adapt(mutation.target);
            else mutation.addedNodes.forEach(scan);
        });
    }).observe(document.documentElement, {
        childList: true,
        subtree: true,
        attributes: true,
        attributeFilter: attributes.map(function (name) { return 'data-' + name; })
    });
}());
