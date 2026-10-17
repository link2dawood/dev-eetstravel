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
            if (url.pathname !== '/guestlist') return route.abort();
            return route.continue();
        });
        const page = await context.newPage();
        const response = await page.goto(base + '/guestlist', {waitUntil:'domcontentloaded',timeout:60000});
        assert.equal(response.status(),200);
        const table = page.locator('#guest-list-table');
        await table.waitFor({state:'visible'});
        assert.ok((await page.title()).includes('Guest Lists'));
        const records = await table.locator('[data-guest-list-id]').evaluateAll(rows => rows.map(row => ({
            id:row.dataset.guestListId, href:row.querySelector('a').href,
            name:row.cells[1].textContent.trim()
        })));
        for (const record of records) assert.equal(new URL(record.href).pathname, `/guestlist/${record.id}/showbyid`);
        await page.locator('#guest-list-search').fill('QA-no-such-guest-list-728e');
        assert.equal(await table.locator('.table-search-empty').isVisible(),true);
        await page.locator('#guest-list-search').fill('');
        if (records.length) {
            assert.equal(await table.locator('.table-search-empty').count(),0);
            assert.ok(await table.locator('[data-guest-list-id]:visible').count() > 0);
        }
        await page.setViewportSize({width:1440,height:1000});
        assert.equal(await table.isVisible(),true);
        console.log(`PASS: guest-list HTTP 200, correct labels, ${records.length} existing view links, search/no-results/reset, phone/desktop render. No records changed or send/delete actions invoked.`);
    } finally {
        await browser.close();
    }
})().catch(error => { console.error(error); process.exitCode = 1; });
