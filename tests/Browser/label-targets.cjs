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
            if (!['/invoices/2/edit','/driver/create','/driver/1/edit','/flights/create','/cruises/create','/season/1/create','/roles/create','/permissions/create'].includes(url.pathname)) return route.abort();
            return route.continue();
        });
        const page = await context.newPage();
        for(const endpoint of ['/invoices/2/edit','/driver/create','/driver/1/edit','/flights/create','/cruises/create','/season/1/create','/roles/create','/permissions/create']) {
            const response=await page.goto(base+endpoint,{waitUntil:'domcontentloaded',timeout:60000});
            assert.equal(response.status(),200,endpoint);
            const broken=await page.locator('.page-body').evaluate(root=>[...root.querySelectorAll('label[for]')].filter(label=>!label.htmlFor || !label.control).map(label=>({text:label.textContent.trim(),for:label.htmlFor})));
            console.log(endpoint,JSON.stringify(broken));
            assert.deepEqual(broken,[],endpoint);
            const controls=await page.locator('.page-body label[for]').evaluateAll(labels=>labels.filter(label=>label.control && ['INPUT','TEXTAREA'].includes(label.control.tagName) && !['checkbox','radio','file','hidden'].includes(label.control.type) && !/datepicker|timepicker/.test(label.control.className) && !label.control.disabled).map(label=>label.htmlFor));
            for(const id of controls) {
                const label=page.locator(`label[for="${id}"]`).first();
                if(!await label.isVisible())continue;
                await label.click();
                assert.equal(await page.evaluate(()=>document.activeElement.id),id);
            }
        }
        console.log('PASS: labels resolve to real controls on all eight audited forms; visible text-field labels focus their targets. No forms submitted.');
    } finally {
        await browser.close();
    }
})().catch(error => { console.error(error); process.exitCode = 1; });
