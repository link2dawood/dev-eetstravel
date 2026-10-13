const assert = require('assert');
const { chromium } = require(process.env.PLAYWRIGHT_MODULE || 'playwright');
// Run against the local app with Google/Pusher unconfigured.
// BUS_TEST_STORAGE_STATE points to a Playwright session saved after sign-in.
// Provider responses are simulated; this test sends no notifications or emails.
(async () => {
    assert(process.env.BUS_TEST_STORAGE_STATE, 'Set BUS_TEST_STORAGE_STATE to an authenticated Playwright storage-state file.');
    const browser = await chromium.launch();
    const context = await browser.newContext({ storageState: process.env.BUS_TEST_STORAGE_STATE });
    const external = [], errors = [];
    await context.route('**/*', route => {
        const host = new URL(route.request().url()).hostname;
        if (host !== '127.0.0.1') { external.push(host); return route.abort(); }
        return route.continue();
    });
    const page = await context.newPage();
    page.on('pageerror', error => errors.push(error.message));
    assert.equal((await page.goto('http://127.0.0.1:8010/bus/calendar')).status(), 200);
    await page.waitForFunction(() => chart2.dataProvider.length > 0, {}, { timeout: 45000 });
    assert.deepEqual(await page.evaluate(() => ({
        mapsScript: !!document.querySelector('script[src*="maps.googleapis.com"]'),
        pusherEnabled: appPusherConfig.enabled, pusherStarted: !!notificationEmail.pusher
    })), { mapsScript: false, pusherEnabled: false, pusherStarted: false });
    assert(!external.some(host => /googleapis|pusher/.test(host)));
    await page.getByRole('button', { name: 'Add trip', exact: true }).click();
    await page.locator('#add_day [name=city_begin]').fill('Vienna');
    assert.equal(await page.locator('#add_day [name=city_begin]').inputValue(), 'Vienna');
    assert.deepEqual(errors, []);
    console.log('PASS: unconfigured services make no connections; calendar and manual city entry work.');
    const wiring = await page.evaluate(() => {
        const calls = [];
        window.Pusher = function (key, options) {
            calls.push({ key, options });
            this.subscribe = function (channel) {
                calls.push({ channel });
                return { bind: function (event) { calls.push({ event }); } };
            };
        };
        window.appPusherConfig = { enabled: true, key: 'test-public-key', cluster: 'ap2' };
        notificationEmail.init(); notificationEmail.init();
        return calls;
    });
    assert.deepEqual(wiring, [
        { key: 'test-public-key', options: { cluster: 'ap2', encrypted: true } },
        { channel: 'notification' }, { event: 'new-emails' }
    ]);
    const emailCalls = await page.evaluate(() => {
        let count = 0;
        const ajax = $.ajax, toast = $.toast;
        $.ajax = () => { count++; return {}; }; $.toast = () => {};
        notificationEmail.newNotification({ user: user_email, server: location.host, newEmailCount: 1 });
        notificationEmail.newNotification({ user: 'different-user', server: location.host, newEmailCount: 1 });
        $.ajax = ajax; $.toast = toast;
        return count;
    });
    assert.equal(emailCalls, 1);
    console.log('PASS: simulated Pusher uses configured key/cluster, subscribes once, and filters user/host with port.');
    const places = await page.evaluate(() => {
        window.auditPlaces = [];
        window.google = { maps: {
            places: { Autocomplete: function (input, options) {
                this.input = input; this.listeners = {};
                this.setComponentRestrictions = restrictions => { this.restrictions = restrictions; };
                this.addListener = (event, callback) => { this.listeners[event] = callback; };
                this.getPlace = () => ({ place_id: 'test-vienna', name: 'Vienna', address_components: [{ long_name: 'Vienna', types: ['locality'] }] });
                auditPlaces.push(this);
            } }, event: { addDomListener: (input, event, callback) => input.addEventListener(event, callback) }
        } };
        const country = $('#add_day [name=country_begin]');
        country.val('AT'); initField(); initField();
        const autocomplete = document.querySelector('#add_day [name=city_begin]')._placesAutocomplete;
        autocomplete.listeners.place_changed();
        const selected = { city: autocomplete.input.value, code: $('#add_day [name=city_begin_code]').val(), restrictions: autocomplete.restrictions, count: auditPlaces.length };
        country.val('DE').trigger('select2:select');
        return { selected, cleared: autocomplete.input.value === '' && $('#add_day [name=city_begin_code]').val() === '', country: autocomplete.restrictions.country };
    });
    assert.equal(places.selected.city, 'Vienna');
    assert.equal(places.selected.code, 'test-vienna');
    assert.equal(places.selected.count, 1);
    assert.deepEqual(places.selected.restrictions.country, ['at']);
    assert(places.cleared); assert.deepEqual(places.country, ['de']);
    console.log('PASS: simulated Places initializes once, stores city/place ID, and resets on country changes.');
    await browser.close();
})().catch(error => { console.error(error.message); process.exit(1); });
