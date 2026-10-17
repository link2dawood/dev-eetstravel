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
        const rows=page.locator('#items-contacts .item-contact');
        const original=await rows.count();
        for(let index=0;index<2;index++) {
            await page.locator('#add_contact').click();
            await page.waitForFunction(count=>document.querySelectorAll('#items-contacts .item-contact').length===count,original+index+1);
        }
        for(const width of [390,1440]) {
            await page.setViewportSize({width,height:1000});
            for(let row=0;row<await rows.count();row++) {
                const labels=rows.nth(row).locator('label[for]');
                assert.equal(await labels.count(),4);
                for(let index=0;index<4;index++) {
                    const label=labels.nth(index);
                    const id=await label.getAttribute('for');
                    assert.equal(await page.locator(`[id="${id}"]`).count(),1);
                    await label.click();
                    assert.equal(await page.evaluate(()=>document.activeElement.id),id);
                    assert.ok(await page.evaluate(()=>document.activeElement.name.startsWith('contacts[')));
                }
            }
        }
        assert.equal(await page.locator('#contact_email').count(),1,'Company email retains its original ID');
        const names=await rows.locator('input').evaluateAll(inputs=>inputs.map(input=>input.name));
        assert.equal(new Set(names).size,names.length);
        assert.ok(names.every(name=>/^contacts\[\d+\]\[contact_(full_name|mobile_phone|work_phone|email)\]$/.test(name)));
        console.log(`PASS: all four labels focus their own field in ${original} loaded and two new contact rows on phone/desktop; unique IDs and existing submission names preserved. No records saved.`);
    } finally {
        await browser.close();
    }
})().catch(error => { console.error(error); process.exitCode = 1; });
