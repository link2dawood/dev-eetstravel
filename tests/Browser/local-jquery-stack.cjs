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
            if (!['/TMS-Client/login', '/TMS-Supplier/login', '/users/1/edit'].includes(url.pathname)) return route.abort();
            return route.continue();
        });
        const page = await context.newPage();
        const errors=[];
        page.on('pageerror',error=>errors.push(error.message));
        for (const endpoint of ['/TMS-Client/login','/TMS-Supplier/login','/users/1/edit']) {
            errors.length=0;
            const response=await page.goto(base+endpoint,{waitUntil:'networkidle',timeout:60000});
            assert.equal(response.status(),200,endpoint);
            assert.equal(await page.locator('script[src]').evaluateAll(nodes=>nodes.filter(n=>/\/jquery(?:-[0-9.]+)?\.min\.js/.test(n.src) && !/steps|validate/.test(n.src)).length),1,endpoint);
            assert.equal(await page.evaluate(()=>typeof window.jQuery),'function');
            if(endpoint.includes('/users/')) {
                assert.equal(await page.locator('.select22').evaluate(el=>!!jQuery(el).data('select2')),true);
                await page.locator('.select22 + .select2 .select2-selection').click();
                assert.equal(await page.locator('.select2-dropdown').isVisible(),true);
            } else {
                assert.equal(await page.evaluate(()=>typeof jQuery.fn.steps),'function');
                assert.equal(await page.evaluate(()=>typeof jQuery.fn.validate),'function');
                await page.locator('input[name=contact_email]').fill('qa@example.invalid');
                await page.locator('input[name=password]').fill('unsaved-test');
                assert.equal(await page.getByRole('button',{name:'Login'}).isEnabled(),true);
            }
            assert.deepEqual(errors,[],endpoint+': '+errors.join('; '));
        }
        // Exercise local wizard navigation on a DOM-only fixture; never submit a form.
        await page.goto(base+'/TMS-Client/login',{waitUntil:'networkidle'});
        await page.evaluate(()=>{
            const form=jQuery('<form id="qa-wizard"><h6>First</h6><formstep>One</formstep><h6>Second</h6><formstep>Two</formstep></form>').appendTo('body');
            form.steps({headerTag:'h6',bodyTag:'formstep',transitionEffect:'none'});
            form.steps('next');
            if(form.steps('getCurrentIndex')!==1) throw new Error('Local wizard navigation failed');
            form.remove();
        });
        console.log('PASS: both portal logins and user edit work with external requests blocked; one local jQuery per page, local validation/wizard navigation and user permissions dropdown. No forms submitted.');
    } finally { await browser.close(); }
})().catch(error=>{console.error(error);process.exitCode=1;});
