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
            if (!['/clients/9/edit', '/api/getClientContacts', '/api/getItemContactView'].includes(url.pathname)) return route.abort();
            return route.continue();
        });
        const page = await context.newPage();
        await page.goto(base+'/clients/9/edit',{waitUntil:'domcontentloaded',timeout:60000});
        await page.waitForFunction(()=>!document.querySelector('#add_contact').disabled);
        await page.evaluate(()=>{
            window.qaSubmits=0;
            document.querySelector('.client-form').addEventListener('submit',event=>{event.preventDefault();window.qaSubmits++;});
        });
        const rows=page.locator('#items-contacts .item-contact');
        const original=await rows.count();
        for(let index=0;index<3;index++) {
            await page.locator('#add_contact').click();
            await page.waitForFunction(count=>document.querySelectorAll('#items-contacts .item-contact').length===count,original+index+1);
            await rows.last().locator('input[name$="[contact_full_name]"]').fill(`Keyboard QA ${index}`);
        }
        const middle=rows.nth(original+1).getByRole('button',{name:'Remove contact'});
        assert.equal(await middle.getAttribute('type'),'button');
        await middle.focus();
        await page.keyboard.press('Tab');
        await page.keyboard.press('Shift+Tab');
        assert.equal(await middle.evaluate(el=>document.activeElement===el),true);
        await page.keyboard.press('Enter');
        assert.equal(await rows.count(),original+2);
        assert.equal(await rows.last().locator('input[name$="[contact_full_name]"]').inputValue(),'Keyboard QA 2');
        assert.equal(await rows.last().locator('.remove-contact').evaluate(el=>document.activeElement===el),true);
        await page.keyboard.press('Space');
        assert.equal(await rows.count(),original+1);
        assert.equal(await rows.last().locator('.remove-contact').evaluate(el=>document.activeElement===el),true);
        assert.equal(await rows.last().locator('input[name$="[contact_full_name]"]').inputValue(),'Keyboard QA 0');
        await rows.last().getByRole('button',{name:'Remove contact'}).click();
        assert.equal(await rows.count(),original);
        if(!original) assert.equal(await page.locator('#add_contact').evaluate(el=>document.activeElement===el),true);
        assert.equal(await page.evaluate(()=>window.qaSubmits),0);
        console.log('PASS: removal button is tab-reachable and labeled; Enter, Space and mouse remove only intended new rows, preserve neighbors, transfer focus and never submit. No contacts saved or existing rows removed.');
    } finally {
        await browser.close();
    }
})().catch(error => { console.error(error); process.exitCode = 1; });
