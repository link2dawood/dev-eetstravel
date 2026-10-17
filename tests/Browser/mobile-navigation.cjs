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
            if (url.pathname !== '/clients/9/edit') return route.abort();
            return route.continue();
        });
        const page = await context.newPage();
        await page.goto(base + '/clients/9/edit', { waitUntil: 'domcontentloaded', timeout: 60000 });
        const header = page.locator('header .navbar-toggler');
        const menu = page.locator('#sidebar-menu');
        async function toggle(control, open) {
            await control.click();
            await page.waitForFunction(expected => {
                const menu = document.querySelector('#sidebar-menu');
                return !menu.classList.contains('collapsing') && menu.classList.contains('show') === expected;
            }, open);
            assert.equal(await header.getAttribute('aria-expanded'), String(open));
            assert.equal(await menu.isVisible(), open);
        }
        assert.equal(await header.getAttribute('aria-controls'), 'sidebar-menu');
        await toggle(header, true);
        await toggle(header, false);
        async function compactBar() {
            assert.equal(await page.locator('.navbar-toggler:visible').count(), 1);
            assert.equal(await page.locator('.navbar-brand:visible').count(), 1);
            const height = await page.locator('aside.navbar').evaluate(el => el.getBoundingClientRect().height);
            assert.equal(height, 0, 'Collapsed sidebar must not leave a second bar');
            assert.ok(await page.locator('header.navbar').evaluate(el => el.getBoundingClientRect().height <= 72), 'Mobile header should fit a compact row');
            await page.locator('[aria-label="Open user menu"]').click();
            assert.equal(await page.locator('.account-menu').isVisible(), true);
            await page.locator('[aria-label="Open user menu"]').click();
        }
        async function headerShortcuts() {
            for (const label of ['Show notifications', 'Show messages']) {
                const trigger = page.locator(`[aria-label="${label}"]`);
                assert.equal(await trigger.isVisible(), true);
                await trigger.click();
                assert.equal(await trigger.getAttribute('aria-expanded'), 'true');
                const dropdown = trigger.locator('..').locator('.dropdown-menu').first();
                assert.equal(await dropdown.isVisible(), true);
                const bounds = await dropdown.boundingBox();
                assert.ok(bounds.x >= 0 && bounds.x + bounds.width <= page.viewportSize().width, `${label} menu must fit viewport: ${JSON.stringify(bounds)}`);
                assert.equal(await dropdown.locator('a').first().isVisible(), true);
                await page.keyboard.press('Escape');
                assert.equal(await trigger.getAttribute('aria-expanded'), 'false');
            }
        }
        await compactBar();
        await headerShortcuts();
        await page.setViewportSize({ width: 320, height: 844 });
        await compactBar();
        await headerShortcuts();
        await toggle(header, true);
        await toggle(header, false);
        await page.setViewportSize({ width: 820, height: 1000 });
        await compactBar();
        await headerShortcuts();
        await toggle(header, true);
        await toggle(header, false);
        await page.setViewportSize({ width: 1440, height: 1000 });
        assert.equal(await header.isVisible(), false);
        assert.equal(await menu.isVisible(), true);
        await headerShortcuts();
        console.log('PASS: compact navigation and notification/message menus at 320/390/820px, keyboard dismissal, and desktop navigation.');
    } finally {
        await browser.close();
    }
})().catch(error => { console.error(error); process.exitCode = 1; });
