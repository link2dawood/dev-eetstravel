const assert = require('node:assert/strict');
const fs = require('node:fs');
const path = require('node:path');
const { chromium } = require(process.env.PLAYWRIGHT_MODULE || 'playwright');

// Supply an authenticated local storageState; this test never submits data.
(async () => {
    const base = 'http://127.0.0.1:8010';
    const browser = await chromium.launch({ headless: true, executablePath: process.env.PLAYWRIGHT_EXECUTABLE_PATH });
    try {
        const context = await browser.newContext({ viewport: { width: 390, height: 844 } });
        await context.route('**/*', route => {
            const request = route.request(), url = new URL(request.url());
            const root = path.resolve('public'), file = path.resolve(root, '.' + decodeURIComponent(url.pathname));
            const types = { '.js': 'application/javascript', '.css': 'text/css', '.woff2': 'font/woff2', '.woff': 'font/woff', '.ttf': 'font/ttf', '.png': 'image/png', '.jpg': 'image/jpeg', '.svg': 'image/svg+xml', '.gif': 'image/gif', '.ico': 'image/x-icon' };
            if (url.origin !== base || !['GET', 'HEAD'].includes(request.method())) return route.abort();
            if (types[path.extname(file)] && file.startsWith(root + path.sep) && fs.existsSync(file)) return route.fulfill({ path: file, contentType: types[path.extname(file)] });
            if (!['/TMS-Client/login', '/TMS-Supplier/login', '/users/1/edit'].includes(url.pathname)) return route.abort();
            return route.continue();
        });
        const page = await context.newPage();
        for(const endpoint of ['/TMS-Client/login','/TMS-Supplier/login']) {
            for(const width of [390,1440]) {
                await page.setViewportSize({width,height:1000});
                const response=await page.goto(base+endpoint,{waitUntil:'networkidle'});
                assert.equal(response.status(),200);
                const logo=page.getByRole('img',{name:'EETS Travel',exact:true});
                assert.equal(await logo.isVisible(),true);
                assert.equal(await logo.evaluate(el=>el.complete && el.naturalWidth>0),true);
                assert.equal(new URL(await logo.getAttribute('src')).pathname,'/img/eets_logo.png');
                const rect=await logo.boundingBox();
                assert.ok(rect.x>=0 && rect.x+rect.width<=width);
                assert.equal(await page.getByRole('button',{name:'Login'}).isVisible(),true);
            }
        }
        console.log('PASS: both portal logos load from local assets with external requests blocked at 390/1440px; accessible name and viewport bounds verified. No forms submitted.');
    } finally {await browser.close();}
})().catch(error=>{console.error(error);process.exitCode=1;});
