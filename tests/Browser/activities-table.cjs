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
        await page.waitForFunction(() => document.querySelector('#activity-table')._tablePagination?.columnQueries);
        const rawDescriptions = await page.locator('#activity-table [data-delete-label]').evaluateAll(cells => cells.filter(cell => /<\/?(?:p|div|br|strong)\b/i.test(cell.textContent)).length);
        assert.equal(rawDescriptions, 0, 'Activity descriptions must not show rich-text markup');
        assert.equal(await page.locator('#activity-table [data-delete-label] script, #activity-table [data-delete-label] img').count(), 0);
        const search = page.locator('#activity-search');
        await search.fill('QA-no-such-activity-79f927');
        assert.equal(await page.locator('.table-search-empty').isVisible(), true);
        await search.fill('');
        // Browser-only rows exercise edge cases without creating activity records.
        await page.evaluate(() => {
            const table = document.querySelector('#activity-table');
            table.tBodies[0].replaceChildren();
            for (let i = 0; i < 63; i++) {
                const row = table.tBodies[0].insertRow();
                ['2026-10-17 12:00:00', i < 60 ? 'QA Alice' : 'QA Bob', `Entry ${String(i).padStart(2, '0')}, "café" <b>text</b>`, 'View'].forEach(text => row.insertCell().textContent = text);
            }
            const state = table._tablePagination; state.page = 1; renderTablePage(table);
        });
        const visible = () => page.locator('#activity-table tbody tr:visible').count();
        assert.equal(await visible(), 50);
        await page.locator('.pagination-container button', {hasText:'Next'}).click();
        assert.equal(await visible(), 13);
        await page.locator('#activity-page-size').selectOption('10');
        assert.equal(await visible(), 10);
        const filters = page.locator('#activity-table tfoot input');
        await filters.nth(1).fill('QA Bob');
        assert.equal(await visible(), 3);
        await search.fill('Entry 62');
        assert.equal(await visible(), 1);
        await filters.nth(2).fill('no match');
        assert.equal(await page.locator('.table-search-empty').isVisible(), true);
        await filters.nth(2).fill(''); await search.fill('');
        await filters.nth(1).fill('QA Alice');
        await page.locator('#activity-table thead th').nth(2).click();
        assert.ok((await page.locator('#activity-table tbody tr:visible').first().innerText()).includes('Entry 00'));
        await page.locator('#activity-table thead th').nth(2).click();
        assert.ok((await page.locator('#activity-table tbody tr:visible').first().innerText()).includes('Entry 59'));
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
                assert.equal(csv.split('\r\n').length, 61, 'Export every matching row across pages');
                assert.ok(csv.includes('""café""'));
                assert.ok(!csv.includes('QA Bob') && !csv.includes('"View"'));
            } else if (type === 'xlsx') {
                const values = await page.evaluate(data => {
                    const book = XLSX.read(data, {type:'base64'});
                    return XLSX.utils.sheet_to_json(book.Sheets.Activities, {header:1});
                }, bytes.toString('base64'));
                assert.equal(values.length, 61); assert.equal(values[0].length, 3);
                assert.ok(values.slice(1).every(row => row[1] === 'QA Alice'));
            } else assert.equal(bytes.subarray(0,4).toString(), '%PDF');
        }
        assert.equal(await page.locator('#activity-export-error').isVisible(), false);
        console.log('PASS: Activities initialization, empty/global/combined-column filters, pagination, sorting, CSV/Excel/PDF downloads and cross-page export; fixtures existed only in browser memory.');
    } finally {
        await browser.close();
    }
})().catch(error => { console.error(error); process.exitCode = 1; });
