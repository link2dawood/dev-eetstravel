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
            if (!['/tour_package/1/edit', '/tour_package/2147483647/edit'].includes(url.pathname)) return route.abort();
            return route.continue();
        });
        const page = await context.newPage();
        for (const width of [390, 1440]) {
            await page.setViewportSize({width,height:900});
            const response = await page.goto(base + '/tour_package/1/edit', {waitUntil:'domcontentloaded',timeout:60000});
            assert.equal(response.status(),200);
            assert.equal(await page.locator('#package-missing-tour').isVisible(),true);
            assert.match(await page.locator('#package-missing-tour').innerText(), /editing is disabled/i);
            assert.ok(await page.locator('dl dd').count() >= 6);
            assert.equal(await page.locator('#tour_package_add_form').count(),0);
            assert.equal(await page.locator('a', {hasText:'Back to packages'}).getAttribute('href'), base + '/tour_package');
        }
        const missing = await page.goto(base + '/tour_package/2147483647/edit', {waitUntil:'domcontentloaded'});
        assert.equal(missing.status(),404);
        console.log('PASS: orphan package details are readable on phone/desktop, editing is disabled with a clear reason, back link exists, and missing records return 404. No records changed.');
    } finally {
        await browser.close();
    }
})().catch(error => { console.error(error); process.exitCode = 1; });
