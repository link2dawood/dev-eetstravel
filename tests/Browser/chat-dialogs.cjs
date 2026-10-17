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
            if (!['/chat/main', '/chat/renderUsersForChat', '/chat/renderCustomChatCreateFrom'].includes(url.pathname)) return route.abort();
            return route.continue();
        });
        const page = await context.newPage();
        await page.goto(base + '/chat/main', { waitUntil: 'domcontentloaded', timeout: 60000 });
        const modal = page.locator('#myModal');
        async function closed() {
            await page.waitForFunction(() => !document.querySelector('#myModal').classList.contains('show') && !document.querySelector('.modal-backdrop'));
        }
        for (const width of [1440, 390]) {
            await page.setViewportSize({width, height: 900});
            for (const endpoint of ['renderUsersForChat', 'renderCustomChatCreateFrom']) {
                await page.locator(`button[data-link="/chat/${endpoint}"]`).click();
                await page.waitForFunction(() => {
                    const el = document.querySelector('#myModal');
                    return el.classList.contains('show') && !el.innerText.includes('Loading...');
                });
                assert.equal(await modal.isVisible(), true);
                assert.equal(await modal.locator('.modal-dialog').count(), 1);
                assert.equal(await modal.locator('.modal-content').count(), 1);
                if (endpoint === 'renderUsersForChat') assert.ok(await modal.locator('.chat-user-select').count() > 0);
                else assert.equal(await modal.locator('#custom_chat_name').isVisible(), true);
                const bounds = await modal.locator('.modal-dialog').boundingBox();
                assert.ok(bounds.x >= 0 && bounds.x + bounds.width <= width);
                await page.waitForTimeout(400);
                await modal.locator('[aria-label="Close"]').click();
                await closed();
            }
        }
        await context.route('**/chat/renderCustomChatCreateFrom', route => route.fulfill({status:500, body:'Simulated failure'}));
        await page.locator('button[data-link="/chat/renderCustomChatCreateFrom"]').click();
        await page.waitForFunction(() => document.querySelector('#myModal [role="alert"]')?.textContent.includes('Unable to load'));
        await page.waitForFunction(() => !jQuery.fn.modal.Constructor.getInstance(document.querySelector('#myModal'))._isTransitioning);
        await page.keyboard.press('Escape');
        await closed();
        await context.unroute('**/chat/renderCustomChatCreateFrom');
        await page.locator('button[data-link="/chat/renderCustomChatCreateFrom"]').click();
        await modal.locator('#custom_chat_name').waitFor({state:'visible'});
        await page.waitForFunction(() => !jQuery.fn.modal.Constructor.getInstance(document.querySelector('#myModal'))._isTransitioning);
        await page.keyboard.press('Escape');
        await closed();
        console.log('PASS: both chat dialogs on desktop/phone, single dialog structure, close/Escape, loading failure and retry; no chats created.');
    } finally {
        await browser.close();
    }
})().catch(error => { console.error(error); process.exitCode = 1; });
