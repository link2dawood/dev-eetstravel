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
            if (!['/', '/home'].includes(url.pathname)) return route.abort();
            return route.continue();
        });
        const page = await context.newPage();
        await page.goto(base+'/',{waitUntil:'networkidle'});
        for(const width of [390,1440]) {
            await page.setViewportSize({width,height:1000});
            for(const closeSelector of ['.modal-header [data-dismiss="modal"]','.modal-footer [data-dismiss="modal"]']) {
                await page.evaluate(()=>jQuery('#modalCreateTour').modal('show'));
                await page.waitForFunction(()=>!jQuery.fn.modal.Constructor.getInstance(document.getElementById('modalCreateTour'))._isTransitioning);
                assert.equal(await page.locator('#modalCreateTour').isVisible(),true);
                await page.locator('#modalCreateTour '+closeSelector).click();
                await page.waitForFunction(()=>!document.getElementById('modalCreateTour').classList.contains('show'));
                assert.equal(await page.locator('#modalCreateTour').isVisible(),false);
                assert.equal(new URL(page.url()).pathname,'/');
            }
        }
        console.log('PASS: dashboard add-tour modal header/footer close controls at phone/desktop; no navigation or form submissions.');
    }finally{await browser.close();}
})().catch(e=>{console.error(e);process.exitCode=1;});