const assert = require('node:assert/strict');
const fs = require('node:fs');
const vm = require('node:vm');

// Execute the actual legacy modal handlers and inspect the request they send.
const handlers = new Map();
const requests = [];
const document = { querySelector: () => ({ content: 'test-csrf-token' }) };
function $(target) {
    return {
        on(event, selector, handler) { handlers.set(selector, handler); return this; },
        data(name) { return target[name]; },
    };
}
$.ajax = request => requests.push(request);
vm.runInNewContext(fs.readFileSync('public/js/AjaxisBootstrap.js', 'utf8'), {
    $, document, baseURL: '', console, window: {},
});
handlers.get('.destroy').call({ link: '/hotel/1/delete' });
assert.equal(requests[0].type, 'post');
assert.equal(requests[0].url, '/hotel/1/delete');
assert.equal(requests[0].headers['X-CSRF-TOKEN'], 'test-csrf-token');
handlers.get('.delete').call({ link: '/hotel/1/deleteMsg' });
assert.equal(requests[1].type, 'get');
assert.equal(requests[1].url, '/hotel/1/deleteMsg');
console.log('Legacy delete confirmation loads with GET; deletion submits POST with CSRF.');
