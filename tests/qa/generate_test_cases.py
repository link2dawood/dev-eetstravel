"""Build reviewable manual QA cases from Laravel's registered route inventory.

Usage: php artisan route:list --json > storage/app/test-case-routes.json
       python tests/qa/generate_test_cases.py
This script generates documents only; it does not call application endpoints.
"""
import csv
import json
from datetime import date
from collections import defaultdict
from pathlib import Path

ROOT = Path(__file__).resolve().parents[2]
OUT = ROOT / 'docs' / 'qa'
OUT.mkdir(parents=True, exist_ok=True)
route_bytes = (ROOT / 'storage/app/test-case-routes.json').read_bytes()
routes = json.loads(route_bytes.decode('utf-16' if route_bytes.startswith((b'\xff\xfe', b'\xfe\xff')) else 'utf-8-sig'))
groups = defaultdict(list)
for route in routes:
    action = route['action']
    if action.startswith('App\\Http\\Controllers\\'):
        module = action.removeprefix('App\\Http\\Controllers\\').split('@')[0].removesuffix('Controller')
    elif route['uri'].startswith(('_debugbar', '_ignition')):
        continue
    else:
        module = 'System endpoints'
    groups[module].append(route)

# Focused cases supplement baseline and endpoint coverage. Each entry is
# (scenario, steps/data, expected result); these are acceptance targets, not claims.
focused = {
    'Activities': [('Activity audit trail', 'Create/edit/delete disposable records as users A/B; filter or page activity list; inspect as limited staff.', 'Entries identify correct actor, record and action; ordering is accurate; sensitive payloads and unauthorized activity remain hidden.')],
    'Auth\\Login': [
        ('Login and logout', 'Sign in with a valid test account; refresh; logout; revisit a protected URL and use browser Back.', 'Session survives refresh; logout removes access; cached pages expose no protected data.'),
        ('Invalid login', 'Try wrong password, unknown email, empty fields and repeated failures.', 'Useful errors without account/password disclosure; configured rate limit applies.')],
    'Auth\\Register': [('Registration validation', 'Try valid test registration, duplicate email, malformed email, weak password and mismatched confirmation.', 'Only valid registration succeeds; password policy and duplicate checks apply; no elevated role can be submitted.')],
    'Auth\\ForgotPassword': [('Reset request', 'Request reset for existing and unknown accounts using a mail sink; repeat requests.', 'Responses do not enumerate accounts; valid reset mail arrives only in the sink; rate limit applies.')],
    'Auth\\ResetPassword': [('Reset token lifecycle', 'Reset with valid, altered, expired and already-used tokens; try mismatched password confirmation.', 'Only a valid unused token resets the intended account; invalid requests preserve the old password.')],
    'Auth\\ConfirmPassword': [('Password confirmation', 'Confirm with wrong then correct password; revisit a confirmation-protected action.', 'Wrong password is rejected; correct confirmation grants only the intended temporary access.')],
    'Tour': [
        ('Tour lifecycle', 'Create a future multi-day tour with client, passengers and services; edit dates; convert to quotation and back; clone.', 'Dates, references and status remain consistent; clone has independent editable records and no duplicated financial entries.'),
        ('Price and passenger boundaries', 'Try negative price; zero passengers; free passengers exceeding passengers; reversed dates; edit price then refresh.', 'Invalid values are rejected; valid price persists and dependent totals recalculate consistently.'),
        ('Confirmed hotel protection', 'Attempt to remove or reschedule a day with a confirmed hotel service; cancel confirmation and retry.', 'Confirmed booking rules prevent accidental loss; allowed changes affect only the chosen tour/day.'),
        ('Exports and public page', 'Export PDF, document and HTML with long descriptions and Unicode; open landing page as guest.', 'Downloads open with correct data and layout; public page reveals only approved public fields.')],
    'TourPackage': [
        ('Service scheduling', 'Add hotel, event, guide, transfer, restaurant, cruise and flight services to days; reorder and change times.', 'Each supported type saves correct supplier/day/time; ordering persists and totals remain consistent.'),
        ('Hotel dates and room types', 'Set hotel check-in/out, main hotel and room types; change day; inspect last-date and date lookup responses.', 'Room options match hotel; dates stay within intended itinerary; day references remain valid.'),
        ('Status and cancellation', 'Confirm service, cancel it, delete cancelled packages and remove one transfer; inspect history.', 'Only selected eligible services change; confirmed bookings remain protected; history identifies changes.')],
    'Bus': [
        ('Unique bus number', 'Create bus QA-BUS-001; repeat number; edit same bus without changing number; change to another bus number.', 'Duplicate create/update rejected; unchanged unique number accepted; no partial attachment save.'),
        ('Trip overlap', 'Assign bus and driver across several days; try overlapping dates and conflicting driver; edit original trip.', 'Conflicts rejected across the whole interval; trip excludes itself during availability checks.'),
        ('Single-day versus full-trip edit', 'Edit one day of a multi-day trip, then edit whole trip; repeat for tour-owned bus days.', 'Single-day changes leave other days untouched; whole-trip changes update only the selected trip.'),
        ('Delete integrity', 'Delete an unused bus; attempt deleting a scheduled bus; delete one day and then one full trip.', 'Unused bus removed; scheduled bus preserved; deletion scope is correct; no orphan records.'),
        ('Calendar boundaries and async errors', 'Navigate Dec/Jan, leap February, filters and legend; delay modal load and trigger save; simulate failed request.', 'Correct month/day alignment; filters update current data; stale form cannot save; failures are visible.')],
    'Driver': [('Driver availability', 'Assign driver to a trip; attempt overlapping assignment via form and calendar; delete an assigned driver.', 'Overlaps and destructive removal of active assignments are prevented without altering other trips.')],
    'Transfer': [('Company-dependent resources', 'Choose company A then B in trip form; inspect buses/drivers; edit a company with linked resources.', 'Options belong to selected company; stale selections clear; linked resource consistency is preserved.')],
    'Client': [('Contacts and geography', 'Add multiple contacts; change country/state; load contact dropdown; clear optional fields.', 'Correct client contacts and country/state options load; stale state clears; optional blanks save correctly.')],
    'ClientInvoice': [('Invoice calculation and payment', 'Create two lines with known quantities, rates and taxes; add partial then final payment; export PDF/Excel.', 'Calculated totals match independent arithmetic and rounding; balances reduce exactly; exports match stored invoice.'), ('Payment failure and replay', 'Submit zero, negative and excessive payment; double-click valid payment and retry after simulated timeout.', 'Invalid amounts rejected; one intended payment is recorded once; ledger and balance stay consistent.')],
    'Invoices': [('Supplier invoice and payments', 'Select tour then supplier; create invoice with known totals; pay partially then fully; inspect transactions.', 'Supplier options match context; payable balance and payment history reconcile; duplicate submission does not double-charge.')],
    'Transaction': [('Ledger reconciliation', 'Record credit and debit with known amounts; edit or delete one allowed entry; compare invoice and office balances.', 'Exactly one intended ledger change occurs; linked balances and reports reconcile with preserved audit history.')],
    'Tax': [('Tax arithmetic', 'Create zero and fractional percentage taxes; attempt negative and invalid rates; apply to invoice lines.', 'Valid tax calculates with consistent rounding; invalid values rejected; existing invoice history is not silently rewritten.')],
    'Office': [('Office isolation', 'Create offices A/B with invoices, expenses and payments; switch office; attempt deletion with dependents.', 'Records and totals stay scoped to selected office; linked financial records are protected.')],
    'OfficeInvoice': [('Office invoice details and PDF', 'Create invoice for office A; open detail endpoint and PDF; attempt office B identifier substitution.', 'Details and export agree; unauthorized office records are inaccessible; totals match line arithmetic.')],
    'TourExpense': [('Tour expense allocation', 'Create expense on tour A in office A; edit amount; try missing tour and foreign office reference.', 'Expense affects only correct tour/office; invalid relationships rejected; balances update once.')],
    'UtilityExpense': [('Utility expense totals', 'Record fractional expense; edit and delete it; compare office report before/after.', 'Expense contributes once to correct office and reporting period with consistent rounding.')],
    'EmployesSalary': [('Salary allocation', 'Create salary for employee/office/period; try duplicate period and negative amount; inspect totals.', 'Valid salary is correctly allocated; duplicate handling follows approved policy; negative amount rejected.')],
    'OfficeEarning': [('Earnings aggregation', 'Record earnings in two offices and periods; edit one entry; compare office summaries.', 'Only corresponding office/period changes; no duplicate income is counted.')],
    'BalanceAmount': [('Balance reconciliation', 'Seed known opening balance, earnings, expenses and payments; inspect balance then edit an entry.', 'Displayed balance equals independently calculated opening balance plus income minus outgoings, under documented sign convention.')],
    'Reporting': [('Supplier report reconciliation', 'Filter hotel/event/guide/restaurant reports by dates and supplier; include empty period and boundary dates.', 'Rows belong to selected supplier/period; totals match source records; no-data state is clear.')],
    'Offer': [('Offer lifecycle and assignment', 'Create current and expired offers; assign one to tour; change status; inspect bookings and cancellation policy.', 'Current/past lists and booking state are correct; assignment does not duplicate services; policy remains linked.')],
    'BookingRequest': [('Booking links and responses', 'Generate supplier link; respond as intended supplier; alter encrypted token and package ID; submit twice.', 'Valid response updates only intended offer; tampered/cross-supplier access rejected; retries do not duplicate booking.'), ('Booking emails', 'Add valid/invalid recipient addresses and open add-email form; use test mail sink for any send.', 'Validation identifies bad addresses; only selected recipients and intended package data appear.')],
    'Quotation': [('Quote confirmation and export', 'Create quote with known lines; save; confirm/cancel; edit column message; export Excel/PDF.', 'Totals and messages persist; confirmation state is consistent; exports match quotation and contain no hidden internal fields.')],
    'Agreements': [('Allotment and room types', 'Create supplier agreement; choose hotel room type; add/edit/delete allotment; test overlapping dates and negative capacity.', 'Room types match hotel; capacity and date validation apply; unrelated allotments stay unchanged.')],
    'Seasons': [('Season price boundaries', 'Create seasons spanning month/year edges; assign hotel room types; attempt reversed and overlapping ranges.', 'Date and overlap policy is enforced; rates resolve to intended season and room type.')],
    'GuestList': [('Guest list and hotel distribution', 'Add/edit/remove guests; inspect hotel-specific list and recipients; send only to mail sink.', 'Counts and names match tour; each hotel receives only intended guests; failed send is reported without losing data.')],
    'RoomList': [('Room occupancy and distribution', 'Assign guests to room types; change occupancy; export/show and send to sink.', 'No duplicate guest assignment; occupancy follows room capacity; output matches selected tour/hotel.')],
    'SupplierSearch': [('Search combinations', 'Search each supplier type using country, city and criteria; combine filters; clear filters; use no-match input.', 'Results satisfy all active filters; clearing restores results; empty state and supplier links work.')],
    'Comparison': [('Supplier comparison', 'Compare two suppliers; add comparison comment; edit/delete comparison; view as another restricted user.', 'Values align with chosen suppliers; comments persist safely; access follows permissions.')],
    'Services': [('Service history', 'Modify service status and price; open history and datatable with paging/filtering.', 'History identifies correct service changes; datatable totals and filtered rows agree.')],
    'Task': [('Task lifecycle and calendar', 'Create task with assignee/due date; change status via list, inline field and calendar; check overdue list.', 'All views show same task state; overdue rules respect timezone; unauthorized reassignment is blocked.')],
    'Announcement': [('Announcement and replies', 'Create announcement for intended audience; reply; edit; regenerate; view as excluded user.', 'Audience restrictions apply; replies attach to correct item; generation does not duplicate unintended records.')],
    'Chat': [('Chat membership and messages', 'Create direct and group chats with test users A/B; add/remove B; send text; request removed chat via direct URL.', 'Messages stay in intended chat; removed/nonmember users cannot read or post; membership updates consistently.')],
    'Comment': [('Comment threading and safety', 'Post parent and reply containing HTML/script text; edit/delete own and another user comment.', 'Threading remains correct; executable content is escaped; authorization applies to edits/deletions.')],
    'Notifications': [('Notification ownership', 'Create notifications for A/B; read all and delete one/all as A; submit B notification ID.', 'Only A notifications change; counts match; B notification remains protected.')],
    'Email': [('Mailbox workflow', 'Using sandbox mailbox, compose, reply, search, mark read, create folder, move and delete test messages; download attachment.', 'Recipients, thread, folder and read state remain correct; only intended mailbox changes; attachment bytes are correct.'), ('Mail failure', 'Use invalid recipient and simulate SMTP/IMAP timeout; retry one test message.', 'Visible error and preserved compose data; retry behavior avoids unintended duplicate sends.')],
    'Templates': [('Template variables and sending', 'Load service/description template; render missing and Unicode values; reply and send through sink.', 'Placeholders resolve safely; missing values are handled; recipients and context match intended service.')],
    'SnappyMail': [('Webmail and configuration', 'Open SSO/direct webmail; view status; save valid and invalid sandbox configuration as admin; try as regular user.', 'Only intended mailbox opens; admin configuration is protected; secrets never appear in response or logs.')],
    'Attachment': [('Upload validation', 'Upload small image/PDF; try oversized file, disguised executable, duplicate filename and interrupted upload.', 'Allowed files store correctly; prohibited/oversized files rejected; no executable serving or partial records.')],
    'File': [('Download/delete authorization', 'Download image/document without storage symlink; try missing file and another user file ID; cancel then confirm delete.', 'Correct bytes, filename and content type; unauthorized/missing files handled safely; cancel preserves file.')],
    'Import': [('Import integrity', 'Use sample with valid, duplicate, missing-column, Unicode and malformed rows; preview then import; retry same file.', 'Row-level errors are actionable; valid mapping preserved; retry does not silently duplicate records; failures do not partially corrupt data.')],
    'Export': [('Export round trip', 'Export services/seasons with commas, quotes, line breaks and Unicode; open in spreadsheet and reimport into isolated fixtures.', 'Columns and values remain intact; no spreadsheet formula execution from user-entered text; selected scope is honored.')],
    'gCalendar': [('Calendar OAuth and sync', 'Use sandbox Google account; authorize, deny, expire token and reconnect; list calendars.', 'State/token validation applies; chosen calendar is correct; denial/expiry show recoverable errors; tokens stay private.')],
    'Holiday': [('Holiday dates', 'Create holiday spanning year boundary; load dashboard calendar; edit/delete holiday.', 'Correct inclusive dates appear; edits refresh calendar; unrelated events remain unchanged.')],
    'Profile': [('Profile and scoped tabs', 'Open own profile and tours/tasks/notifications tabs; request another user identifier with limited role.', 'Correct authorized data appears; private fields and unrelated notifications are not exposed.')],
    'Setting': [('Settings persistence', 'Change allowed setting, refresh and restore; submit invalid value and extra administrative field as non-admin.', 'Valid setting persists; invalid/unauthorized changes are rejected; credentials are not returned.')],
    'Menu': [('Menu permission and ordering', 'Create/reorder allowed menu item; assign route and parent; try invalid route and cyclic parent.', 'Navigation renders usable links with permission filtering; invalid hierarchy is rejected or clearly reported.')],
    'ScaffoldInterface\\User': [('User roles and permissions', 'Create test user; grant/revoke role and direct permission; sign in separately; try granting own privilege without admin rights.', 'Effective permissions update consistently; revoked access disappears; self-escalation is rejected.')],
    'ScaffoldInterface\\Role': [('Role permission propagation', 'Assign role to A/B; add/revoke permission; refresh sessions; delete role used by users.', 'Both users reflect permission changes; no cached excess access; role deletion follows documented dependency rules.')],
    'ScaffoldInterface\\Permission': [('Permission enforcement', 'Create permission and assign through role/direct grant; revoke; call protected route directly.', 'Server enforces permission independently of menu visibility; revoked permission cannot authorize requests.')],
    'ScaffoldInterface\\App': [('Dashboard consistency', 'Compare tasks, tours, holidays and announcements with source modules; switch calendar period; quick-create event.', 'Counts and events agree with scoped source data; quick-create saves once and displays on correct date.')],
    'TMSClient\\Login': [('Client portal isolation', 'Login client A and B separately; open home and quotation requests; replace client/tour IDs; logout.', 'Each client sees only its own records; cross-client and staff-only actions are rejected; logout removes access.')],
    'TMSClient\\Tour': [('Client tour workflow', 'As client A create full and simple tours, edit own, inspect package and try client B tour ID.', 'Allowed fields persist; ownership is enforced; clients cannot modify internal pricing/status through extra fields.')],
    'TMSClient\\TourPackage': [('Client package isolation', 'View/create/edit own tour package; replace package or parent tour ID with client B identifiers.', 'Only owned permitted packages are accessible; staff-only fields and unrelated packages stay unchanged.')],
    'TMSClient\\Upload': [('Client spreadsheet upload', 'Download sample; preview and upload valid sheet; try malformed/oversized file and another client tour ID.', 'Template matches parser; useful errors appear; imported tours belong only to signed-in client.')],
    'TMSClient\\Modal': [('Client modal data', 'Load selection modal and dependent dropdowns for own tour; substitute another client package/tour identifier.', 'Only permitted supplier/package options are returned; cross-client data never appears in HTML or JSON.')],
    'TMSSupplier\\Login': [('Supplier portal isolation', 'Login supplier A/B separately; inspect home; try client/staff routes; logout and revisit home.', 'Only supplier-owned records are visible; portal boundaries hold; logged-out requests require authentication.')],
    'TMSSupplier\\Offer': [('Supplier offer comment', 'Post comment for own offer; try another supplier offer ID and unauthenticated post.', 'Only authorized supplier can comment; unrelated offers remain unchanged; text is safely rendered.')],
    'Api\\TourApi': [('API tour boundaries', 'POST name lengths 2/3/191/192, pax 0/1/500/501, free pax above pax, past/reversed dates and foreign IDs.', 'Documented validation rejects boundary violations with structured errors; valid boundary inputs persist.'), ('API bulk and clone', 'Clone fixture; bulk-update mixed owned/unowned/missing IDs; retry request; inspect calendars/statistics.', 'Ownership holds per record; errors explain partial versus atomic outcome; no unintended duplicates; summaries match source data.')],
    'Api\\ClientApi': [('Client API search and ownership', 'Create/update test client; search with filters and paging; request tours for unauthorized client.', 'Consistent response schema and filtered counts; no unauthorized client/tour data disclosure.')],
    'Api\\UserApi': [('User API field restrictions', 'Update permitted profile fields; submit role/password hashes and another user ID; inspect tours/tasks responses.', 'Only authorized fields/records change; hashes/tokens never returned; related records are permission-scoped.')],
    'Api\\TaskApi': [('API task status and overdue', 'Create due/overdue tasks; PATCH valid and unknown statuses; update as unrelated user.', 'Overdue dates and status transitions are correct; invalid/unauthorized updates leave record unchanged.')],
    'Api\\Dashboard': [('Dashboard API totals and caching', 'Compare charts/statistics/deadlines to fixtures; mutate a task/tour; clear cache as authorized and unauthorized user.', 'Scoped totals update after intended invalidation; unauthorized cache operation is rejected; schema stays stable.')],
    'Api\\SettingsApi': [('Cache operations authorization', 'Inspect cache status; clear and warm sandbox cache as admin; repeat as ordinary user and guest.', 'Only authorized operators mutate cache; status reports accurately; no secrets or user-data leakage.')],
    'Api\\Emails': [('Mailbox user-ID isolation', 'Use sandbox A/B mailboxes; change users/{userId} and email IDs for list/get/send/move/delete/attachments.', 'Caller cannot read or mutate another mailbox; messages are sent only through authorized sandbox identity.')],
    'System endpoints': [('Maintenance and broadcast access', 'Check maintenance endpoints as guest/regular user/admin in staging; authorize private channel as member/nonmember.', 'Maintenance is restricted to intended operators; private channels require valid membership; no operational secrets leak.')],
}

# Supplier and reference modules receive their own explicit domain case.
for module in ['Hotel', 'Event', 'Guide', 'Restaurant', 'Cruises', 'Flight']:
    focused[module] = [('Supplier data and dependencies', f'Create {module} with location, contact and applicable rates/attachments; link to tour service; edit; attempt deletion while linked.', 'Supplier-specific details persist; dropdowns resolve correct record; used supplier cannot be removed leaving broken tour services.')]
for module in ['Criteria', 'Currencies', 'CurrencyRate', 'Rate', 'RoomTypes', 'Status']:
    focused[module] = [('Reference data consistency', f'Create valid {module} reference; attempt duplicate/invalid value; use in dependent form; edit then attempt delete while in use.', 'Valid reference appears in dependent selectors; validation applies; existing records retain consistent meaning and links.')]
focused['CurrencyRate'].append(('Conversion precision', 'Use known base/quote currencies and fractional exchange rate; try zero/negative rate; convert a known invoice amount.', 'Conversion direction and rounding match independent arithmetic; invalid rates rejected; historical amounts follow documented rate policy.'))

fields = ['ID', 'Module', 'Type', 'Priority', 'Scenario', 'Preconditions', 'Steps and data', 'Expected result', 'Route', 'Status', 'Actual result', 'Evidence', 'Defect ID']
cases = []

def add(module, kind, priority, scenario, steps, expected, route='', pre='Isolated QA database; authorized test role and owned fixture records.'):
    cases.append(dict(zip(fields, [f'TC-{len(cases)+1:04d}', module, kind, priority, scenario, pre, steps, expected, route, 'Not run', '', '', ''])))

for module, entries in sorted(groups.items()):
    methods = {r['action'].split('@')[-1].lower() for r in entries}
    add(module, 'Permissions', 'P0', 'Guest, limited-role and record ownership access', 'Call module URLs as guest, authorized role and role without permission; replace IDs with another tenant/user records; compare response and database.', 'Protected requests require login/API authentication and permission; cross-owner access is rejected; no data mutation or disclosure. Public routes expose only their approved public scope.')
    add(module, 'UI/API', 'P1', 'Presentation and response contract', 'Open available screens at 1440px and 390px; use keyboard Tab/Enter/Escape; inspect labels, errors, console and network. For API-only module inspect JSON headers/schema instead.', 'Readable layout and reachable controls; no clipped actions or blocking JavaScript errors; APIs have documented schema/content type and no stack traces.')
    if methods & {'index', 'search', 'datatable'}:
        add(module, 'Functional', 'P1', 'List, search, sort and pagination', 'Seed at least 25 records with mixed text/numeric names; search a match and no match; sort both directions; change page/size; clear filters; reload.', 'Rows/counts match filters; correct sort order; pagination stable; useful empty state. Apply only controls exposed by this module; record unsupported controls as N/A.')
    if methods & {'store', 'update', 'save'}:
        add(module, 'Functional', 'P1', 'Create and edit persistence', 'Create valid unique QA fixture using exposed form/API; refresh detail/list; edit one field; refresh again; inspect related records.', 'One record is created; intended edit persists; related records and totals stay consistent; success feedback and navigation work.')
        add(module, 'Validation', 'P1', 'Required fields, boundaries and unexpected input', 'Submit empty required fields, whitespace, duplicate unique values, maximum length +/-1, invalid dates/numbers/foreign IDs and extra privileged fields; resubmit valid input.', 'Server rejects invalid input with field-specific feedback and no partial write; permitted values save; privileged extra fields cannot escalate access. Derive exact limits from module request rules before execution.')
        add(module, 'Resilience', 'P1', 'Retry and failed-save behavior', 'Double-submit intended creation; simulate slow/failing request; refresh/back; retry after failure; inspect row count and dependencies.', 'Feedback is visible; entered values recoverable; no unintended duplicate or partial business transaction; retry outcome is clear.')
    if methods & {'destroy', 'delete', 'deletemsg'}:
        add(module, 'Integrity', 'P0', 'Delete confirmation and dependency safety', 'Cancel deletion of disposable fixture; confirm deletion of unused fixture; attempt deleting linked fixture and missing ID; repeat deletion.', 'Cancel preserves record; allowed delete removes only selected fixture; in-use dependencies are protected or follow explicitly approved cascade; repeat/missing IDs never cause server error.')
    for scenario, steps, expected in focused.get(module, []):
        add(module, 'Domain', 'P0' if any(s in scenario.lower() for s in ['payment', 'isolation', 'integrity', 'permission', 'overlap']) else 'P1', scenario, steps, expected)
    for route in entries:
        spec = f"{route['method']} /{route['uri'].lstrip('/')}"
        action = route['action'].split('@')[-1]
        add(module, 'Endpoint', 'P1', f'{action}: route contract', f'Prepare owned fixture and valid payload for {action}; substitute every path parameter; call each registered method separately. Check route dispatch, intended HTML/JSON/file/redirect and database effect. Repeat with invalid/missing ID and malformed input where accepted.', f'Dispatch reaches intended {action} action without 500/missing view/method errors; response and side effects match its business purpose; invalid requests produce controlled errors with no unintended changes.', spec)

for scenario, steps, expected in [
    ('Google Places configuration and fallback', 'Test no key, valid restricted sandbox key, rejected key and blocked provider; choose city then change country; open repeated dynamic forms.', 'Manual entry remains usable when unavailable; valid autocomplete writes correct city/place ID; country changes clear stale data; widgets initialize once.'),
    ('Pusher notification delivery', 'Test no credentials, valid sandbox credentials and disconnect/reconnect; deliver event to A/B sessions on localhost with port and staging domain.', 'App remains usable without provider; only intended users receive notification once; configured key/cluster are used; secret remains server-side.'),
    ('Cross-module booking to settlement', 'Create client and supplier; create tour/quote; add services and offer; confirm booking; assign bus/driver; generate invoice; pay; inspect reports and history.', 'All relationships and statuses remain consistent; availability holds; invoice/payment/report totals reconcile; no duplicate financial transaction.'),
    ('Accessibility and locale', 'Keyboard-test menu, modal, form errors and tables; check zoom 200%, Unicode, long names and date/currency formatting across modules.', 'Visible focus and meaningful labels; modal focus can exit; layout remains operable; no raw translation keys; locale formatting is consistent.'),
    ('Session expiry and request protection', 'Expire session during form edit; submit missing/invalid CSRF token; revisit saved link after logout; try external redirect parameter.', 'Expired session recovers clearly; cookie-authenticated mutations reject invalid CSRF; protected data stays inaccessible; redirects remain trusted.'),
]:
    add('Cross-module', 'Integration', 'P0', scenario, steps, expected)

with (OUT / 'test-cases.csv').open('w', newline='', encoding='utf-8-sig') as stream:
    writer = csv.DictWriter(stream, fieldnames=fields)
    writer.writeheader()
    writer.writerows(cases)

with (OUT / 'route-coverage.csv').open('w', newline='', encoding='utf-8-sig') as stream:
    writer = csv.writer(stream)
    writer.writerow(['Module', 'Method', 'URI', 'Name', 'Action', 'Middleware', 'Case ID'])
    endpoint_cases = iter(c for c in cases if c['Type'] == 'Endpoint')
    for module, entries in sorted(groups.items()):
        for route in entries:
            writer.writerow([module, route['method'], route['uri'], route['name'], route['action'], '; '.join(route['middleware']), next(endpoint_cases)['ID']])

lines = [
    '# Application test cases', '',
    f'Generated from the registered Laravel routes on {date.today().isoformat()}. This is a manual acceptance/regression suite, not executable PHPUnit coverage or a test execution report. All cases start as **Not run**.', '',
    f'Coverage: **{len(groups)} route/controller groups**, **{sum(map(len, groups.values()))} registered application/system routes**, **{len(cases)} test cases**. Framework debug/ignition routes are excluded; staff, client, supplier, legacy API, modern API and system endpoints are included. Different controllers for the same business module are retained separately to make permission and portal coverage visible.', '',
    '## Execution setup', '',
    '- Use an isolated staging/QA database and storage with disposable QA-prefixed records. Do not run destructive cases against live records. Existing PHPUnit tests may use database resets; inspect their database configuration before running them.',
    '- Prepare administrator, limited/read-only staff, two client accounts, two supplier accounts and two chat/email users. Use sandbox mailboxes or a local mail sink; send only to controlled test recipients.',
    '- Seed related countries/cities, suppliers, room types, currencies/rates, status values, tour days, invoices and bus/driver assignments. Use relative future dates; include leap day, month/year boundaries and timezone edges.',
    '- Derive exact required fields, permitted statuses, length limits and response shapes from current request/controller rules. Acceptance targets below flag discrepancies; they do not assert that the current implementation already meets them.',
    '- Run P0 cases first, then P1. Repeat UI cases on desktop and mobile; test Chromium plus a second supported browser. Use provider sandbox credentials for live Google/Pusher/Calendar checks; mark unavailable checks Blocked with reason.',
    '- In test-cases.csv fill Status (Pass/Fail/Blocked/N/A), Actual result, Evidence and Defect ID. Record build, browser, tester and date with evidence. N/A requires a reason. Verify cleanup and financial/relationship totals after mutations.', '',
    '## Files', '',
    '- [test-cases.csv](test-cases.csv): individual cases, priorities, preconditions, steps, expected results and execution columns.',
    '- [route-coverage.csv](route-coverage.csv): every included registered route mapped to an endpoint case, including middleware.',
    '- Endpoint rows are route smoke/contract cases. Module/domain cases supply behavior checks; passing route smoke alone does not establish full functional correctness.', '',
    '## Module inventory', '',
    '| Module/controller group | Routes | Cases | Focus |',
    '| --- | ---: | ---: | --- |',
]
for module, entries in sorted(groups.items()):
    count = sum(c['Module'] == module for c in cases)
    focus = '; '.join(x[0] for x in focused.get(module, [])) or 'Baseline and endpoint contracts'
    lines.append(f'| {module} | {len(entries)} | {count} | {focus} |')
lines += ['', '## Detailed module scenarios', '']
for module in sorted(groups):
    lines += [f'### {module}', '']
    for case in cases:
        if case['Module'] == module and case['Type'] != 'Endpoint':
            lines += [f"**{case['ID']} [{case['Priority']}] {case['Scenario']}**", '', f"Steps: {case['Steps and data']}", '', f"Expected: {case['Expected result']}", '']
lines += ['## Cross-module scenarios', '']
for case in cases:
    if case['Module'] == 'Cross-module':
        lines += [f"**{case['ID']} [{case['Priority']}] {case['Scenario']}**", '', f"Steps: {case['Steps and data']}", '', f"Expected: {case['Expected result']}", '']
lines += ['## Existing automation', '',
    'BusValidationTest.php and BusSafetyTest.php contain focused bus regressions; tests/Browser/bus-integrations.cjs checks local fallback and simulated Google/Pusher behavior. The newly generated manual cases are not marked passed by these tests. Other existing tests must be reviewed for scope and database safety before execution.', '',
    '## Regenerating', '',
    'From the repository root:', '', '```powershell',
    'php artisan route:list --json > storage/app/test-case-routes.json',
    'python tests/qa/generate_test_cases.py',
    '```', '',
    'Regeneration overwrites the generated documents and resets result columns. Save completed execution sheets separately first. Route-derived IDs can shift when routes change; link results to the exact build and exported sheet.', '',
]
(OUT / 'TEST_CASES.md').write_text('\n'.join(lines), encoding='utf-8')
assert set(groups) <= set(focused), f'Missing domain coverage: {set(groups) - set(focused)}'
assert len({c['ID'] for c in cases}) == len(cases)
assert sum(c['Type'] == 'Endpoint' for c in cases) == sum(map(len, groups.values()))
print(f'Generated {len(cases)} cases covering {len(groups)} groups and {sum(map(len, groups.values()))} routes in docs/qa.')
