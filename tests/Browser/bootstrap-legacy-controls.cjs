const assert = require('node:assert/strict');
const fs = require('node:fs');
const path = require('node:path');
const { chromium } = require(process.env.PLAYWRIGHT_MODULE || 'playwright');

// Supply an authenticated local storageState; this test never submits data.
(async () => {
    const base = 'http://127.0.0.1:8010';
    const browser = await chromium.launch({ headless: true, executablePath: process.env.PLAYWRIGHT_EXECUTABLE_PATH });
    try {
        const context = await browser.newContext({ viewport: { width: 390, height: 844 } });
        await context.route('**/*', route => {
            const request = route.request(), url = new URL(request.url());
            const root = path.resolve('public'), file = path.resolve(root, '.' + decodeURIComponent(url.pathname));
            const types = { '.js': 'application/javascript', '.css': 'text/css', '.woff2': 'font/woff2', '.woff': 'font/woff', '.ttf': 'font/ttf', '.png': 'image/png', '.jpg': 'image/jpeg', '.svg': 'image/svg+xml', '.gif': 'image/gif', '.ico': 'image/x-icon' };
            if (url.origin !== base || !['GET', 'HEAD'].includes(request.method())) return route.abort();
            if (types[path.extname(file)] && file.startsWith(root + path.sep) && fs.existsSync(file)) return route.fulfill({ path: file, contentType: types[path.extname(file)] });
            if (!['/TMS-Client/login', '/TMS-Supplier/login', '/users/1/edit'].includes(url.pathname)) return route.abort();
            return route.continue();
        });
        const page = await context.newPage();
        const errors=[];page.on('pageerror',e=>errors.push(e.message));
        for(const stack of ['tabler/js/tabler.min.js','clientassets/js/bootstrap.bundle.min.js']) {
            await page.goto(base+'/TMS-Client/login',{waitUntil:'networkidle'});
            await page.setContent('<button data-toggle="modal" data-target="#legacy">Open</button><div id="legacy" class="modal" tabindex="-1"><div class="modal-dialog"><div class="modal-content"><a href="close" data-dismiss="modal">Close</a></div></div></div><div class="dropdown"><button data-toggle="dropdown">Menu</button><ul class="dropdown-menu"><li>Item</li></ul></div><div class="alert"><button data-dismiss="alert">Dismiss</button>Notice</div><a data-toggle="collapse" href="#panel">Expand</a><div id="panel" class="collapse">Panel</div><ul class="nav"><li><a class="nav-link active" data-toggle="tab" href="#one">One</a></li><li><a class="nav-link" data-toggle="tab" href="#two">Two</a></li></ul><div class="tab-content"><div id="one" class="tab-pane active">First</div><div id="two" class="tab-pane">Second</div></div>');
            await page.addStyleTag({path:stack.startsWith('tabler')?'public/tabler/css/tabler.min.css':'public/clientassets/css/bootstrap.min.css'});
            await page.addScriptTag({path:'public/js/bootstrap-legacy-attributes.js'});
            await page.addScriptTag({path:'public/'+stack});
            await page.getByRole('button',{name:'Open',exact:true}).click();
            assert.equal(await page.locator('#legacy').isVisible(),true);
            await page.getByText('Close',{exact:true}).click();
            await page.waitForFunction(()=>!document.getElementById('legacy').classList.contains('show'));
            assert.equal(await page.locator('#legacy').isVisible(),false);
            await page.getByRole('button',{name:'Menu',exact:true}).click();
            assert.equal(await page.locator('.dropdown-menu').isVisible(),true);
            await page.getByRole('button',{name:'Menu',exact:true}).click();
            assert.equal(await page.locator('.dropdown-menu').isVisible(),false);
            await page.getByRole('button',{name:'Dismiss',exact:true}).click();
            assert.equal(await page.locator('.alert').count(),0);
            await page.getByText('Expand',{exact:true}).click();
            await page.waitForFunction(()=>document.getElementById('panel').classList.contains('show'));
            await page.getByText('Two',{exact:true}).click();
            assert.ok(await page.locator('#two').evaluate(el=>el.classList.contains('active')));
            await page.evaluate(()=>{
                const host=document.createElement('div');host.innerHTML='<button data-toggle="modal" data-target="#legacy">Dynamic</button><span id="explicit" data-toggle="modal" data-bs-toggle="collapse"></span><span id="tip" data-toggle="tooltip" data-original-title="Calculation"></span>';document.body.append(host);
            });
            await page.getByRole('button',{name:'Dynamic',exact:true}).click();
            assert.equal(await page.locator('#legacy').isVisible(),true);
            await page.getByText('Close',{exact:true}).click();
            assert.equal(await page.locator('#explicit').getAttribute('data-bs-toggle'),'collapse');
            assert.equal(await page.locator('#tip').getAttribute('title'),'Calculation');
        }
        assert.deepEqual(errors,[]);
        console.log('PASS: legacy modal open/close, dropdown, alert, collapse, tabs, dynamically inserted controls and native tooltip title work with Tabler and portal Bootstrap bundles. Explicit Bootstrap 5 attributes preserved. Fixture only; no records changed.');
    } finally {await browser.close();}
})().catch(error=>{console.error(error);process.exitCode=1;});
