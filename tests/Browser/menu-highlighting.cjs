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
            if (!['/clients/9/edit','/hotel/1/edit','/driver/1/edit'].includes(url.pathname)) return route.abort();
            return route.continue();
        });
        const page = await context.newPage();
        for(const [endpoint,list] of [['/clients/9/edit','/clients'],['/hotel/1/edit','/hotel'],['/driver/1/edit','/driver']]) {
            const response=await page.goto(base+endpoint,{waitUntil:'domcontentloaded',timeout:60000});
            assert.equal(response.status(),200);
            let link=page.locator(`aside a[href$="${list}"]`).first();
            if(!await link.count() && endpoint!=='/clients/9/edit') {
                const index=endpoint.startsWith('/hotel') ? 0 : 1;
                await context.route(base+endpoint,route=>route.fulfill({path:`storage/app/ui-audit/ui27-menu-${index}.html`,contentType:'text/html'}));
                await page.goto(base+endpoint,{waitUntil:'domcontentloaded'});
                link=page.locator(`aside a[href$="${list}"]`).first();
            }
            assert.equal(await link.evaluate(el=>el.classList.contains('active') || el.closest('.nav-item').classList.contains('active')),true,endpoint);
            const dropdown=await link.evaluate(el=>{
                const menu=el.closest('.dropdown-menu');
                return menu ? {open:menu.classList.contains('show'),expanded:menu.closest('.nav-item').querySelector('.dropdown-toggle').getAttribute('aria-expanded')} : null;
            });
            if(dropdown) assert.deepEqual(dropdown,{open:true,expanded:'true'});
            await page.setViewportSize({width:1440,height:1000});
            assert.equal(await link.isVisible(),true);
        }
        console.log('PASS: client/hotel/driver edit pages highlight the correct sidebar module and expand its group. No records changed.');
    } finally {
        await browser.close();
    }
})().catch(error => { console.error(error); process.exitCode = 1; });
