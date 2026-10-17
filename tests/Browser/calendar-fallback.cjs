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
            if (!['/', '/home'].includes(url.pathname)) return route.abort();
            return route.continue();
        });
        const page = await context.newPage();
        const errors=[];
        page.on('pageerror',error=>errors.push(error.message));
        for(const endpoint of ['/','/home']) {
            const response=await page.goto(base+endpoint,{waitUntil:'domcontentloaded',timeout:60000});
            assert.equal(response.status(),200);
            for(const width of [390,1440]) {
                await page.setViewportSize({width,height:1000});
                assert.equal(await page.locator('#calendar-unavailable').isVisible(),true);
                assert.equal(await page.locator('#bootsnipp-calendar').isVisible(),false);
                const links=await page.locator('#calendar-unavailable a').evaluateAll(elements=>elements.map(el=>new URL(el.href).pathname));
                assert.ok(links.includes('/task'));
                const rect=await page.locator('#calendar-unavailable').boundingBox();
                assert.ok(rect.x>=0 && rect.x+rect.width<=width);
            }
        }
        await Promise.all([
            page.waitForNavigation({waitUntil:'domcontentloaded'}),
            page.getByRole('button',{name:'Retry calendar'}).click()
        ]);
        assert.equal(await page.locator('#calendar-unavailable').isVisible(),true);
        assert.ok(!errors.some(error=>/FullCalendar/.test(error)),errors.join('\n'));
        console.log('PASS: blocked FullCalendar CDN shows a contained fallback on dashboard/root at phone/desktop widths, retains task-list access and retries without FullCalendar exceptions. No records changed.');
    } finally {
        await browser.close();
    }
})().catch(error => { console.error(error); process.exitCode = 1; });
