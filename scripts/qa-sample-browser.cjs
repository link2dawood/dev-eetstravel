const assert = require('node:assert/strict');
const fs = require('node:fs');
const { chromium } = require(process.env.PLAYWRIGHT_MODULE || 'playwright');
const directory = 'storage/app/sample-data/section-checks';
const base = 'http://127.0.0.1:8010';

(async () => {
    const browser = await chromium.launch({ headless: true, executablePath: process.env.PLAYWRIGHT_EXECUTABLE_PATH });
    const context = await browser.newContext({ storageState: process.env.QA_STORAGE_STATE || `${directory}/browser-state.json` });
    // Read-only UI checks: block external services and requests that can change records.
    await context.route('**/*', route => {
        const request = route.request();
        return new URL(request.url()).origin === base && ['GET', 'HEAD'].includes(request.method())
            ? route.continue() : route.abort();
    });
    const page = await context.newPage();
    const downloadsOnly = process.argv[2] === 'downloads';
    const results = downloadsOnly
        ? JSON.parse(fs.readFileSync(`${directory}/browser-results.json`)).filter(result => !result.path)
        : [];
    let errors = [];
    page.on('pageerror', error => errors.push(error.message));
    try {
        const cases = [
            ['Bus Company', '/transfer?search=QA%20Sample', 'QA Sample Prague Coaches'],
            ['Base Input', '/status?search=QA%20Sample', 'QA Sample Awaiting Deposit'],
            ['Communications', '/comment?search=QA%20Sample', 'QA Sample note'],
            ['Clients', '/clients?search=QA%20Sample', 'QA Sample Danube Tours'],
            ['Accounting', '/accounting', 'QA-SAMPLE-CLIENT-'],
        ];
        for (const [section, path, expected] of downloadsOnly ? [] : cases) {
            errors = [];
            const response = await page.goto(base + path, { waitUntil: 'networkidle' });
            assert.equal(response.status(), 200, section);
            assert((await page.locator('body').innerText()).includes(expected), `${section}: sample visible`);
            if (section === 'Accounting') {
                await page.locator('#accounting-search').fill('QA-SAMPLE-CLIENT-');
                await page.locator('#accounting-search').press('End');
                await page.waitForFunction(() => [...document.querySelectorAll('#transactions-table tbody tr')]
                    .filter(row => row.getClientRects().length > 0).length === 3);
                const count = await page.locator('#transactions-table tbody tr:visible').count();
                assert.equal(count, 3, 'Accounting search shows all three sample invoices');
            }
            results.push({ section, status: 200, sampleVisible: true, javascriptErrors: [...errors] });
            console.log(`PASS: ${section} sample visible in browser; JavaScript errors: ${errors.length}`);
        }
        if (!downloadsOnly) {
            errors = [];
            assert.equal((await page.goto(base + '/bus/calendar', { waitUntil: 'networkidle' })).status(), 200);
            await page.waitForFunction(() => typeof chart2 !== 'undefined' && chart2.dataProvider.length > 0);
            results.push({ section: 'Bus Calendar', chartLoaded: true, javascriptErrors: [...errors] });
            console.log('PASS: Bus Calendar chart loads with data');
        }
        const invoiceId = JSON.parse(fs.readFileSync('storage/app/sample-data/accounting.json')).client_invoices.UNPAID;
        for (const [path, signature] of [
            [`/accounting/${invoiceId}/export/short`, '%PDF'], [`/accounting/${invoiceId}/excel`, 'PK'],
        ]) {
            const response = await context.request.get(base + path);
            assert.equal(response.status(), 200, path);
            assert((await response.body()).subarray(0, signature.length).toString() === signature, path);
            results.push({ path, validDownload: true });
            console.log(`PASS: ${path} downloads a valid file`);
        }
    } finally {
        fs.writeFileSync(`${directory}/browser-results.json`, JSON.stringify(results, null, 2));
        await browser.close();
    }
})().catch(error => { console.error(error); process.exitCode = 1; });
