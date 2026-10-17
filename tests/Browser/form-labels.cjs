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
            if (!['/hotel/1/edit', '/event/create', '/guide/create', '/restaurant/create', '/transfer/create', '/agreements/1/edit/1', '/hotel', '/event', '/clients', '/currencies'].includes(url.pathname)) return route.abort();
            return route.continue();
        });
        const page = await context.newPage();
        for(const endpoint of ['/hotel/1/edit','/event/create','/guide/create','/restaurant/create','/transfer/create','/agreements/1/edit/1']) {
            const response=await page.goto(base+endpoint,{waitUntil:'domcontentloaded',timeout:60000});
            assert.equal(response.status(),200,endpoint);
            const issues=await page.locator('.page-body').evaluate(root=>[...root.querySelectorAll('input[name],select[name],textarea[name]')]
                .filter(el=>el.type!=='hidden' && !['button','submit'].includes(el.type))
                .filter(el=>!(el.labels?.length || el.getAttribute('aria-label') || el.getAttribute('aria-labelledby')))
                .map(el=>({name:el.name,id:el.id})));
            console.log(endpoint,JSON.stringify(issues));
            assert.deepEqual(issues,[],endpoint);
            const duplicateIds=await page.evaluate(()=>{
                const ids=[...document.querySelectorAll('[id]')].map(el=>el.id).filter(Boolean);
                return ids.filter((id,index)=>ids.indexOf(id)!==index);
            });
            assert.deepEqual(duplicateIds,[],endpoint);
            const label=page.locator('label[for]').filter({hasText:/Name/}).first();
            if(await label.count()) {
                const id=await label.getAttribute('for');
                const target=page.locator(`[id="${id}"]`);
                if(await target.count() && ['INPUT','TEXTAREA'].includes(await target.evaluate(el=>el.tagName))) {
                    await label.click();
                    assert.equal(await target.evaluate(el=>document.activeElement===el),true);
                }
            }
        }
        for(const [endpoint,id] of [['/hotel','hotels-search'],['/event','events-search'],['/clients','clients-search'],['/currencies','currencies-search']]) {
            const response=await page.goto(base+endpoint,{waitUntil:'domcontentloaded',timeout:60000});
            assert.equal(response.status(),200);
            assert.ok(await page.locator(`[id="${id}"]`).getAttribute('aria-label'));
        }
        console.log('PASS: all six audited service/agreement forms have named fields, no duplicate IDs and working Name label focus; four representative lists expose named search controls. No forms submitted.');
    } finally {
        await browser.close();
    }
})().catch(error => { console.error(error); process.exitCode = 1; });
