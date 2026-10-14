// Requires authenticated Playwright storage state and QA SAMPLE record manifest.
// Uses only labeled sample records; no mail or external payment provider is called.
const assert = require('node:assert/strict');
const fs = require('node:fs');
const { chromium } = require(process.env.PLAYWRIGHT_MODULE || 'playwright');
const base = process.env.ACCOUNTING_BASE_URL || 'http://127.0.0.1:8010';
(async () => {
 const ids = JSON.parse(fs.readFileSync(process.env.ACCOUNTING_MANIFEST));
 const browser = await chromium.launch();
 const context = await browser.newContext({storageState:process.env.ACCOUNTING_STORAGE_STATE});
 await context.route('**/*', r => new URL(r.request().url()).origin===base?r.continue():r.abort());
 const page = await context.newPage(); const results=[];
 const paths=['/accounting','/accounting/create',`/accounting/${ids.client_invoice}`,`/accounting/${ids.client_invoice}/edit`,`/addInvoicePayment/${ids.client_invoice}`,
 '/invoices','/invoices/create',`/invoices/${ids.supplier_link}`,`/invoices/${ids.supplier_link}/edit`,`/add_payment/${ids.supplier_invoice}`,
 '/office','/office/create',`/office/${ids.office}`,`/office/${ids.office}/edit`,
 '/taxes','/taxes/create',`/taxes/${ids.tax}`,`/taxes/${ids.tax}/edit`,
 '/transaction','/transaction/create',`/transaction/${ids.transaction}`,
 `/officeInvoices/create/${ids.office}`,'/reporting'];
 for (const child of ['tour_expenses','utility_expenses','employes-salary','office_earning','office_balance']) paths.push(`/${child}/create/${ids.office}`,`/${child}/${ids[child]}/edit`);
 try {
  for(const path of paths) {
   const errors=[]; const listener=e=>errors.push(e.message); page.on('pageerror',listener);
   const response=await page.goto(base+path,{waitUntil:'domcontentloaded',timeout:60000}); await page.waitForTimeout(500);
   const status=response.status();
   results.push({path,status,errors,body:status>=400?(await page.locator('body').innerText()).slice(-1500):undefined});
   fs.writeFileSync('storage/app/accounting-audit-results.json',JSON.stringify(results,null,2));
   console.log(path,status,errors);
   page.off('pageerror',listener);
  }
  await page.goto(base+'/accounting/create',{waitUntil:'domcontentloaded'});
  await page.selectOption('[name=currency]','EUR'); await page.selectOption('[name=office_id]',String(ids.office));
  await page.selectOption('[name=tour_id]',String(ids.tour)); await page.selectOption('[name=client_id]',String(ids.client));
  await page.locator('[name$="[item_name]"]').first().fill('QA SAMPLE Browser VAT service');
  await page.locator('[name$="[quantity]"]').first().fill('2'); await page.locator('[name$="[amount]"]').first().fill('100');
  await page.locator('[name$="[vat]"]').first().selectOption('0.2');
  await page.locator('[name$="[vat]"]').first().dispatchEvent('change');
  assert.equal(await page.locator('[name$="[total_amount]"]').first().inputValue(),'240.00');
  await page.locator('#add_feild_button').click(); await page.locator('[name="paid_amount[]"]').first().waitFor();
  await page.locator('[name="paid_amount[]"]').first().fill('20.50'); await page.locator('[name="paymentdate[]"]').first().fill('2026-10-14');
  await page.locator('[name="payment_method[]"]').first().selectOption('Business Debit Card');
  await Promise.all([page.waitForURL(base+'/accounting'),page.locator('#invoice-form button[type=submit]').last().click()]);
  console.log('PASS: UI created VAT invoice with fractional payment'); results.push({scenario:'UI VAT invoice and fractional payment',status:'Pass'});
  const csrf=await page.locator('meta[name="csrf-token"]').getAttribute('content');
  const post=async(path,data)=>context.request.post(base+path,{headers:{'X-CSRF-TOKEN':csrf,'Accept':'application/json'},data});
  const invalid=await post('/inv_payment_store/'+ids.client_invoice,{paid_amount:[-5],paymentdate:['2026-10-14'],payment_method:['Bank']});
  assert.equal(invalid.status(),422); results.push({scenario:'Negative client payment rejected',status:'Pass'});
  const overpay=await post('/payment_store/'+ids.supplier_invoice,{paid_amount:[1000],paymentdate:['2026-10-14'],payment_method:['Bank']});
  assert.equal(overpay.status(),422); results.push({scenario:'Supplier overpayment rejected',status:'Pass'});
  const badTax=await post('/taxes',{name:'QA SAMPLE Invalid VAT',percentageInput:-10});
  assert.equal(badTax.status(),422); results.push({scenario:'Negative tax rejected',status:'Pass'});
  await page.setViewportSize({width:390,height:844});
  for(const path of ['/accounting/create',`/office/${ids.office}`,'/taxes',`/invoices/${ids.supplier_link}`]) {
   await page.goto(base+path,{waitUntil:'domcontentloaded'});
   const overflow=await page.evaluate(()=>document.documentElement.scrollWidth>innerWidth+2);
   results.push({scenario:'Mobile '+path,overflow,status:overflow?'Fail':'Pass'});
   await page.screenshot({path:'storage/app/accounting-mobile-'+path.replace(/\W/g,'_')+'.png',fullPage:true});
  }
  assert(results.every(r=>r.status===200||r.status==='Pass') && results.every(r=>!r.errors?.length),'Page or UI failures; inspect results.');
 } finally {
  fs.writeFileSync('storage/app/accounting-audit-results.json',JSON.stringify(results,null,2));
  await browser.close();
 }
})().catch(error=>{console.error(error);process.exitCode=1;});
