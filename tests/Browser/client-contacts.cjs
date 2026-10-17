const assert = require('node:assert/strict');
const fs = require('node:fs');
const { chromium } = require(process.env.PLAYWRIGHT_MODULE || 'playwright');
const directory = 'storage/app/sample-data/section-checks';

(async () => {
    const browser = await chromium.launch({ headless: true, executablePath: process.env.PLAYWRIGHT_EXECUTABLE_PATH });
    const context = await browser.newContext({ storageState: process.env.QA_STORAGE_STATE || `${directory}/browser-state.json` });
    await context.route('**/*', route => {
        const request = route.request();
        return new URL(request.url()).origin === 'http://127.0.0.1:8010' && ['GET', 'HEAD'].includes(request.method())
            ? route.continue() : route.abort();
    });
    const page = await context.newPage();
    const errors = [];
    page.on('pageerror', error => errors.push(error.message));
    try {
        assert.equal((await page.goto('http://127.0.0.1:8010/clients/9/edit', { waitUntil: 'domcontentloaded' })).status(), 200);
        await page.waitForFunction(() => !document.querySelector('#add_contact').disabled);
        const rows = page.locator('#items-contacts .item-contact');
        const before = await rows.count();
        await page.locator('#add_contact').click();
        await page.locator('#add_contact').click();
        await page.waitForFunction(count => document.querySelectorAll('#items-contacts .item-contact').length === count, before + 2);
        const names = await rows.locator('input[name$="[contact_full_name]"]').evaluateAll(inputs => inputs.map(input => input.name));
        assert.equal(new Set(names).size, before + 2, 'Every contact has a unique input index');
        const indices = names.map(name => name.match(/contacts\[(\d+)\]/)[1]);
        assert(indices.every(index => String(Number(index)) === index), 'Contact indices are numeric without concatenated leading zeros');
        await rows.nth(before).locator('input[name$="[contact_full_name]"]').fill('Read-only QA Contact One');
        await rows.nth(before + 1).locator('input[name$="[contact_full_name]"]').fill('Read-only QA Contact Two');
        const form = await page.locator('.client-form').evaluate(form => {
            const data = new FormData(form);
            // Match the application's save-without-files path.
            data.delete('attach[]');
            return new URLSearchParams(data).toString();
        });
        fs.writeFileSync(`${directory}/client-contact-form.txt`, form);
        assert.deepEqual(errors, []);
        console.log('PASS: client 9 edit loads; two contacts append with unique numeric indices; no JavaScript errors.');
        console.log('Form captured for read-only server validation; nothing saved to client 9.');

        let failContactLoad = true;
        await context.route('**/api/getClientContacts?**', route => failContactLoad
            ? route.fulfill({ status: 503, body: 'Simulated unavailable service' }) : route.continue());
        await page.goto('http://127.0.0.1:8010/clients/9/edit', { waitUntil: 'domcontentloaded' });
        await page.locator('#retry_contacts').waitFor({ state: 'visible' });
        assert(await page.locator('#add_contact').isDisabled());
        assert.equal(await page.locator('#contacts_loaded').inputValue(), '0');
        failContactLoad = false;
        await page.locator('#retry_contacts').click();
        await page.waitForFunction(() => !document.querySelector('#add_contact').disabled);
        assert.equal(await page.locator('#contacts_loaded').inputValue(), '1');
        console.log('PASS: failed contact-list load shows an error; retry restores rows and enables Add Contact.');

        let failNewRow = true;
        await context.route('**/api/getItemContactView?**', route => failNewRow
            ? route.fulfill({ status: 503, body: 'Simulated unavailable service' }) : route.continue());
        const existing = await rows.count();
        await page.locator('#add_contact').click();
        await page.locator('#contacts-error').waitFor({ state: 'visible' });
        assert.equal(await rows.count(), existing);
        failNewRow = false;
        await page.locator('#add_contact').click();
        await page.waitForFunction(count => document.querySelectorAll('#items-contacts .item-contact').length === count, existing + 1);
        const newName = rows.last().locator('input[name$="[contact_full_name]"]');
        await newName.fill('<img src=x onerror="window.qaInjected=true">');
        assert.equal(await page.evaluate(() => window.qaInjected), undefined);
        assert.equal(await page.locator('#items-contacts img').count(), 0);
        while (await rows.count()) await rows.first().locator('#delete_contact_item').click();
        assert.equal(await page.locator('#contacts_loaded').inputValue(), '1');
        assert.deepEqual(errors, []);
        console.log('PASS: failed Add Contact recovers; HTML input stays text; deleting all rows retains the explicit loaded marker.');

        await page.locator('#add_contact').click();
        await page.waitForFunction(() => document.querySelectorAll('#items-contacts .item-contact').length === 1);
        const invalidEmail = rows.first().locator('input[name$="[contact_email]"]');
        await invalidEmail.fill('bad@@email');
        const nameField = await rows.first().locator('input[name$="[contact_full_name]"]').getAttribute('name');
        const index = nameField.match(/contacts\[(\d+)\]/)[1];
        let interceptedSaves = 0;
        await context.route('http://127.0.0.1:8010/clients/9', async route => {
            if (!['POST', 'PUT'].includes(route.request().method())) return route.continue();
            interceptedSaves++;
            await route.fulfill({ status: 422, contentType: 'application/json', body: JSON.stringify({
                message: 'The given data was invalid.', errors: {
                    [`contacts.${index}.contact_full_name`]: ['Enter a full name for every contact, or remove the empty row.'],
                    [`contacts.${index}.contact_email`]: ['Enter a valid email address for each contact.'],
                },
            }) });
        });
        await page.locator('.client-form button[type=submit]').click();
        await rows.first().locator('.validation-error').first().waitFor({ state: 'visible' });
        assert.equal(await rows.first().locator('.validation-error').count(), 2);
        assert.equal(await invalidEmail.inputValue(), 'bad@@email');
        assert(await page.locator('.client-form button[type=submit]').isEnabled());
        assert.equal(interceptedSaves, 1);
        assert(page.url().endsWith('/clients/9/edit'));
        assert.deepEqual(errors, []);
        console.log('PASS: simulated 422 highlights the exact contact fields, keeps typed data, and enables retry; no live save occurred.');
    } finally {
        await browser.close();
    }
})().catch(error => { console.error(error); process.exitCode = 1; });
