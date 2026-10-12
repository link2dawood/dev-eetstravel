// Server-side list search: an input with data-list-search reloads the page with ?search=
// after the user stops typing, so the whole list is searched, not only the visible page.
(function () {
    document.querySelectorAll('input[data-list-search]').forEach(function (input) {
        var timer = null;
        var initial = input.value;

        function go() {
            var term = input.value.trim();
            if (term === initial.trim()) return;
            var url = new URL(window.location.href);
            if (term) url.searchParams.set('search', term); else url.searchParams.delete('search');
            url.searchParams.delete('page');
            window.location.href = url.toString();
        }

        input.addEventListener('input', function () {
            clearTimeout(timer);
            timer = setTimeout(go, 500);
        });
        input.addEventListener('keydown', function (e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                clearTimeout(timer);
                go();
            }
        });

        // Keep the cursor in the box after the reload
        if (initial) {
            input.focus();
            input.setSelectionRange(initial.length, initial.length);
        }
    });
})();
