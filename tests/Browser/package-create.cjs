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
            if (!/^\/tour_package(?:\/create|\/\d+\/create)?$/.test(url.pathname)) return route.abort();
            return route.continue();
        });
        const page = await context.newPage();
        const list = await page.goto(base + '/tour_package', {waitUntil:'domcontentloaded',timeout:60000});
        assert.equal(list.status(), 200);
        await Promise.all([
            page.waitForURL(url => url.pathname === '/tour_package/create', {waitUntil:'domcontentloaded'}),
            page.locator('form[action$="/tour_package/create"] button').click()
        ]);
        const select = page.locator('#tour_day_id');
        assert.equal(await select.isVisible(), true);
        assert.equal(await select.evaluate(el => el.validity.valueMissing), true);
        const id = await select.locator('option').evaluateAll(options => options.find(option => option.value)?.value);
        assert.ok(id, 'Existing tour days should be selectable');
        await select.selectOption(id);
        const response = page.waitForResponse(response => new URL(response.url()).pathname === `/tour_package/${id}/create`);
        await page.locator('button[type="submit"]', {hasText:'Continue'}).click();
        assert.equal((await response).status(), 200);
        await page.waitForURL(`**/tour_package/${id}/create`);
        assert.equal(await page.locator('#tour_package_create_form').isVisible(), true);
        for (const query of ['tour_day_id=abc', 'tour_day_id[]=1', 'tour_day_id=0']) {
            const invalid = await page.goto(base + '/tour_package/create?' + query, {waitUntil:'domcontentloaded'});
            assert.equal(invalid.status(), 422);
        }
        const missing = await page.goto(base + '/tour_package/create?tour_day_id=2147483647', {waitUntil:'domcontentloaded'});
        assert.equal(missing.status(), 404);
        console.log('PASS: New opens tour-day selector, requires context, continues to real package form, rejects malformed/missing IDs. No packages created.');
    } finally {
        await browser.close();
    }
})().catch(error => { console.error(error); process.exitCode = 1; });
