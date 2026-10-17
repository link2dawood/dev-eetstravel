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
            if (!['/currencies', '/event', '/transfer', '/guide'].includes(url.pathname)) return route.abort();
            return route.continue();
        });
        const page = await context.newPage();
        for (const [endpoint, search, expected] of [['/currencies','EUR','€'], ['/transfer','Pyrénées','Pyrénées']]) {
            const response = await page.goto(`${base}${endpoint}?search=${encodeURIComponent(search)}`,{waitUntil:'domcontentloaded',timeout:60000});
            assert.equal(response.status(),200,endpoint);
            const text = await page.locator('table tbody').first().innerText();
            assert.ok(text.includes(expected),`${endpoint} must display ${expected}`);
            assert.ok(!text.includes('Ã¼') && !text.includes('â‚¬') && !text.includes('Ã¢â€š'));
        }
        for (const endpoint of ['/event','/guide']) {
            const response = await page.goto(base+endpoint,{waitUntil:'domcontentloaded'});
            assert.equal(response.status(),200);
        }
        console.log('PASS: Euro and active accented transfer names display correctly and support UTF-8 searches; Events/Guides still load. Archived event names verified separately against repair backup. No browser form submissions.');
    } finally {
        await browser.close();
    }
})().catch(error => { console.error(error); process.exitCode = 1; });
