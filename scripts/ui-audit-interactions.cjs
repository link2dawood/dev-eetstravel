const fs = require('node:fs');
const path = require('node:path');
const { chromium } = require(process.env.PLAYWRIGHT_MODULE || 'playwright');
const directory = 'storage/app/ui-audit';
const base = 'http://127.0.0.1:8010';
(async () => {
    const browser = await chromium.launch({ headless: true, executablePath: process.env.PLAYWRIGHT_EXECUTABLE_PATH });
    const context = await browser.newContext({ storageState: 'storage/app/sample-data/section-checks/browser-state.json' });
    await context.route('**/*', route => {
        const request = route.request(), url = new URL(request.url());
        const unsafe = /delete|destroy|logout|clear|import|repair|send|sso|sync|generate|parseEmails|readAll|notification|Notifications|getOrCreateChat|createCustomChat|addUserToCustomChat|removeUserFromChat|roomlist\/.*\/create/i.test(url.pathname);
        if (url.origin !== base || !['GET','HEAD'].includes(request.method()) || unsafe) return route.abort();
        const root=path.resolve('public'), file=path.resolve(root,'.'+decodeURIComponent(url.pathname));
        const types={'.js':'application/javascript','.css':'text/css','.woff2':'font/woff2','.woff':'font/woff','.ttf':'font/ttf','.png':'image/png','.jpg':'image/jpeg','.svg':'image/svg+xml','.gif':'image/gif','.ico':'image/x-icon'};
        if(types[path.extname(file)]&&file.startsWith(root+path.sep)&&fs.existsSync(file))return route.fulfill({path:file,contentType:types[path.extname(file)]});
        return route.continue();
    });
    const page = await context.newPage(); const results = {};
    page.setDefaultNavigationTimeout(60000);
    try {
        await page.setViewportSize({width:390,height:844});
        await page.goto(base + '/clients/9/edit', {waitUntil:'domcontentloaded'});
        await page.waitForFunction(() => !document.querySelector('#add_contact').disabled);
        results.mobile = await page.evaluate(() => ({
            togglers: [...document.querySelectorAll('.navbar-toggler')].filter(el=>el.getClientRects().length).map(el=>({target:el.getAttribute('data-bs-target'),targetExists:!!document.querySelector(el.getAttribute('data-bs-target'))})),
            bars: [...document.querySelectorAll('aside.navbar,header.navbar')].map(el=>({height:el.getBoundingClientRect().height,brand:el.querySelector('.navbar-brand')?.innerText})),
            controls: [...document.querySelectorAll('.client-form input.form-control,.client-form .select2-selection')].filter(el=>el.getClientRects().length).map(el=>({id:el.id,class:el.className,height:el.getBoundingClientRect().height})),
            notificationTriggers:[...document.querySelectorAll('[aria-label="Show notifications"],[aria-label="Show messages"]')].map(el=>({label:el.getAttribute('aria-label'),visible:el.getClientRects().length>0})),
        }));
        await page.locator('header .navbar-toggler').click();
        results.mobile.headerMenuOpens = await page.locator('#sidebar-menu').evaluate(el=>el.classList.contains('show'));
        await page.locator('aside .navbar-toggler').click();
        await page.waitForTimeout(800);
        results.mobile.sidebarMenuOpens = await page.locator('#sidebar-menu').evaluate(el=>el.classList.contains('show'));
        results.mobile.sidebarState = await page.locator('#sidebar-menu').evaluate(el=>({class:el.className,display:getComputedStyle(el).display,height:el.getBoundingClientRect().height}));
        results.navigation = await page.locator('aside a[href$="/clients"]').evaluate(el=>({class:el.className,parent:el.closest('.nav-item').className}));
        await page.screenshot({path:directory+'/screenshots/mobile-menu-open.png'});
        await page.locator('aside .navbar-toggler').click();
        await page.setViewportSize({width:1440,height:1000});
        const before = await page.locator('#items-contacts .item-contact').count();
        await page.locator('#add_contact').click();
        await page.waitForFunction(count=>document.querySelectorAll('#items-contacts .item-contact').length===count,before+1);
        const label=page.locator('#items-contacts .item-contact').last().locator('label[for="contact_email"]');
        await label.click();
        results.contactLabel = await page.evaluate(()=>({activeName:document.activeElement.name,activeId:document.activeElement.id,duplicateEmailIds:document.querySelectorAll('#contact_email').length,
            removers:[...document.querySelectorAll('#delete_contact_item')].map(el=>({tag:el.tagName,role:el.getAttribute('role'),tabindex:el.getAttribute('tabindex')}))}));

        await page.goto(base + '/chat/main', {waitUntil:'domcontentloaded'});
        if (await page.locator('.add-direct-chat').count()) {
            await page.locator('.add-direct-chat').first().click();
            await page.waitForTimeout(800);
            results.chat = await page.evaluate(()=>({visibleModals:[...document.querySelectorAll('.modal')].filter(el=>el.getClientRects().length&&getComputedStyle(el).display!=='none').map(el=>({id:el.id,text:el.innerText.slice(0,200)})),duplicateModalIds:document.querySelectorAll('#myModal').length}));
        }
        await page.setViewportSize({width:390,height:844});
        await page.goto(base+'/profile/edit',{waitUntil:'domcontentloaded'});
        await page.locator('#avatar').scrollIntoViewIfNeeded();
        await page.screenshot({path:directory+'/screenshots/profile-avatar-mobile.png'});
        results.avatarUpload = await page.evaluate(()=>[...document.querySelectorAll('.file-caption-name,.file-input .btn-file,.file-input .fileinput-remove')].map(el=>({class:el.className,left:el.getBoundingClientRect().left,right:el.getBoundingClientRect().right,width:el.getBoundingClientRect().width,visible:el.getClientRects().length>0})));
        fs.writeFileSync(directory+'/interaction-results.json',JSON.stringify(results,null,2));
        console.log(JSON.stringify(results,null,2));
    } finally {await browser.close();}
})().catch(error=>{console.error(error);process.exitCode=1;});
