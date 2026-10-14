const assert = require('node:assert/strict');
const fs = require('node:fs');
const { chromium } = require(process.env.PLAYWRIGHT_MODULE || 'playwright');
const base = process.env.ACCOUNTING_BASE_URL || 'http://127.0.0.1:8010';
(async()=>{
 const ids=JSON.parse(fs.readFileSync(process.env.ACCOUNTING_MANIFEST));
 const browser=await chromium.launch();
 const context=await browser.newContext({storageState:process.env.ACCOUNTING_STORAGE_STATE});
 await context.route('**/*',r=>new URL(r.request().url()).origin===base?r.continue():r.abort());
 const page=await context.newPage();const results=[];
 const pass=name=>{results.push({scenario:name,status:'Pass'});console.log('PASS:',name);};
 try {
  await page.goto(base+'/accounting',{waitUntil:'domcontentloaded'});
  const tableCheck=await page.evaluate(()=>{
   const table=document.getElementById('transactions-table');
   filterTable('transactions-table','QA-SAMPLE-CLIENT-');
   const matched=[...table.tBodies[0].rows].filter(r=>r.style.display!=='none'&&!r.classList.contains('table-search-empty')).length;
   filterTable('transactions-table','');
   table.querySelector('th').click(); table.querySelector('th').click();
   const first=Number(table.tBodies[0].rows[0].cells[0].textContent.trim());
   const state=table._tablePagination;
   state.size=5;state.page=2;renderTablePage(table);
   const next=[...table.tBodies[0].rows].find(r=>r.style.display!=='none');
   const second=Number(next.cells[0].textContent.trim());
   filterTable('transactions-table','NO-SUCH-INVOICE-999999');
   const empty=!!table.querySelector('.table-search-empty');
   state.size=50;filterTable('transactions-table','');
   return {matched,first,second,empty};
  });
  console.log('Table check:',tableCheck);
  assert.equal(tableCheck.matched,3);assert(tableCheck.first>tableCheck.second);assert(tableCheck.empty);
  pass('Invoice search, clear, sorting, pagination and no-match state');
  const csrf=await page.locator('meta[name="csrf-token"]').getAttribute('content');
  const post=(path,data,extra={})=>context.request.post(base+path,{headers:{'X-CSRF-TOKEN':csrf,Accept:'application/json'},data,maxRedirects:0,...extra});
  const get=path=>context.request.get(base+path,{maxRedirects:0});
  const invoicePayload={currency:'EUR',office_id:ids.office,tour_id:ids.tour,client_id:ids.client,items:[{item_name:'QA SAMPLE service',quantity:2,amount:100,vat:.2,total_amount:0}],extra_cost:5.50};
  assert.equal((await post(`/accounting/${ids.client_invoice}/update`,invoicePayload)).status(),302);
  let show=await get(`/accounting/${ids.client_invoice}`);assert((await show.text()).includes('245.50'));
  invoicePayload.extra_cost=0;assert.equal((await post(`/accounting/${ids.client_invoice}/update`,invoicePayload)).status(),302);
  pass('Client invoice edit recalculates VAT and fractional extra cost; restores sample total');
  const supplierPayload={office_id:ids.office,tours:ids.tour,invoice_no:'QA-SAMPLE-SUPPLIER-PARTIAL',total_amount:310};
  assert.equal((await post(`/invoices/${ids.supplier_link}/update`,supplierPayload)).status(),302);
  show=await get(`/invoices/${ids.supplier_link}`);assert((await show.text()).includes('235.00'));
  assert.equal((await post(`/invoices/${ids.supplier_link}/update`,{...supplierPayload,total_amount:50})).status(),422);
  supplierPayload.total_amount=300;assert.equal((await post(`/invoices/${ids.supplier_link}/update`,supplierPayload)).status(),302);
  pass('Supplier edit preserves existing payments and rejects total below paid amount');
  assert.equal((await get(`/transaction/${ids.transaction}/edit`)).status(),200);
  const updatePayment=(amount)=>context.request.put(base+`/transaction/${ids.transaction}`,{headers:{'X-CSRF-TOKEN':csrf,Accept:'application/json'},data:{date:'2026-10-14',amount,payment_method:'Business Debit Card'},maxRedirects:0});
  assert.equal((await updatePayment(1000)).status(),422);
  assert.equal((await updatePayment(75.25)).status(),302);
  assert.equal((await updatePayment(75)).status(),302);pass('Ledger payment editing preserves reference and validates balance');
  assert.equal((await post('/transaction',{date:'2026-10-14',pay_to:'Client',invoice_id:ids.client_invoice,amount:15.55})).status(),302);
  await page.goto(base+'/transaction',{waitUntil:'domcontentloaded'});
  const ledgerRow=page.locator('tbody tr').filter({hasText:'QA-SAMPLE-CLIENT-UNPAID'});const paymentId=Number(await ledgerRow.locator('td').first().innerText());assert(paymentId>0);
  assert.equal((await context.request.delete(base+`/transaction/${paymentId}`,{headers:{'X-CSRF-TOKEN':csrf,Accept:'application/json'},maxRedirects:0})).status(),302);
  assert.equal((await get(`/transaction/${paymentId}`)).status(),404);pass('Ledger payment create/delete');
  const children={
   tour_expenses:{tour_name:'QA SAMPLE Tour expense',tour_expenses:100.25,tour_departure_date:'2026-11-01',tour_return_date:'2026-11-03'},
   utility_expenses:{subject_of_expense:'QA SAMPLE Utilities',month:'2026-10-01',monthly_expense:25.50},
   'employes-salary':{employe_name:'QA SAMPLE Employee',month:'2026-10-01',employe_salary:200.50,bonuses:10},
   office_earning:{month:'2026-10-01',revenue:1000.50,profit:400.25},
   office_balance:{subject_of_balance:'QA SAMPLE Balance',month:'2026-10-01',total_amount:64.50,due_date:'2026-11-01'}
  };
  for(const [child,data] of Object.entries(children)){
   assert.equal((await post(`/${child}/${ids[child]}/update`,{...data,office_id:ids.office})).status(),302,child+' valid update');
   const field=['tour_expenses','monthly_expense','employe_salary','profit','total_amount'].find(f=>f in data);
   assert.equal((await post(`/${child}/${ids[child]}/update`,{...data,office_id:ids.office,[field]:-1})).status(),422,child+' negative');
   pass(child+' saves fractional amount and rejects negative update');
  }
  assert.equal((await post('/tour_expenses',{...children.tour_expenses,office_id:ids.office,tour_return_date:'2026-10-01'})).status(),422);
  pass('Reversed tour expense dates rejected');
  const label='QA SAMPLE Disposable '+Date.now();
  assert.equal((await post('/office',{office_name:label,office_address:'Temporary QA office'})).status(),302);
  await page.goto(base+'/office',{waitUntil:'domcontentloaded'});
  const officeRow=page.locator('tbody tr').filter({hasText:label});const officeId=Number(await officeRow.locator('td').first().innerText());
  assert(officeId>0);
  assert.equal((await post(`/office/${officeId}/update`,{office_name:label+' edited',office_address:'Edited QA address'})).status(),302);
  assert.equal((await get(`/office/${officeId}/delete`)).status(),302);
  assert.equal((await get(`/office/${officeId}`)).status(),404);
  pass('Office create/edit/delete with optional bank fields');
  assert.equal((await post('/taxes',{name:label,percentageInput:7.50})).status(),302);
  await page.goto(base+'/taxes',{waitUntil:'domcontentloaded'});
  const taxRow=page.locator('tbody tr').filter({hasText:label});const taxId=Number(await taxRow.locator('td').first().innerText());assert(taxId>0);
  assert.equal((await post(`/taxes/${taxId}/update`,{name:label+' zero',percentageInput:0})).status(),302);
  assert.equal((await context.request.delete(base+`/taxes/${taxId}`,{headers:{'X-CSRF-TOKEN':csrf,Accept:'application/json'},maxRedirects:0})).status(),302);
  assert.equal((await get(`/taxes/${taxId}`)).status(),404);pass('Tax create/edit including zero VAT/delete');
  const interData={from_office:ids.office,to_office:ids.partner,invoiceno:label.slice(0,55),dateoffice:'2026-10-14',data:[{office_item:'QA SAMPLE transfer service',officeinvoice_date:'2026-10-14',officeinvoice_code:'QA-TEST',officeinvoice_amount:125.55,des:'Test only'}]};
  let response=await post('/officeInvoices/store',interData,{headers:{'X-CSRF-TOKEN':csrf,Accept:'application/json','X-Requested-With':'XMLHttpRequest'}});
  assert.equal(response.status(),200);const detail=(await response.json()).route;const interId=Number(detail.split('/').pop());assert(interId>0);
  assert.equal((await get(`/officeInvoices/${interId}/edit`)).status(),200);
  assert.equal((await post(`/officeInvoices/${interId}/update`,{...interData,data:[{...interData.data[0],officeinvoice_amount:130.55}]})).status(),302);
  response=await get(`/officeInvoices/${interId}/export/short`);assert.equal(response.status(),200);assert((await response.body()).subarray(0,4).toString()==='%PDF');
  assert.equal((await get(`/officeInvoices/${interId}/delete`)).status(),302);assert.equal((await get(`/officeInvoicesdetail/${interId}`)).status(),404);
  pass('Interoffice invoice create/edit/PDF/delete');
  for(const [path,magic] of [[`/accounting/${ids.client_invoice}/export/short`,'%PDF'],[`/accounting/${ids.client_invoice}/excel`,'PK']]){
   response=await get(path);assert.equal(response.status(),200,path);assert((await response.body()).subarray(0,magic.length).toString()===magic,path+' content');pass(path+' valid download');
  }
  for(const type of ['hotel','event','guide','restaurant']){
   const response=await get(`/reporting?service=${type[0].toUpperCase()+type.slice(1)}`);assert.equal(response.status(),200);
   const html=await response.text();const match=html.match(new RegExp('/reporting/'+type+'/(\\d+)/show'));
   assert(match,type+' report link');assert.equal((await get(`/reporting/${type}/${match[1]}/show`)).status(),200);pass(type+' report/filter');
  }
  const missing=await get('/reporting?q=NO-SUCH-QA-SUPPLIER-999999');assert.equal(missing.status(),200);assert((await missing.text()).includes('No services found'));pass('Report no-match filter');
  const guest=await browser.newContext();const denied=await guest.request.get(base+'/accounting',{maxRedirects:0});assert.equal(denied.status(),302);assert(denied.headers().location.includes('login'));await guest.close();pass('Guest access rejected');
 } finally {fs.writeFileSync('storage/app/accounting-workflow-results.json',JSON.stringify(results,null,2));await browser.close();}
})().catch(e=>{console.error(e);process.exitCode=1});
