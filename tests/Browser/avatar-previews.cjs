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
            if (!['/profile', '/profile/edit', '/users/1/edit'].includes(url.pathname)) return route.abort();
            return route.continue();
        });
        const page = await context.newPage();
        for (const width of [390,1440]) {
            await page.setViewportSize({width,height:1000});
            for (const endpoint of ['/profile','/profile/edit','/users/1/edit']) {
                const response = await page.goto(base+endpoint,{waitUntil:'domcontentloaded',timeout:60000});
                assert.equal(response.status(),200,endpoint);
                const preview = page.locator('.avatar-preview');
                await preview.waitFor({state:'visible'});
                assert.ok((await preview.locator('.avatar-initials').innerText()).trim());
                assert.ok(await preview.getAttribute('aria-label'));
                await page.waitForFunction(() => [...document.querySelectorAll('.avatar-preview img')].every(img => img.complete && img.naturalWidth > 0));
                const bounds = await preview.boundingBox();
                assert.ok(bounds.width > 0 && bounds.x >= 0 && bounds.x + bounds.width <= width);
            }
        }
        console.log('PASS: profile, profile edit and user edit show usable avatar/initial previews on phone/desktop without broken preview images. No files uploaded or records changed.');
    } finally {
        await browser.close();
    }
})().catch(error => { console.error(error); process.exitCode = 1; });
