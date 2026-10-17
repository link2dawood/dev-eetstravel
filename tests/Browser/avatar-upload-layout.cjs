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
            if (!['/profile/edit', '/users/create', '/users/1/edit'].includes(url.pathname)) return route.abort();
            return route.continue();
        });
        const page = await context.newPage();
        for(const endpoint of ['/profile/edit','/users/create','/users/1/edit']) {
            const response=await page.goto(base+endpoint,{waitUntil:'domcontentloaded',timeout:60000});
            assert.equal(response.status(),200);
            await page.locator('.avatar-upload-field .file-input').waitFor();
            async function fits() {
                const issues=await page.locator('.avatar-upload-field').evaluate(root=>{
                    const container=root.getBoundingClientRect();
                    return [...root.querySelectorAll('.file-caption-main,.file-caption,.file-caption-name,.input-group-btn,.btn-file,.fileinput-remove-button')]
                        .filter(el=>el.getClientRects().length)
                        .map(el=>({class:el.className,rect:el.getBoundingClientRect()}))
                        .filter(({rect})=>rect.left<container.left-1 || rect.right>container.right+1)
                        .map(({class:cls,rect})=>({class:cls,left:rect.left,right:rect.right,containerLeft:container.left,containerRight:container.right}));
                });
                assert.deepEqual(issues,[],endpoint+' at '+page.viewportSize().width);
            }
            for(const width of [320,390,768,1440]) {
                await page.setViewportSize({width,height:1000});
                await fits();
            }
            await page.locator('#avatar').setInputFiles({name:'long-avatar-filename-'.repeat(8)+'.png',mimeType:'image/png',buffer:Buffer.from('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMCAO+jRZkAAAAASUVORK5CYII=','base64')});
            for(const width of [320,390,1440]) {
                await page.setViewportSize({width,height:1000});
                await fits();
            }
            await page.locator('.avatar-upload-field .fileinput-remove-button').click();
            assert.equal(await page.locator('#avatar').evaluate(el=>el.files.length),0);
        }
        console.log('PASS: avatar caption/actions fit their container on all three forms at 320/390/768/1440px, including a long local filename; Remove clears only the unsaved file. No uploads or form submissions.');
    } finally {
        await browser.close();
    }
})().catch(error => { console.error(error); process.exitCode = 1; });
