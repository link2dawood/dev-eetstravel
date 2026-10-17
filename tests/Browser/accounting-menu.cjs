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
            if (!['/accounting','/reporting','/invoices','/accounting/1','/invoices/2'].includes(url.pathname)) return route.abort();
            return route.continue();
        });
        const page = await context.newPage();
        for(const [endpoint,module] of [['/accounting','accounting'],['/reporting','reporting'],['/invoices','invoices'],['/accounting/1','accounting'],['/invoices/2','invoices']]) {
            const response=await page.goto(base+endpoint,{waitUntil:'domcontentloaded',timeout:60000});
            assert.equal(response.status(),200,endpoint);
            const group=page.locator('aside .nav-item.dropdown').filter({has:page.locator('a[href="#navbar-accounting"]')});
            const active=group.locator('.dropdown-item.active');
            assert.equal(await active.count(),1,endpoint);
            assert.equal(new URL(await active.getAttribute('href'),base).pathname,'/'+module);
            assert.equal(await active.getAttribute('aria-current'),'true');
            assert.equal(await group.locator('a[href="#navbar-accounting"]').getAttribute('aria-expanded'),'true');
            assert.ok((await group.getAttribute('class')).includes('active'));
            await page.setViewportSize({width:1440,height:1000});
            assert.equal(await active.isVisible(),true);
        }
        console.log('PASS: Accounting/Reporting/Invoices lists and invoice detail pages mark exactly one matching Accounting child and expand the active group. No financial records changed.');
    } finally {
        await browser.close();
    }
})().catch(error => { console.error(error); process.exitCode = 1; });
