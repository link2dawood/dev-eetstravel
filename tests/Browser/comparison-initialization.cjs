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
            const previews={'/tour/3':'storage/app/ui-audit/ui41-preview-0.html','/comparison/2':'storage/app/ui-audit/ui41-preview-1.html'};
            if(previews[url.pathname])return route.fulfill({path:previews[url.pathname],contentType:'text/html'});
            return route.abort();
        });
        for(const index of [0,1]) {
            const denied=fs.readFileSync('storage/app/ui-audit/ui41-denied-'+index+'.html','utf8');
            assert.ok(denied.includes('A user with permission to update comparisons'));
            assert.ok(!denied.includes('name="initialize_front_sheet"'));
        }
        const page = await context.newPage();
        for(const endpoint of ['/tour/3','/comparison/2']) {
            for(const width of [390,1440]) {
                await page.setViewportSize({width,height:1000});
                await page.goto(base+endpoint,{waitUntil:'networkidle'});
                const button=page.getByRole('button',{name:'Initialize front sheet',exact:true});
                assert.equal(await button.isVisible(),true);
                const form=button.locator('..');
                assert.equal(await form.getAttribute('method'),'POST');
                assert.equal(await form.locator('input[name=_method]').inputValue(),'PUT');
                assert.ok(await form.locator('input[name=_token]').inputValue());
                assert.equal(await form.locator('input[name=initialize_front_sheet]').inputValue(),'1');
                const rect=await button.boundingBox();assert.ok(rect.x>=0 && rect.x+rect.width<=width);
            }
        }
        console.log('PASS: read-only tour/comparison previews show explicit initialization at phone/desktop widths with POST, PUT override and CSRF token. No submit action invoked.');
    }finally{await browser.close();}
})().catch(e=>{console.error(e);process.exitCode=1;});