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
            if (!['/task/create', '/task/133/edit'].includes(url.pathname)) return route.abort();
            return route.continue();
        });
        const page = await context.newPage();
        const errors = [];
        page.on('pageerror', error => errors.push(error.message));
        for (const endpoint of ['/task/create', '/task/133/edit']) {
            await page.goto(base + endpoint, {waitUntil:'domcontentloaded', timeout:60000});
            const input = page.locator('#end_time');
            assert.equal(await input.getAttribute('type'), 'time');
            assert.equal(await input.evaluate(el => el.checkValidity()), true);
            if (endpoint.endsWith('create')) assert.equal(await input.inputValue(), '18:00');
            else assert.match(await input.inputValue(), /^\d{2}:\d{2}(:\d{2})?$/);
            for (const value of ['00:00', '23:59', '12:34:56']) {
                await input.fill(value);
                assert.equal(await input.inputValue(), value);
                assert.equal(await input.evaluate(el => el.checkValidity()), true);
                assert.equal(await page.locator('#task-form').evaluate(el => new FormData(el).get('end_time')), value);
            }
            await input.fill('');
            assert.equal(await input.evaluate(el => el.validity.valueMissing), true);
            await input.evaluate(el => { el.value = '25:99'; });
            assert.equal(await input.inputValue(), '');
            await input.fill('18:00');
            // Verify the assignment guard now registers after time setup, without submitting.
            await page.locator('.user_checkboxes').evaluateAll(elements => elements.forEach(el => el.checked = false));
            const dialog = page.waitForEvent('dialog');
            const dispatch = page.locator('#task-form').evaluate(form => form.dispatchEvent(new Event('submit', {bubbles:true, cancelable:true})));
            const alert = await dialog;
            assert.match(alert.message(), /assign at least one user/i);
            await alert.accept();
            assert.equal(await dispatch, false);
            assert.equal(errors.length, 0, errors.join('\n'));
        }
        console.log('PASS: create/edit native time fields, preserved initial values, midnight/end-of-day/seconds, required and invalid values, serialized times and assignment guard. No forms submitted.');
    } finally {
        await browser.close();
    }
})().catch(error => { console.error(error); process.exitCode = 1; });
