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
            if (!['/clients/9/edit', '/hotel/1/edit'].includes(url.pathname)) return route.abort();
            return route.continue();
        });
        const page = await context.newPage();
        for (const width of [390,1440]) {
            await page.setViewportSize({width,height:1000});
            for (const endpoint of ['/clients/9/edit','/hotel/1/edit']) {
                await page.goto(base+endpoint,{waitUntil:'domcontentloaded',timeout:60000});
                await page.locator('.select2-selection--single').first().waitFor({state:'visible'});
                const measurements = await page.evaluate(() => ({
                    input: document.querySelector('input.form-control:not([type="hidden"])').getBoundingClientRect().height,
                    selects:[...document.querySelectorAll('.select2-selection--single')].filter(el=>el.getClientRects().length).map(el=>el.getBoundingClientRect().height)
                }));
                assert.ok(measurements.selects.length>0);
                for (const height of measurements.selects) {
                    assert.ok(height>=44, 'Comfortable control height');
                    assert.ok(Math.abs(height-measurements.input)<=1, JSON.stringify(measurements));
                }
                const select = page.locator('.select2-selection--single').first();
                await select.click();
                assert.equal(await page.locator('.select2-dropdown').isVisible(),true);
                await page.keyboard.press('Escape');
                assert.equal(await page.locator('.select2-dropdown').count(),0);
            }
        }
        await page.evaluate(() => {
            const wrapper=document.createElement('div'); wrapper.style.width='280px';
            const select=document.createElement('select'); select.id='qa-multi'; select.multiple=true;
            for(const name of ['First long selected option','Second long selected option','Third long selected option']) {
                select.add(new Option(name,name,true,true));
            }
            wrapper.append(select); document.querySelector('.page-body').prepend(wrapper);
            $(select).select2({width:'100%'});
        });
        const multi=page.locator('#qa-multi + .select2 .select2-selection--multiple');
        assert.ok(await multi.evaluate(el=>el.getBoundingClientRect().height)>44);
        assert.ok(await multi.evaluate(el=>el.scrollWidth<=el.clientWidth+1));
        await multi.locator('.select2-selection__choice__remove').first().click();
        assert.equal(await multi.locator('.select2-selection__choice').count(),2);
        console.log('PASS: client/hotel single selects match text input heights at phone/desktop widths, dropdown open/Escape work, and multiple selections wrap and remain removable. No forms submitted.');
    } finally {
        await browser.close();
    }
})().catch(error => { console.error(error); process.exitCode = 1; });
