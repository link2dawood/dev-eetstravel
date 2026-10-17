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
            if (!['/invoices/2','/accounting/1','/chat/main','/status/1','/status/1/edit'].includes(url.pathname)) return route.abort();
            return route.continue();
        });
        const page = await context.newPage();
        for(const endpoint of ['/invoices/2','/accounting/1','/chat/main','/status/1']) {
            const response=await page.goto(base+endpoint,{waitUntil:'domcontentloaded',timeout:60000});
            assert.equal(response.status(),200,endpoint);
            assert.equal(await page.locator('a button, button a, button button').count(),0,endpoint);
            if(endpoint==='/invoices/2' || endpoint==='/accounting/1') {
                const link=page.locator('.margin_button a',{hasText:'Back'});
                assert.equal(await link.count(),1);
                assert.ok((await link.getAttribute('class')).includes('btn-primary'));
                assert.equal(new URL(await link.getAttribute('href')).pathname,endpoint.startsWith('/invoices')?'/invoices':'/accounting');
            }
            if(endpoint==='/chat/main') {
                assert.equal(await page.locator('button.add-direct-chat').count(),3);
                for(const control of await page.locator('button.add-direct-chat').all()) assert.equal(await control.getAttribute('type'),'button');
            }
        }
        const edit=page.locator('.margin_button a',{hasText:'Edit'});
        assert.equal(new URL(await edit.getAttribute('href')).pathname,'/status/1/edit');
        await Promise.all([page.waitForURL('**/status/1/edit',{waitUntil:'domcontentloaded'}),edit.click()]);
        await page.goBack({waitUntil:'domcontentloaded'});
        const back=page.locator('.margin_button button',{hasText:'Back'});
        assert.equal(await back.getAttribute('type'),'button');
        await back.focus();
        assert.equal(await back.evaluate(el=>document.activeElement===el),true);
        console.log('PASS: no nested interactive controls on all four audited pages, navigation links retain destinations and styles, Status Edit navigates correctly, history Back is a non-submit button, and chat actions remain native buttons. No forms submitted.');
    } finally {
        await browser.close();
    }
})().catch(error => { console.error(error); process.exitCode = 1; });
