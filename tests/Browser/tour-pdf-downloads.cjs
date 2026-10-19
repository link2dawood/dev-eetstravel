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
            if (!['/tour/65', '/tour/65/export/voucher', '/tour/65/export/short'].includes(url.pathname)) return route.abort();
            return route.continue();
        });
        const page = await context.newPage();
        const response=await page.goto(base+'/tour/65',{waitUntil:'domcontentloaded',timeout:60000});
        assert.equal(response.status(),200);
        for(const type of ['voucher','short']) {
            const link=page.locator('a[href$="/tour/65/export/'+type+'"]');
            assert.equal(await link.count(),1);
            assert.equal(await link.getAttribute('onclick'),null);
            await link.locator('..').locator('..').locator('button.dropdown-toggle').click();
            const pending=page.waitForEvent('download');
            await link.click();
            const download=await pending;
            const stream=await download.createReadStream(),chunks=[];
            for await(const chunk of stream)chunks.push(chunk);
            const bytes=Buffer.concat(chunks);
            assert.equal(bytes.subarray(0,5).toString(),'%PDF-');
            assert.ok(bytes.length>1000);
            assert.equal(context.pages().length,1,'PDF download must not leave a blank tab');
            assert.equal(new URL(page.url()).pathname,'/tour/65');
            console.log(type+': nonempty PDF downloaded, tour page retained, no new tab.');
        }
        console.log('PASS: actual voucher/itinerary PDF menu actions for tour 65 download correctly without blank tabs. No forms submitted or tour records changed.');
    }finally{await browser.close();}
})().catch(error=>{console.error(error);process.exitCode=1;});