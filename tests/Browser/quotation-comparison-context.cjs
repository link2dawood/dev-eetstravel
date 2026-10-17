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
            if (!['/quotation/2', '/quotation/2147483647', '/comparison', '/comparison/create'].includes(url.pathname)) return route.abort();
            return route.continue();
        });
        const page = await context.newPage();
        for (const width of [390,1440]) {
            await page.setViewportSize({width,height:900});
            const response = await page.goto(base + '/quotation/2', {waitUntil:'domcontentloaded',timeout:60000});
            assert.equal(response.status(),200);
            assert.equal(await page.locator('#quotation-details').isVisible(),true);
            assert.equal(await page.locator('a',{hasText:'Back to quotations'}).getAttribute('href'),base + '/quotation');
        }
        const missing = await page.goto(base + '/quotation/2147483647',{waitUntil:'domcontentloaded'});
        assert.equal(missing.status(),404);
        for (const [index, endpoint] of ['/comparison','/comparison/create'].entries()) {
            const restricted = await page.goto(base + endpoint,{waitUntil:'domcontentloaded'});
            assert.equal(restricted.status(),403);
            await context.route(base + endpoint, route => route.fulfill({path:`storage/app/ui-audit/ui15-comparison-${index}.html`,contentType:'text/html'}));
            for (const width of [390,1440]) {
                await page.setViewportSize({width,height:900});
                await page.goto(base + endpoint,{waitUntil:'domcontentloaded'});
                assert.equal(await page.locator('#comparison-context').isVisible(),true);
                assert.match(await page.locator('#comparison-context').innerText(),/Standalone comparison creation is not available/);
                assert.equal(await page.locator('a',{hasText:'Open quotations'}).getAttribute('href'),base + '/quotation');
            }
        }
        console.log('PASS: quotation detail content and missing-record 404; both Comparison authorized previews explain required context on phone/desktop; real account restrictions unchanged. No comparison detail routes visited or records changed.');
    } finally {
        await browser.close();
    }
})().catch(error => { console.error(error); process.exitCode = 1; });
