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
            if (!['/settings','/settings/create'].includes(url.pathname)) return route.abort();
            return route.continue();
        });
        const page = await context.newPage();
        const access=await page.goto(base+'/settings',{waitUntil:'domcontentloaded',timeout:60000});
        assert.equal(access.status(),403,'Existing access restriction remains unchanged');
        for(const [index,endpoint] of ['/settings','/settings/create'].entries()) {
            await context.route(base+endpoint,route=>route.fulfill({path:`storage/app/ui-audit/ui29-settings-${index}.html`,contentType:'text/html'}));
            await page.goto(base+endpoint,{waitUntil:'domcontentloaded'});
            const group=page.locator('aside .nav-item.dropdown').filter({has:page.locator('a[href="#navbar-users"]')});
            const current=group.locator('.dropdown-item.active');
            assert.equal(await current.count(),1);
            assert.equal(new URL(await current.getAttribute('href'),base).pathname,'/settings');
            assert.equal(await group.locator('a[href="#navbar-users"]').getAttribute('aria-expanded'),'true');
            assert.equal(await group.locator('.dropdown-menu.show').count(),1);
            assert.ok((await group.getAttribute('class')).includes('active'));
        }
        console.log('PASS: Settings index/create authorized previews highlight Settings and expand administration; actual account remains HTTP 403. No settings or permission records changed.');
    } finally {
        await browser.close();
    }
})().catch(error => { console.error(error); process.exitCode = 1; });
