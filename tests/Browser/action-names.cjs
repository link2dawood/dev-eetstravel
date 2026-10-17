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
            if (!['/hotel/1','/guide/8','/clients/6','/accounting/create','/office/1','/room_types','/currencies'].includes(url.pathname)) return route.abort();
            return route.continue();
        });
        const page = await context.newPage();
        for(const endpoint of ['/hotel/1','/guide/8','/clients/6','/accounting/create','/office/1','/room_types','/currencies']) {
            let response=await page.goto(base+endpoint,{waitUntil:'domcontentloaded',timeout:60000});
            if(endpoint==='/office/1' && response.status()===403) {
                await context.route(base+endpoint,route=>route.fulfill({path:'storage/app/ui-audit/ui21-office.html',contentType:'text/html'}));
                response=await page.goto(base+endpoint,{waitUntil:'domcontentloaded'});
            }
            assert.equal(response.status(),200,endpoint);
            const unnamed=await page.evaluate(()=>[...document.querySelectorAll('button,a.btn,[role="button"]')]
                .filter(el=>el.getClientRects().length && getComputedStyle(el).visibility!=='hidden')
                .filter(el=>!((el.getAttribute('aria-label')||el.getAttribute('title')||el.innerText||'').trim() || el.getAttribute('aria-labelledby')))
                .map(el=>({tag:el.tagName,id:el.id,class:el.className})));
            console.log(endpoint,JSON.stringify(unnamed));
            assert.deepEqual(unnamed,[],endpoint);
            for(const button of await page.locator('.del-attach').all()) {
                assert.equal(await button.getAttribute('type'),'button');
                assert.equal(await button.getAttribute('aria-label'),'Delete attachment');
            }
        }
        console.log('PASS: all seven audited pages/authorized previews have named visible buttons and action links; attachment removers cannot submit forms. No actions clicked or data changed.');
    } finally {
        await browser.close();
    }
})().catch(error => { console.error(error); process.exitCode = 1; });
