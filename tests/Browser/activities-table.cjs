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
            if (url.pathname !== '/activities') return route.abort();
            return route.continue();
        });
        const page = await context.newPage();
        page.on('console', message => { if (message.text().startsWith('Activities export failed:')) console.log(message.text()); });
        await page.goto(base + '/activities', { waitUntil: 'domcontentloaded', timeout: 60000 });
        await page.waitForFunction(() => document.querySelector('#activity-table')?.dataset.serverPaginated === 'true');
        const rawDescriptions = await page.locator('#activity-table [data-delete-label]').evaluateAll(cells => cells.filter(cell => /<\/?(?:p|div|br|strong)\b/i.test(cell.textContent)).length);
        assert.equal(rawDescriptions, 0, 'Activity descriptions must not show rich-text markup');
        assert.equal(await page.locator('#activity-table [data-delete-label] script, #activity-table [data-delete-label] img').count(), 0);
        assert.equal(await page.locator('#activity-table tbody tr').count() <= 50, true);
        assert.equal(await page.locator('#activity-table tfoot input').count(), 0);
        assert.equal(await page.locator('.pagination-container').count(), 0);
        const search = page.locator('#activity-search');
        await search.fill('QA-no-such-activity-79f927');
        await Promise.all([
            page.waitForNavigation({waitUntil: 'domcontentloaded'}),
            page.locator('form button[type="submit"]').click()
        ]);
        assert.equal(await page.locator('#activity-table .table-empty').isVisible(), true);
        assert.ok(new URL(page.url()).searchParams.get('q').includes('QA-no-such'));
        // Export fixtures exist only in the browser; no activity rows are written.
        await page.evaluate(() => {
            const table = document.querySelector('#activity-table');
            table.tBodies[0].replaceChildren();
            for (let i = 0; i < 10; i++) {
                const row = table.tBodies[0].insertRow();
                ['2026-10-31 12:00:00', 'QA Alice', `Entry ${i}, "café" <b>text</b>`, 'View'].forEach(text => row.insertCell().textContent = text);
            }
        });
        for (const type of ['csv', 'xlsx', 'pdf']) {
            console.log('Checking export:', type);
            const pending = page.waitForEvent('download');
            pending.catch(() => {});
            await page.locator(`[data-activity-export="${type}"]`).click();
            assert.equal(await page.locator('#activity-export-error').isVisible(), false, await page.locator('#activity-export-error').textContent());
            const download = await pending;
            const stream = await download.createReadStream(); const chunks = [];
            for await (const chunk of stream) chunks.push(chunk);
            const bytes = Buffer.concat(chunks);
            assert.ok(bytes.length > 100);
            if (type === 'csv') {
                const csv = bytes.toString('utf8');
                assert.equal(csv.split('\r\n').length, 11, 'Export current page only');
                assert.ok(csv.includes('""café""'));
                assert.ok(!csv.includes('QA Bob') && !csv.includes('"View"'));
            } else if (type === 'xlsx') {
                const values = await page.evaluate(data => {
                    const book = XLSX.read(data, {type:'base64'});
                    return XLSX.utils.sheet_to_json(book.Sheets.Activities, {header:1});
                }, bytes.toString('base64'));
                assert.equal(values.length, 11); assert.equal(values[0].length, 3);
                assert.ok(values.slice(1).every(row => row[1] === 'QA Alice'));
            } else assert.equal(bytes.subarray(0,4).toString(), '%PDF');
        }
        assert.equal(await page.locator('#activity-export-error').isVisible(), false);
        console.log('PASS: Activities server pagination, empty search, CSV/Excel/PDF page downloads; fixtures existed only in browser memory.');
    } finally {
        await browser.close();
    }
})().catch(error => { console.error(error); process.exitCode = 1; });
