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
            if (!['/flights/1', '/flights/1/edit'].includes(url.pathname)) return route.abort();
            return route.continue();
        });
        const page = await context.newPage();
        for (const endpoint of ['/flights/1', '/flights/1/edit']) {
            const response = await page.goto(base + endpoint, {waitUntil:'domcontentloaded',timeout:60000});
            assert.equal(response.status(), 200, endpoint);
            assert.ok(await page.locator('[role="status"]').filter({hasText:'Google location details are temporarily unavailable'}).isVisible());
            if (endpoint.endsWith('/edit')) {
                assert.ok(await page.locator('#name').inputValue());
                const location = await page.locator('#place_id').inputValue();
                assert.ok(location, 'Saved location must survive failed lookup');
                assert.equal(await page.locator('#place_id').evaluate(el => new FormData(el.form).get('place_id')), location);
            } else {
                assert.ok(await page.locator('#info-tab').textContent());
            }
        }
        console.log('PASS: real flight detail/edit return HTTP 200 during unavailable Google lookup; fallback shown and saved location remains in edit submission data. No forms submitted.');
    } finally {
        await browser.close();
    }
})().catch(error => { console.error(error); process.exitCode = 1; });
