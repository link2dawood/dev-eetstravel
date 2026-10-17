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
            if (url.pathname !== '/holiday') return route.abort();
            return route.continue();
        });
        const page = await context.newPage();
        const access = await page.goto(base + '/holiday', {waitUntil:'domcontentloaded', timeout:60000});
        assert.equal(access.status(),403,'Existing account access restriction must remain unchanged');
        await context.route('**/holiday', route => route.fulfill({path:'storage/app/ui-audit/ui12-holiday.html', contentType:'text/html'}));
        const errors = [];
        page.on('pageerror', error => errors.push(error.message));
        await page.goto(base + '/holiday', {waitUntil:'domcontentloaded'});
        const table = page.locator('#holidaycalendar-table');
        await table.waitFor({state:'visible'});
        const records = await table.locator('[data-holiday-id]').evaluateAll(rows => rows.map(row => ({
            id:row.dataset.holidayId, links:[...row.querySelectorAll('a[href]')].map(link => link.href)
        })));
        for (const row of records) {
            const paths = row.links.map(link => new URL(link).pathname);
            assert.ok(paths.includes(`/holiday/${row.id}`));
            assert.ok(paths.includes(`/holiday/${row.id}/edit`));
        }
        await page.locator('#holiday-search').fill('QA-no-such-holiday-9812');
        assert.equal(await table.locator('.table-search-empty').isVisible(),true);
        await page.locator('#holiday-search').fill('');
        if (records.length) assert.ok(await table.locator('[data-holiday-id]:visible').count() > 0);
        await page.setViewportSize({width:1440,height:1000});
        assert.equal(await table.isVisible(),true);
        assert.deepEqual(errors,[]);
        console.log(`PASS: actual account remains HTTP 403; read-only authorized preview renders without JavaScript errors on phone/desktop, ${records.length} records have valid links, search/no-results/reset work. No permissions or holidays changed.`);
    } finally {
        await browser.close();
    }
})().catch(error => { console.error(error); process.exitCode = 1; });
