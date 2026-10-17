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
            if (!['/hotel/create', '/hotel/1/edit', '/event/create', '/clients/9/edit', '/api/getClientContacts', '/api/getItemContactView'].includes(url.pathname)) return route.abort();
            return route.continue();
        });
        const page = await context.newPage();
        for (const endpoint of ['/hotel/create','/hotel/1/edit']) {
        await page.goto(base+endpoint,{waitUntil:'domcontentloaded',timeout:60000});
        for(const name of ['name','address_first','work_phone','work_email','country','city']) {
            const field=page.locator(`[name="${name}"]`);
            assert.equal(await field.evaluate(el=>el.required),true,name);
            assert.equal(await field.evaluate(el=>[...el.labels].some(label=>label.classList.contains('required')||label.textContent.includes('*'))),true,name);
        }
        }
        await page.goto(base+'/event/create',{waitUntil:'domcontentloaded'});
        for(const name of ['country','city']) {
            const field=page.locator(`[name="${name}"]`);
            assert.equal(await field.evaluate(el=>el.required),false);
            assert.equal(await field.evaluate(el=>[...el.labels].some(label=>label.classList.contains('required')||label.textContent.includes('*'))),false);
        }
        await page.goto(base+'/clients/9/edit',{waitUntil:'domcontentloaded'});
        await page.waitForFunction(()=>!document.querySelector('#add_contact').disabled);
        const before=await page.locator('.item-contact').count();
        await page.locator('#add_contact').click();
        await page.waitForFunction(count=>document.querySelectorAll('.item-contact').length===count,before+1);
        const field=page.locator('.item-contact').last().locator('input[name$="[contact_full_name]"]');
        assert.equal(await field.evaluate(el=>el.required && el.validity.valueMissing),true);
        assert.equal(await field.evaluate(el=>[...el.labels].some(label=>label.textContent.includes('*'))),true);
        await field.fill('QA Required Contact');
        assert.equal(await field.evaluate(el=>el.checkValidity()),true);
        assert.equal(await page.locator('.item-contact').last().locator('input[name$="[contact_email]"]').evaluate(el=>el.required),false);
        console.log('PASS: hotel required controls have matching markers; event locations are optional; added client contact names are visibly required and enforce empty-input validity. No forms submitted.');
    } finally {
        await browser.close();
    }
})().catch(error => { console.error(error); process.exitCode = 1; });
