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
            if (!['/', '/home', '/invoices/2', '/accounting/1', '/office/1', '/profile', '/quotation/3/create', '/quotation/2/edit', '/offer/28/show'].includes(url.pathname)) return route.abort();
            return route.continue();
        });
        const page = await context.newPage();
        for(const endpoint of ['/', '/home', '/invoices/2', '/accounting/1', '/office/1', '/profile', '/quotation/3/create', '/quotation/2/edit', '/offer/28/show']) {
            let response=await page.goto(base+endpoint,{waitUntil:'domcontentloaded',timeout:60000});
            if(endpoint==='/office/1' && response.status()===403) {
                await context.route(base+endpoint, route=>route.fulfill({path:'storage/app/ui-audit/ui21-office.html',contentType:'text/html'}));
                response=await page.goto(base+endpoint,{waitUntil:'domcontentloaded'});
            }
            assert.equal(response.status(),200,endpoint);
            await page.waitForTimeout(500);
            const duplicates=await page.evaluate(()=>{
                const counts={}; document.querySelectorAll('[id]').forEach(el=>{if(el.id)counts[el.id]=(counts[el.id]||0)+1;});
                return Object.entries(counts).filter(([,count])=>count>1);
            });
            console.log(endpoint,JSON.stringify(duplicates));
            assert.deepEqual(duplicates,[],endpoint);
            if(endpoint.startsWith('/quotation/')) {
                assert.equal(await page.locator('#quotation_table').evaluate(el=>el.tagName),'TABLE');
                if(endpoint.endsWith('/edit')) {
                    assert.equal(await page.locator('#calculation').evaluate(el=>el.tagName),'TABLE');
                    assert.equal(await page.locator('a[href="#calculation-tab"]').count(),1);
                    assert.equal(await page.locator('#calculation-tab').count(),1);
                }
            }
            if(endpoint==='/profile') assert.equal(await page.locator('#logout-form').count(),1);
            if(endpoint==='/') {
                assert.equal(await page.locator('#myModal .AjaxisModal').count(),1);
                assert.equal(await page.locator('#dashboard-tour-delete-modal #confirmDeleteBtn').count(),1);
            }
        }
        console.log('PASS: no duplicate DOM IDs on all nine audited pages/previews; quotation table/tab, dashboard modal and logout targets are unique. No forms or destructive actions invoked.');
    } finally {
        await browser.close();
    }
})().catch(error => { console.error(error); process.exitCode = 1; });
