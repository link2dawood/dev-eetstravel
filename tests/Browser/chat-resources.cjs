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
            if (!['/chat', '/chat/create', '/chat/main', '/chat/1', '/chat/1/edit', '/chat/2147483647'].includes(url.pathname)) return route.abort();
            return route.continue();
        });
        const page = await context.newPage();
        for (const endpoint of ['/chat', '/chat/create']) {
            const response = await page.goto(base + endpoint, {waitUntil:'domcontentloaded',timeout:60000});
            assert.equal(response.status(),200);
            assert.equal(new URL(page.url()).pathname,'/chat/main');
            assert.equal(await page.locator('button[data-link="/chat/renderCustomChatCreateFrom"]').isVisible(),true);
        }
        for (const endpoint of ['/chat/1', '/chat/1/edit']) {
            const response = await page.goto(base + endpoint, {waitUntil:'domcontentloaded',timeout:60000});
            assert.equal(response.status(),200);
            assert.equal(new URL(page.url()).pathname,'/chat/1');
            assert.equal(await page.locator('[aria-label="Conversation messages"]').isVisible(),true);
            assert.equal(await page.locator('a', {hasText:'Open Chats'}).getAttribute('href'), base + '/chat/main');
        }
        await page.setViewportSize({width:1440,height:1000});
        assert.equal(await page.locator('[aria-label="Conversation messages"]').isVisible(),true);
        const missing = await page.goto(base + '/chat/2147483647', {waitUntil:'domcontentloaded'});
        assert.equal(missing.status(),404);
        console.log('PASS: chat index/create redirect to working conversation page; detail/edit show conversation preview on phone/desktop; missing chat returns 404. No messages or chats created/deleted.');
    } finally {
        await browser.close();
    }
})().catch(error => { console.error(error); process.exitCode = 1; });
