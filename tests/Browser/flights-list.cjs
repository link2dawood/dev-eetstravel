const assert = require('node:assert/strict');
const fs = require('node:fs');
const path = require('node:path');
const { chromium } = require(process.env.PLAYWRIGHT_MODULE || 'playwright');

// Supply an authenticated local storageState; this test never submits data.
(async () => {
    const base = 'http://127.0.0.1:8010';
    const browser = await chromium.launch({ headless: true, executablePath: process.env.PLAYWRIGHT_EXECUTABLE_PATH });
    try {
        const context = await browser.newContext({ storageState: 'storage/app/sample-data/section-checks/browser-state.json', viewport: { width: 390, height: 844 } });
        await context.route('**/*', route => {
            const request = route.request(), url = new URL(request.url());
            const root = path.resolve('public'), file = path.resolve(root, '.' + decodeURIComponent(url.pathname));
            const types = { '.js': 'application/javascript', '.css': 'text/css', '.woff2': 'font/woff2', '.woff': 'font/woff', '.ttf': 'font/ttf', '.png': 'image/png', '.jpg': 'image/jpeg', '.svg': 'image/svg+xml', '.gif': 'image/gif', '.ico': 'image/x-icon' };
            if (url.origin !== base || !['GET', 'HEAD'].includes(request.method())) return route.abort();
            if (types[path.extname(file)] && file.startsWith(root + path.sep) && fs.existsSync(file)) return route.fulfill({ path: file, contentType: types[path.extname(file)] });
            if (url.pathname !== '/flights') return route.abort();
            return route.continue();
        });
        const page = await context.newPage();
        const response = await page.goto(base + '/flights', { waitUntil: 'domcontentloaded', timeout: 60000 });
        assert.equal(response.status(), 200);
        const table = page.locator('#flights-table');
        await table.waitFor({state:'visible'});
        const rows = await table.locator('tbody tr').evaluateAll(elements => elements.filter(row => !row.classList.contains('table-empty')).map(row => ({
            id: row.cells[0].textContent.trim().replace(/^#/, ''),
            links: [...row.querySelectorAll('a[href]')].map(link => link.getAttribute('href')).filter(link => !link.startsWith('javascript:')),
            deleteLink: row.querySelector('.action-delete-btn')?.getAttribute('data-delete-url')
        })));
        assert.ok(rows.length > 0, 'Check existing flight action links');
        for (const row of rows) {
            assert.match(row.id, /^\d+$/);
            const paths = row.links.map(link => new URL(link, base).pathname);
            assert.ok(paths.includes(`/flights/${row.id}`), 'Show link must contain flight ID');
            assert.ok(paths.includes(`/flights/${row.id}/edit`), 'Edit link must contain flight ID');
            assert.equal(new URL(row.deleteLink, base).pathname, `/flights/${row.id}/delete`);
            assert.ok(row.links.every(link => !new URL(link, base).searchParams.has('id')));
        }
        await page.locator('#flights-search').fill('QA-no-such-flight-7219');
        await page.locator('#flights-search').press('ArrowRight');
        assert.equal(await table.locator('.table-search-empty').isVisible(), true);
        await page.locator('#flights-search').fill('');
        await page.locator('#flights-search').press('ArrowRight');
        assert.equal(await table.locator('.table-search-empty').count(), 0);
        await page.setViewportSize({width:1440,height:1000});
        assert.equal(await table.isVisible(), true);
        console.log(`PASS: Flights returns HTTP 200, ${rows.length} existing rows have correct show/edit/delete URLs, search resets, and phone/desktop table renders. No row actions opened or records changed.`);
    } finally {
        await browser.close();
    }
})().catch(error => { console.error(error); process.exitCode = 1; });
