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
            if (!/^\/(users|roles|permissions)\/(1|2147483647)$/.test(url.pathname)) return route.abort();
            return route.continue();
        });
        const page = await context.newPage();
        for (const width of [390,1440]) {
            await page.setViewportSize({width,height:900});
            for (const resource of ['users','roles','permissions']) {
                const response = await page.goto(`${base}/${resource}/1`, {waitUntil:'domcontentloaded',timeout:60000});
                assert.equal(response.status(),200,resource);
                assert.equal(await page.locator('#resource-details').isVisible(),true);
                assert.ok(await page.locator('#resource-details dd').count() >= 3);
                assert.equal(await page.locator('a',{hasText:'Back to list'}).getAttribute('href'), `${base}/${resource}`);
                assert.equal(await page.locator('#resource-details input').count(),0);
                assert.ok(!(await page.locator('#resource-details').innerText()).toLowerCase().includes('password'));
            }
        }
        for (const resource of ['users','roles','permissions']) {
            const response = await page.goto(`${base}/${resource}/2147483647`,{waitUntil:'domcontentloaded'});
            assert.equal(response.status(),404,resource);
        }
        const guest = await browser.newContext();
        for (const resource of ['users','roles','permissions']) {
            const response = await guest.request.get(`${base}/${resource}/1`,{maxRedirects:0});
            assert.equal(response.status(),302);
            assert.ok(response.headers().location.endsWith('/login'));
        }
        await guest.close();
        console.log('PASS: user/role/permission details render on phone/desktop with read-only fields and back links, missing IDs return 404, and unauthenticated requests redirect to login. No accounts or permissions changed.');
    } finally {
        await browser.close();
    }
})().catch(error => { console.error(error); process.exitCode = 1; });
