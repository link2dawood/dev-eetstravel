const fs = require('node:fs');
const path = require('node:path');
const { chromium } = require(process.env.PLAYWRIGHT_MODULE || 'playwright');
const base = 'http://127.0.0.1:8010';
const directory = 'storage/app/ui-audit';
const preview = process.env.UI_AUDIT_PREVIEW === '1';
const readback = process.env.UI_AUDIT_RECHECK === '1';
const inventory = readback
    ? JSON.parse(fs.readFileSync(`${directory}/recheck-results.json`)).filter(item=>item.file).map(item=>({...item,guest:false,readback:true}))
    : preview
    ? JSON.parse(fs.readFileSync(`${directory}/protected-previews/results.json`)).filter(item=>item.file).map(item=>({...item,guest:false,preview:true}))
    : JSON.parse(fs.readFileSync(`${directory}/inventory.json`)).pages;
const state = process.env.QA_STORAGE_STATE || 'storage/app/sample-data/section-checks/browser-state.json';

(async () => {
    fs.mkdirSync(`${directory}/screenshots`, { recursive: true });
    const browser = await chromium.launch({ headless: true, executablePath: process.env.PLAYWRIGHT_EXECUTABLE_PATH });
    const results = [];
    const contexts = {};
    const assetCache = new Map();
    const mime = {'.js':'application/javascript','.css':'text/css','.woff2':'font/woff2','.woff':'font/woff','.ttf':'font/ttf','.png':'image/png','.jpg':'image/jpeg','.jpeg':'image/jpeg','.svg':'image/svg+xml','.gif':'image/gif','.ico':'image/x-icon'};
    for (const guest of [false, true]) {
        contexts[guest] = await browser.newContext(guest ? {} : { storageState: state });
        await contexts[guest].route('**/*', async route => {
            const request = route.request(); const url = new URL(request.url());
            const unsafe = /delete|destroy|logout|clear|import|repair|send|sso|sync|generate|parseEmails|readAll|notification|Notifications|getOrCreateChat|createCustomChat|addUserToCustomChat|removeUserFromChat|roomlist\/.*\/create/i.test(url.pathname);
            if (!['GET','HEAD'].includes(request.method()) || unsafe) return route.abort();
            if (!readback && !preview && /^\/(tour|comparison)\/\d+$/.test(url.pathname)) return route.abort();
            if (url.origin === base) {
                if ((preview || readback) && request.isNavigationRequest()) {
                    const item = inventory.find(item=>item.path===url.pathname);
                    if (item) return route.fulfill({path:`${directory}/${preview?'protected-previews/':''}${item.file}`,contentType:'text/html',status:item.status});
                }
                const extension = path.extname(url.pathname).toLowerCase();
                const publicRoot = path.resolve('public');
                const file = path.resolve(publicRoot, '.' + decodeURIComponent(url.pathname));
                if (mime[extension] && file.startsWith(publicRoot + path.sep) && fs.existsSync(file) && fs.statSync(file).isFile()) {
                    if (!assetCache.has(file)) assetCache.set(file, fs.readFileSync(file));
                    return route.fulfill({ body: assetCache.get(file), contentType: mime[extension] });
                }
                return route.continue();
            }
            if (['cdn.jsdelivr.net','cdnjs.cloudflare.com','fonts.googleapis.com','fonts.gstatic.com'].includes(url.hostname)) {
                try {
                    if (!assetCache.has(url.href)) {
                        const response = await route.fetch({ timeout: 6000 });
                        assetCache.set(url.href, {body: await response.body(), headers: response.headers(), status: response.status()});
                    }
                    const cached = assetCache.get(url.href);
                    return cached ? await route.fulfill(cached) : route.abort();
                } catch (error) { assetCache.set(url.href, null); return route.abort(); }
            }
            return route.abort();
        });
    }
    const start = Number(process.env.UI_AUDIT_START || 0);
    const end = Number(process.env.UI_AUDIT_END || inventory.length);
    try {
        for (let index = start; index < Math.min(end, inventory.length); index++) {
            const item = inventory[index];
            const page = await contexts[item.guest].newPage();
            const errors = [], failures = [], externalFailures = [];
            page.on('pageerror', error => errors.push(error.message));
            page.on('requestfailed', request => {
                const url = new URL(request.url());
                if (url.origin !== base) externalFailures.push({url: url.origin + url.pathname, type: request.resourceType()});
            });
            page.on('response', response => {
                if (response.status() >= 400 && new URL(response.url()).origin === base) {
                    failures.push({ url: new URL(response.url()).pathname, status: response.status(), type: response.request().resourceType() });
                }
            });
            const result = { ...item, errors, failures, externalFailures, viewports: [] };
            try {
                await page.setViewportSize({ width: 1440, height: 1000 });
                const response = await page.goto(base + item.path, { waitUntil: 'domcontentloaded', timeout: 25000 });
                result.status = response ? response.status() : null;
                result.finalPath = new URL(page.url()).pathname;
                await page.waitForTimeout(350);
                for (const width of [1440, 390]) {
                    await page.setViewportSize({ width, height: width === 390 ? 844 : 1000 });
                    await page.waitForTimeout(120);
                    const details = await page.evaluate(() => {
                        const visible = el => el.getClientRects().length > 0 && getComputedStyle(el).visibility !== 'hidden' && getComputedStyle(el).display !== 'none';
                        const describe = el => ({ tag: el.tagName, id: el.id, name: el.getAttribute('name'), text: (el.textContent || '').trim().slice(0,70), class: typeof el.className === 'string' ? el.className.slice(0,100) : '' });
                        const ids = {};
                        for (const el of document.querySelectorAll('[id]')) (ids[el.id] ||= []).push(el);
                        const main = document.querySelector('.page-body, section.content, main, .content-wrapper') || document.body;
                        const formControls = [...main.querySelectorAll('input,select,textarea')].filter(el => visible(el) && !['hidden','submit','button','checkbox','radio'].includes(el.type) && !el.classList.contains('select2-search__field'));
                        const unnamed = formControls.filter(el => !(el.labels?.length || el.getAttribute('aria-label') || el.getAttribute('aria-labelledby') || el.getAttribute('title')));
                        const controls = [...main.querySelectorAll('button,a.btn,[role=button]')].filter(visible);
                        const name = el => (el.textContent || '').trim() || el.getAttribute('aria-label') || el.getAttribute('title') || el.getAttribute('data-bs-title') || el.getAttribute('aria-labelledby');
                        const overflow = [...main.querySelectorAll('*')].filter(el => {
                            if (!visible(el) || el.closest('.table-responsive,.select2-dropdown,.modal,.offcanvas,.dropdown-menu,.amcharts-chart-div,.amcharts-main-div')) return false;
                            const rect = el.getBoundingClientRect();
                            return rect.width > 0 && rect.left >= 0 && rect.right > innerWidth + 3 && !el.children.length;
                        }).slice(0,12).map(describe);
                        return {
                            width: innerWidth, documentWidth: document.documentElement.scrollWidth,
                            title: document.title, textLength: main.innerText.trim().length,
                            headings: [...main.querySelectorAll('h1,h2,.page-title')].map(el => el.innerText.trim()).filter(Boolean).slice(0,8),
                            duplicateIds: Object.entries(ids).filter(([id,els]) => id && els.length > 1).map(([id,els]) => ({id,count:els.length})).slice(0,30),
                            brokenImages: [...document.images].filter(el => visible(el) && el.complete && el.naturalWidth === 0).map(el=>({src:el.getAttribute('src'),alt:el.alt})).slice(0,12),
                            unlabelledFields: unnamed.map(describe).slice(0,25),
                            unnamedButtons: controls.filter(el => !name(el)).map(describe).slice(0,20),
                            orphanLabels: [...main.querySelectorAll('label[for]')].filter(el => visible(el) && !document.getElementById(el.htmlFor)).map(el=>({for:el.htmlFor,text:el.innerText.trim()})).slice(0,20),
                            nestedInteractive: [...main.querySelectorAll('a button,button a')].map(describe).slice(0,20),
                            overflow,
                            mainText: main.innerText.trim().slice(0,1400),
                        };
                    });
                    result.viewports.push(details);
                    if (width === 390 || result.status >= 400) {
                        const screenshot = `${readback?'readback-':preview?'preview-':''}${String(index).padStart(3,'0')}-${width}.png`;
                        await page.screenshot({ path: `${directory}/screenshots/${screenshot}`, fullPage: false, timeout: 10000 });
                        details.screenshot = screenshot;
                    }
                    if (result.status !== 200) break;
                }
            } catch (error) { result.navigationError = error.message; }
            results.push(result);
            fs.writeFileSync(`${directory}/results-${readback?'readback-':preview?'preview-':''}${start}-${end}.json`, JSON.stringify(results, null, 2));
            console.log(`${index+1}/${inventory.length} ${item.path} ${result.status || 'ERROR'} js=${errors.length} mobileOverflow=${result.viewports.at(-1)?.documentWidth || '?'} ${result.navigationError ? result.navigationError.split('\n')[0] : ''}`);
            await page.close();
        }
    } finally { await browser.close(); }
})().catch(error => { console.error(error); process.exitCode = 1; });
