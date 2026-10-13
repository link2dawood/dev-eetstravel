# Application test cases

Generated from the registered Laravel routes on 2026-10-13. This is a manual acceptance/regression suite, not executable PHPUnit coverage or a test execution report. All cases start as **Not run**.

Coverage: **82 route/controller groups**, **786 registered application/system routes**, **1323 test cases**. Framework debug/ignition routes are excluded; staff, client, supplier, legacy API, modern API and system endpoints are included. Different controllers for the same business module are retained separately to make permission and portal coverage visible.

## Execution setup

- Use an isolated staging/QA database and storage with disposable QA-prefixed records. Do not run destructive cases against live records. Existing PHPUnit tests may use database resets; inspect their database configuration before running them.
- Prepare administrator, limited/read-only staff, two client accounts, two supplier accounts and two chat/email users. Use sandbox mailboxes or a local mail sink; send only to controlled test recipients.
- Seed related countries/cities, suppliers, room types, currencies/rates, status values, tour days, invoices and bus/driver assignments. Use relative future dates; include leap day, month/year boundaries and timezone edges.
- Derive exact required fields, permitted statuses, length limits and response shapes from current request/controller rules. Acceptance targets below flag discrepancies; they do not assert that the current implementation already meets them.
- Run P0 cases first, then P1. Repeat UI cases on desktop and mobile; test Chromium plus a second supported browser. Use provider sandbox credentials for live Google/Pusher/Calendar checks; mark unavailable checks Blocked with reason.
- In test-cases.csv fill Status (Pass/Fail/Blocked/N/A), Actual result, Evidence and Defect ID. Record build, browser, tester and date with evidence. N/A requires a reason. Verify cleanup and financial/relationship totals after mutations.

## Files

- [test-cases.csv](test-cases.csv): individual cases, priorities, preconditions, steps, expected results and execution columns.
- [route-coverage.csv](route-coverage.csv): every included registered route mapped to an endpoint case, including middleware.
- Endpoint rows are route smoke/contract cases. Module/domain cases supply behavior checks; passing route smoke alone does not establish full functional correctness.

## Module inventory

| Module/controller group | Routes | Cases | Focus |
| --- | ---: | ---: | --- |
| Activities | 1 | 5 | Activity audit trail |
| Agreements | 9 | 16 | Allotment and room types |
| Announcement | 9 | 17 | Announcement and replies |
| Api\ClientApi | 7 | 15 | Client API search and ownership |
| Api\Dashboard | 15 | 19 | Dashboard API totals and caching |
| Api\Emails | 12 | 15 | Mailbox user-ID isolation |
| Api\SettingsApi | 3 | 6 | Cache operations authorization |
| Api\TaskApi | 8 | 16 | API task status and overdue |
| Api\TourApi | 10 | 19 | API tour boundaries; API bulk and clone |
| Api\UserApi | 6 | 13 | User API field restrictions |
| Attachment | 2 | 6 | Upload validation |
| Auth\ConfirmPassword | 2 | 5 | Password confirmation |
| Auth\ForgotPassword | 2 | 5 | Reset request |
| Auth\Login | 3 | 7 | Login and logout; Invalid login |
| Auth\Register | 2 | 5 | Registration validation |
| Auth\ResetPassword | 2 | 5 | Reset token lifecycle |
| BalanceAmount | 11 | 19 | Balance reconciliation |
| BookingRequest | 6 | 10 | Booking links and responses; Booking emails |
| Bus | 22 | 34 | Unique bus number; Trip overlap; Single-day versus full-trip edit; Delete integrity; Calendar boundaries and async errors |
| Chat | 25 | 33 | Chat membership and messages |
| Client | 13 | 21 | Contacts and geography |
| ClientInvoice | 20 | 29 | Invoice calculation and payment; Payment failure and replay |
| Comment | 11 | 19 | Comment threading and safety |
| Comparison | 8 | 16 | Supplier comparison |
| Criteria | 10 | 18 | Reference data consistency |
| Cruises | 10 | 18 | Supplier data and dependencies |
| Currencies | 10 | 18 | Reference data consistency |
| CurrencyRate | 10 | 19 | Reference data consistency; Conversion precision |
| Driver | 9 | 17 | Driver availability |
| Email | 25 | 30 | Mailbox workflow; Mail failure |
| EmployesSalary | 11 | 19 | Salary allocation |
| Event | 10 | 18 | Supplier data and dependencies |
| Export | 2 | 5 | Export round trip |
| File | 2 | 6 | Download/delete authorization |
| Flight | 10 | 18 | Supplier data and dependencies |
| GuestList | 12 | 20 | Guest list and hotel distribution |
| Guide | 10 | 18 | Supplier data and dependencies |
| Holiday | 10 | 18 | Holiday dates |
| Hotel | 12 | 20 | Supplier data and dependencies |
| Import | 8 | 11 | Import integrity |
| Invoices | 13 | 21 | Supplier invoice and payments |
| Menu | 9 | 17 | Menu permission and ordering |
| Notifications | 6 | 10 | Notification ownership |
| Offer | 15 | 19 | Offer lifecycle and assignment |
| Office | 10 | 18 | Office isolation |
| OfficeEarning | 11 | 19 | Earnings aggregation |
| OfficeInvoice | 14 | 22 | Office invoice details and PDF |
| Profile | 6 | 9 | Profile and scoped tabs |
| Quotation | 15 | 23 | Quote confirmation and export |
| Rate | 10 | 18 | Reference data consistency |
| Reporting | 15 | 23 | Supplier report reconciliation |
| Restaurant | 10 | 18 | Supplier data and dependencies |
| RoomList | 4 | 10 | Room occupancy and distribution |
| RoomTypes | 10 | 18 | Reference data consistency |
| ScaffoldInterface\App | 9 | 12 | Dashboard consistency |
| ScaffoldInterface\Permission | 10 | 18 | Permission enforcement |
| ScaffoldInterface\Role | 12 | 20 | Role permission propagation |
| ScaffoldInterface\User | 13 | 21 | User roles and permissions |
| Seasons | 6 | 13 | Season price boundaries |
| Services | 2 | 6 | Service history |
| Setting | 7 | 15 | Settings persistence |
| SnappyMail | 6 | 9 | Webmail and configuration |
| Status | 10 | 18 | Reference data consistency |
| SupplierSearch | 4 | 8 | Search combinations |
| System endpoints | 4 | 7 | Maintenance and broadcast access |
| TMSClient\Login | 5 | 9 | Client portal isolation |
| TMSClient\Modal | 4 | 7 | Client modal data |
| TMSClient\Tour | 8 | 16 | Client tour workflow |
| TMSClient\TourPackage | 7 | 15 | Client package isolation |
| TMSClient\Upload | 3 | 6 | Client spreadsheet upload |
| TMSSupplier\Login | 4 | 8 | Supplier portal isolation |
| TMSSupplier\Offer | 1 | 4 | Supplier offer comment |
| Task | 13 | 21 | Task lifecycle and calendar |
| Tax | 9 | 17 | Tax arithmetic |
| Templates | 14 | 22 | Template variables and sending |
| Tour | 40 | 51 | Tour lifecycle; Price and passenger boundaries; Confirmed hotel protection; Exports and public page |
| TourExpense | 11 | 19 | Tour expense allocation |
| TourPackage | 33 | 43 | Service scheduling; Hotel dates and room types; Status and cancellation |
| Transaction | 9 | 17 | Ledger reconciliation |
| Transfer | 10 | 18 | Company-dependent resources |
| UtilityExpense | 11 | 19 | Utility expense totals |
| gCalendar | 3 | 6 | Calendar OAuth and sync |

## Detailed module scenarios

### Activities

**TC-0001 [P0] Guest, limited-role and record ownership access**

Steps: Call module URLs as guest, authorized role and role without permission; replace IDs with another tenant/user records; compare response and database.

Expected: Protected requests require login/API authentication and permission; cross-owner access is rejected; no data mutation or disclosure. Public routes expose only their approved public scope.

**TC-0002 [P1] Presentation and response contract**

Steps: Open available screens at 1440px and 390px; use keyboard Tab/Enter/Escape; inspect labels, errors, console and network. For API-only module inspect JSON headers/schema instead.

Expected: Readable layout and reachable controls; no clipped actions or blocking JavaScript errors; APIs have documented schema/content type and no stack traces.

**TC-0003 [P1] List, search, sort and pagination**

Steps: Seed at least 25 records with mixed text/numeric names; search a match and no match; sort both directions; change page/size; clear filters; reload.

Expected: Rows/counts match filters; correct sort order; pagination stable; useful empty state. Apply only controls exposed by this module; record unsupported controls as N/A.

**TC-0004 [P1] Activity audit trail**

Steps: Create/edit/delete disposable records as users A/B; filter or page activity list; inspect as limited staff.

Expected: Entries identify correct actor, record and action; ordering is accurate; sensitive payloads and unauthorized activity remain hidden.

### Agreements

**TC-0006 [P0] Guest, limited-role and record ownership access**

Steps: Call module URLs as guest, authorized role and role without permission; replace IDs with another tenant/user records; compare response and database.

Expected: Protected requests require login/API authentication and permission; cross-owner access is rejected; no data mutation or disclosure. Public routes expose only their approved public scope.

**TC-0007 [P1] Presentation and response contract**

Steps: Open available screens at 1440px and 390px; use keyboard Tab/Enter/Escape; inspect labels, errors, console and network. For API-only module inspect JSON headers/schema instead.

Expected: Readable layout and reachable controls; no clipped actions or blocking JavaScript errors; APIs have documented schema/content type and no stack traces.

**TC-0008 [P1] Create and edit persistence**

Steps: Create valid unique QA fixture using exposed form/API; refresh detail/list; edit one field; refresh again; inspect related records.

Expected: One record is created; intended edit persists; related records and totals stay consistent; success feedback and navigation work.

**TC-0009 [P1] Required fields, boundaries and unexpected input**

Steps: Submit empty required fields, whitespace, duplicate unique values, maximum length +/-1, invalid dates/numbers/foreign IDs and extra privileged fields; resubmit valid input.

Expected: Server rejects invalid input with field-specific feedback and no partial write; permitted values save; privileged extra fields cannot escalate access. Derive exact limits from module request rules before execution.

**TC-0010 [P1] Retry and failed-save behavior**

Steps: Double-submit intended creation; simulate slow/failing request; refresh/back; retry after failure; inspect row count and dependencies.

Expected: Feedback is visible; entered values recoverable; no unintended duplicate or partial business transaction; retry outcome is clear.

**TC-0011 [P0] Delete confirmation and dependency safety**

Steps: Cancel deletion of disposable fixture; confirm deletion of unused fixture; attempt deleting linked fixture and missing ID; repeat deletion.

Expected: Cancel preserves record; allowed delete removes only selected fixture; in-use dependencies are protected or follow explicitly approved cascade; repeat/missing IDs never cause server error.

**TC-0012 [P1] Allotment and room types**

Steps: Create supplier agreement; choose hotel room type; add/edit/delete allotment; test overlapping dates and negative capacity.

Expected: Room types match hotel; capacity and date validation apply; unrelated allotments stay unchanged.

### Announcement

**TC-0022 [P0] Guest, limited-role and record ownership access**

Steps: Call module URLs as guest, authorized role and role without permission; replace IDs with another tenant/user records; compare response and database.

Expected: Protected requests require login/API authentication and permission; cross-owner access is rejected; no data mutation or disclosure. Public routes expose only their approved public scope.

**TC-0023 [P1] Presentation and response contract**

Steps: Open available screens at 1440px and 390px; use keyboard Tab/Enter/Escape; inspect labels, errors, console and network. For API-only module inspect JSON headers/schema instead.

Expected: Readable layout and reachable controls; no clipped actions or blocking JavaScript errors; APIs have documented schema/content type and no stack traces.

**TC-0024 [P1] List, search, sort and pagination**

Steps: Seed at least 25 records with mixed text/numeric names; search a match and no match; sort both directions; change page/size; clear filters; reload.

Expected: Rows/counts match filters; correct sort order; pagination stable; useful empty state. Apply only controls exposed by this module; record unsupported controls as N/A.

**TC-0025 [P1] Create and edit persistence**

Steps: Create valid unique QA fixture using exposed form/API; refresh detail/list; edit one field; refresh again; inspect related records.

Expected: One record is created; intended edit persists; related records and totals stay consistent; success feedback and navigation work.

**TC-0026 [P1] Required fields, boundaries and unexpected input**

Steps: Submit empty required fields, whitespace, duplicate unique values, maximum length +/-1, invalid dates/numbers/foreign IDs and extra privileged fields; resubmit valid input.

Expected: Server rejects invalid input with field-specific feedback and no partial write; permitted values save; privileged extra fields cannot escalate access. Derive exact limits from module request rules before execution.

**TC-0027 [P1] Retry and failed-save behavior**

Steps: Double-submit intended creation; simulate slow/failing request; refresh/back; retry after failure; inspect row count and dependencies.

Expected: Feedback is visible; entered values recoverable; no unintended duplicate or partial business transaction; retry outcome is clear.

**TC-0028 [P0] Delete confirmation and dependency safety**

Steps: Cancel deletion of disposable fixture; confirm deletion of unused fixture; attempt deleting linked fixture and missing ID; repeat deletion.

Expected: Cancel preserves record; allowed delete removes only selected fixture; in-use dependencies are protected or follow explicitly approved cascade; repeat/missing IDs never cause server error.

**TC-0029 [P1] Announcement and replies**

Steps: Create announcement for intended audience; reply; edit; regenerate; view as excluded user.

Expected: Audience restrictions apply; replies attach to correct item; generation does not duplicate unintended records.

### Api\ClientApi

**TC-0039 [P0] Guest, limited-role and record ownership access**

Steps: Call module URLs as guest, authorized role and role without permission; replace IDs with another tenant/user records; compare response and database.

Expected: Protected requests require login/API authentication and permission; cross-owner access is rejected; no data mutation or disclosure. Public routes expose only their approved public scope.

**TC-0040 [P1] Presentation and response contract**

Steps: Open available screens at 1440px and 390px; use keyboard Tab/Enter/Escape; inspect labels, errors, console and network. For API-only module inspect JSON headers/schema instead.

Expected: Readable layout and reachable controls; no clipped actions or blocking JavaScript errors; APIs have documented schema/content type and no stack traces.

**TC-0041 [P1] List, search, sort and pagination**

Steps: Seed at least 25 records with mixed text/numeric names; search a match and no match; sort both directions; change page/size; clear filters; reload.

Expected: Rows/counts match filters; correct sort order; pagination stable; useful empty state. Apply only controls exposed by this module; record unsupported controls as N/A.

**TC-0042 [P1] Create and edit persistence**

Steps: Create valid unique QA fixture using exposed form/API; refresh detail/list; edit one field; refresh again; inspect related records.

Expected: One record is created; intended edit persists; related records and totals stay consistent; success feedback and navigation work.

**TC-0043 [P1] Required fields, boundaries and unexpected input**

Steps: Submit empty required fields, whitespace, duplicate unique values, maximum length +/-1, invalid dates/numbers/foreign IDs and extra privileged fields; resubmit valid input.

Expected: Server rejects invalid input with field-specific feedback and no partial write; permitted values save; privileged extra fields cannot escalate access. Derive exact limits from module request rules before execution.

**TC-0044 [P1] Retry and failed-save behavior**

Steps: Double-submit intended creation; simulate slow/failing request; refresh/back; retry after failure; inspect row count and dependencies.

Expected: Feedback is visible; entered values recoverable; no unintended duplicate or partial business transaction; retry outcome is clear.

**TC-0045 [P0] Delete confirmation and dependency safety**

Steps: Cancel deletion of disposable fixture; confirm deletion of unused fixture; attempt deleting linked fixture and missing ID; repeat deletion.

Expected: Cancel preserves record; allowed delete removes only selected fixture; in-use dependencies are protected or follow explicitly approved cascade; repeat/missing IDs never cause server error.

**TC-0046 [P1] Client API search and ownership**

Steps: Create/update test client; search with filters and paging; request tours for unauthorized client.

Expected: Consistent response schema and filtered counts; no unauthorized client/tour data disclosure.

### Api\Dashboard

**TC-0054 [P0] Guest, limited-role and record ownership access**

Steps: Call module URLs as guest, authorized role and role without permission; replace IDs with another tenant/user records; compare response and database.

Expected: Protected requests require login/API authentication and permission; cross-owner access is rejected; no data mutation or disclosure. Public routes expose only their approved public scope.

**TC-0055 [P1] Presentation and response contract**

Steps: Open available screens at 1440px and 390px; use keyboard Tab/Enter/Escape; inspect labels, errors, console and network. For API-only module inspect JSON headers/schema instead.

Expected: Readable layout and reachable controls; no clipped actions or blocking JavaScript errors; APIs have documented schema/content type and no stack traces.

**TC-0056 [P1] List, search, sort and pagination**

Steps: Seed at least 25 records with mixed text/numeric names; search a match and no match; sort both directions; change page/size; clear filters; reload.

Expected: Rows/counts match filters; correct sort order; pagination stable; useful empty state. Apply only controls exposed by this module; record unsupported controls as N/A.

**TC-0057 [P1] Dashboard API totals and caching**

Steps: Compare charts/statistics/deadlines to fixtures; mutate a task/tour; clear cache as authorized and unauthorized user.

Expected: Scoped totals update after intended invalidation; unauthorized cache operation is rejected; schema stays stable.

### Api\Emails

**TC-0073 [P0] Guest, limited-role and record ownership access**

Steps: Call module URLs as guest, authorized role and role without permission; replace IDs with another tenant/user records; compare response and database.

Expected: Protected requests require login/API authentication and permission; cross-owner access is rejected; no data mutation or disclosure. Public routes expose only their approved public scope.

**TC-0074 [P1] Presentation and response contract**

Steps: Open available screens at 1440px and 390px; use keyboard Tab/Enter/Escape; inspect labels, errors, console and network. For API-only module inspect JSON headers/schema instead.

Expected: Readable layout and reachable controls; no clipped actions or blocking JavaScript errors; APIs have documented schema/content type and no stack traces.

**TC-0075 [P0] Mailbox user-ID isolation**

Steps: Use sandbox A/B mailboxes; change users/{userId} and email IDs for list/get/send/move/delete/attachments.

Expected: Caller cannot read or mutate another mailbox; messages are sent only through authorized sandbox identity.

### Api\SettingsApi

**TC-0088 [P0] Guest, limited-role and record ownership access**

Steps: Call module URLs as guest, authorized role and role without permission; replace IDs with another tenant/user records; compare response and database.

Expected: Protected requests require login/API authentication and permission; cross-owner access is rejected; no data mutation or disclosure. Public routes expose only their approved public scope.

**TC-0089 [P1] Presentation and response contract**

Steps: Open available screens at 1440px and 390px; use keyboard Tab/Enter/Escape; inspect labels, errors, console and network. For API-only module inspect JSON headers/schema instead.

Expected: Readable layout and reachable controls; no clipped actions or blocking JavaScript errors; APIs have documented schema/content type and no stack traces.

**TC-0090 [P1] Cache operations authorization**

Steps: Inspect cache status; clear and warm sandbox cache as admin; repeat as ordinary user and guest.

Expected: Only authorized operators mutate cache; status reports accurately; no secrets or user-data leakage.

### Api\TaskApi

**TC-0094 [P0] Guest, limited-role and record ownership access**

Steps: Call module URLs as guest, authorized role and role without permission; replace IDs with another tenant/user records; compare response and database.

Expected: Protected requests require login/API authentication and permission; cross-owner access is rejected; no data mutation or disclosure. Public routes expose only their approved public scope.

**TC-0095 [P1] Presentation and response contract**

Steps: Open available screens at 1440px and 390px; use keyboard Tab/Enter/Escape; inspect labels, errors, console and network. For API-only module inspect JSON headers/schema instead.

Expected: Readable layout and reachable controls; no clipped actions or blocking JavaScript errors; APIs have documented schema/content type and no stack traces.

**TC-0096 [P1] List, search, sort and pagination**

Steps: Seed at least 25 records with mixed text/numeric names; search a match and no match; sort both directions; change page/size; clear filters; reload.

Expected: Rows/counts match filters; correct sort order; pagination stable; useful empty state. Apply only controls exposed by this module; record unsupported controls as N/A.

**TC-0097 [P1] Create and edit persistence**

Steps: Create valid unique QA fixture using exposed form/API; refresh detail/list; edit one field; refresh again; inspect related records.

Expected: One record is created; intended edit persists; related records and totals stay consistent; success feedback and navigation work.

**TC-0098 [P1] Required fields, boundaries and unexpected input**

Steps: Submit empty required fields, whitespace, duplicate unique values, maximum length +/-1, invalid dates/numbers/foreign IDs and extra privileged fields; resubmit valid input.

Expected: Server rejects invalid input with field-specific feedback and no partial write; permitted values save; privileged extra fields cannot escalate access. Derive exact limits from module request rules before execution.

**TC-0099 [P1] Retry and failed-save behavior**

Steps: Double-submit intended creation; simulate slow/failing request; refresh/back; retry after failure; inspect row count and dependencies.

Expected: Feedback is visible; entered values recoverable; no unintended duplicate or partial business transaction; retry outcome is clear.

**TC-0100 [P0] Delete confirmation and dependency safety**

Steps: Cancel deletion of disposable fixture; confirm deletion of unused fixture; attempt deleting linked fixture and missing ID; repeat deletion.

Expected: Cancel preserves record; allowed delete removes only selected fixture; in-use dependencies are protected or follow explicitly approved cascade; repeat/missing IDs never cause server error.

**TC-0101 [P1] API task status and overdue**

Steps: Create due/overdue tasks; PATCH valid and unknown statuses; update as unrelated user.

Expected: Overdue dates and status transitions are correct; invalid/unauthorized updates leave record unchanged.

### Api\TourApi

**TC-0110 [P0] Guest, limited-role and record ownership access**

Steps: Call module URLs as guest, authorized role and role without permission; replace IDs with another tenant/user records; compare response and database.

Expected: Protected requests require login/API authentication and permission; cross-owner access is rejected; no data mutation or disclosure. Public routes expose only their approved public scope.

**TC-0111 [P1] Presentation and response contract**

Steps: Open available screens at 1440px and 390px; use keyboard Tab/Enter/Escape; inspect labels, errors, console and network. For API-only module inspect JSON headers/schema instead.

Expected: Readable layout and reachable controls; no clipped actions or blocking JavaScript errors; APIs have documented schema/content type and no stack traces.

**TC-0112 [P1] List, search, sort and pagination**

Steps: Seed at least 25 records with mixed text/numeric names; search a match and no match; sort both directions; change page/size; clear filters; reload.

Expected: Rows/counts match filters; correct sort order; pagination stable; useful empty state. Apply only controls exposed by this module; record unsupported controls as N/A.

**TC-0113 [P1] Create and edit persistence**

Steps: Create valid unique QA fixture using exposed form/API; refresh detail/list; edit one field; refresh again; inspect related records.

Expected: One record is created; intended edit persists; related records and totals stay consistent; success feedback and navigation work.

**TC-0114 [P1] Required fields, boundaries and unexpected input**

Steps: Submit empty required fields, whitespace, duplicate unique values, maximum length +/-1, invalid dates/numbers/foreign IDs and extra privileged fields; resubmit valid input.

Expected: Server rejects invalid input with field-specific feedback and no partial write; permitted values save; privileged extra fields cannot escalate access. Derive exact limits from module request rules before execution.

**TC-0115 [P1] Retry and failed-save behavior**

Steps: Double-submit intended creation; simulate slow/failing request; refresh/back; retry after failure; inspect row count and dependencies.

Expected: Feedback is visible; entered values recoverable; no unintended duplicate or partial business transaction; retry outcome is clear.

**TC-0116 [P0] Delete confirmation and dependency safety**

Steps: Cancel deletion of disposable fixture; confirm deletion of unused fixture; attempt deleting linked fixture and missing ID; repeat deletion.

Expected: Cancel preserves record; allowed delete removes only selected fixture; in-use dependencies are protected or follow explicitly approved cascade; repeat/missing IDs never cause server error.

**TC-0117 [P1] API tour boundaries**

Steps: POST name lengths 2/3/191/192, pax 0/1/500/501, free pax above pax, past/reversed dates and foreign IDs.

Expected: Documented validation rejects boundary violations with structured errors; valid boundary inputs persist.

**TC-0118 [P1] API bulk and clone**

Steps: Clone fixture; bulk-update mixed owned/unowned/missing IDs; retry request; inspect calendars/statistics.

Expected: Ownership holds per record; errors explain partial versus atomic outcome; no unintended duplicates; summaries match source data.

### Api\UserApi

**TC-0129 [P0] Guest, limited-role and record ownership access**

Steps: Call module URLs as guest, authorized role and role without permission; replace IDs with another tenant/user records; compare response and database.

Expected: Protected requests require login/API authentication and permission; cross-owner access is rejected; no data mutation or disclosure. Public routes expose only their approved public scope.

**TC-0130 [P1] Presentation and response contract**

Steps: Open available screens at 1440px and 390px; use keyboard Tab/Enter/Escape; inspect labels, errors, console and network. For API-only module inspect JSON headers/schema instead.

Expected: Readable layout and reachable controls; no clipped actions or blocking JavaScript errors; APIs have documented schema/content type and no stack traces.

**TC-0131 [P1] List, search, sort and pagination**

Steps: Seed at least 25 records with mixed text/numeric names; search a match and no match; sort both directions; change page/size; clear filters; reload.

Expected: Rows/counts match filters; correct sort order; pagination stable; useful empty state. Apply only controls exposed by this module; record unsupported controls as N/A.

**TC-0132 [P1] Create and edit persistence**

Steps: Create valid unique QA fixture using exposed form/API; refresh detail/list; edit one field; refresh again; inspect related records.

Expected: One record is created; intended edit persists; related records and totals stay consistent; success feedback and navigation work.

**TC-0133 [P1] Required fields, boundaries and unexpected input**

Steps: Submit empty required fields, whitespace, duplicate unique values, maximum length +/-1, invalid dates/numbers/foreign IDs and extra privileged fields; resubmit valid input.

Expected: Server rejects invalid input with field-specific feedback and no partial write; permitted values save; privileged extra fields cannot escalate access. Derive exact limits from module request rules before execution.

**TC-0134 [P1] Retry and failed-save behavior**

Steps: Double-submit intended creation; simulate slow/failing request; refresh/back; retry after failure; inspect row count and dependencies.

Expected: Feedback is visible; entered values recoverable; no unintended duplicate or partial business transaction; retry outcome is clear.

**TC-0135 [P1] User API field restrictions**

Steps: Update permitted profile fields; submit role/password hashes and another user ID; inspect tours/tasks responses.

Expected: Only authorized fields/records change; hashes/tokens never returned; related records are permission-scoped.

### Attachment

**TC-0142 [P0] Guest, limited-role and record ownership access**

Steps: Call module URLs as guest, authorized role and role without permission; replace IDs with another tenant/user records; compare response and database.

Expected: Protected requests require login/API authentication and permission; cross-owner access is rejected; no data mutation or disclosure. Public routes expose only their approved public scope.

**TC-0143 [P1] Presentation and response contract**

Steps: Open available screens at 1440px and 390px; use keyboard Tab/Enter/Escape; inspect labels, errors, console and network. For API-only module inspect JSON headers/schema instead.

Expected: Readable layout and reachable controls; no clipped actions or blocking JavaScript errors; APIs have documented schema/content type and no stack traces.

**TC-0144 [P1] List, search, sort and pagination**

Steps: Seed at least 25 records with mixed text/numeric names; search a match and no match; sort both directions; change page/size; clear filters; reload.

Expected: Rows/counts match filters; correct sort order; pagination stable; useful empty state. Apply only controls exposed by this module; record unsupported controls as N/A.

**TC-0145 [P1] Upload validation**

Steps: Upload small image/PDF; try oversized file, disguised executable, duplicate filename and interrupted upload.

Expected: Allowed files store correctly; prohibited/oversized files rejected; no executable serving or partial records.

### Auth\ConfirmPassword

**TC-0148 [P0] Guest, limited-role and record ownership access**

Steps: Call module URLs as guest, authorized role and role without permission; replace IDs with another tenant/user records; compare response and database.

Expected: Protected requests require login/API authentication and permission; cross-owner access is rejected; no data mutation or disclosure. Public routes expose only their approved public scope.

**TC-0149 [P1] Presentation and response contract**

Steps: Open available screens at 1440px and 390px; use keyboard Tab/Enter/Escape; inspect labels, errors, console and network. For API-only module inspect JSON headers/schema instead.

Expected: Readable layout and reachable controls; no clipped actions or blocking JavaScript errors; APIs have documented schema/content type and no stack traces.

**TC-0150 [P1] Password confirmation**

Steps: Confirm with wrong then correct password; revisit a confirmation-protected action.

Expected: Wrong password is rejected; correct confirmation grants only the intended temporary access.

### Auth\ForgotPassword

**TC-0153 [P0] Guest, limited-role and record ownership access**

Steps: Call module URLs as guest, authorized role and role without permission; replace IDs with another tenant/user records; compare response and database.

Expected: Protected requests require login/API authentication and permission; cross-owner access is rejected; no data mutation or disclosure. Public routes expose only their approved public scope.

**TC-0154 [P1] Presentation and response contract**

Steps: Open available screens at 1440px and 390px; use keyboard Tab/Enter/Escape; inspect labels, errors, console and network. For API-only module inspect JSON headers/schema instead.

Expected: Readable layout and reachable controls; no clipped actions or blocking JavaScript errors; APIs have documented schema/content type and no stack traces.

**TC-0155 [P1] Reset request**

Steps: Request reset for existing and unknown accounts using a mail sink; repeat requests.

Expected: Responses do not enumerate accounts; valid reset mail arrives only in the sink; rate limit applies.

### Auth\Login

**TC-0158 [P0] Guest, limited-role and record ownership access**

Steps: Call module URLs as guest, authorized role and role without permission; replace IDs with another tenant/user records; compare response and database.

Expected: Protected requests require login/API authentication and permission; cross-owner access is rejected; no data mutation or disclosure. Public routes expose only their approved public scope.

**TC-0159 [P1] Presentation and response contract**

Steps: Open available screens at 1440px and 390px; use keyboard Tab/Enter/Escape; inspect labels, errors, console and network. For API-only module inspect JSON headers/schema instead.

Expected: Readable layout and reachable controls; no clipped actions or blocking JavaScript errors; APIs have documented schema/content type and no stack traces.

**TC-0160 [P1] Login and logout**

Steps: Sign in with a valid test account; refresh; logout; revisit a protected URL and use browser Back.

Expected: Session survives refresh; logout removes access; cached pages expose no protected data.

**TC-0161 [P1] Invalid login**

Steps: Try wrong password, unknown email, empty fields and repeated failures.

Expected: Useful errors without account/password disclosure; configured rate limit applies.

### Auth\Register

**TC-0165 [P0] Guest, limited-role and record ownership access**

Steps: Call module URLs as guest, authorized role and role without permission; replace IDs with another tenant/user records; compare response and database.

Expected: Protected requests require login/API authentication and permission; cross-owner access is rejected; no data mutation or disclosure. Public routes expose only their approved public scope.

**TC-0166 [P1] Presentation and response contract**

Steps: Open available screens at 1440px and 390px; use keyboard Tab/Enter/Escape; inspect labels, errors, console and network. For API-only module inspect JSON headers/schema instead.

Expected: Readable layout and reachable controls; no clipped actions or blocking JavaScript errors; APIs have documented schema/content type and no stack traces.

**TC-0167 [P1] Registration validation**

Steps: Try valid test registration, duplicate email, malformed email, weak password and mismatched confirmation.

Expected: Only valid registration succeeds; password policy and duplicate checks apply; no elevated role can be submitted.

### Auth\ResetPassword

**TC-0170 [P0] Guest, limited-role and record ownership access**

Steps: Call module URLs as guest, authorized role and role without permission; replace IDs with another tenant/user records; compare response and database.

Expected: Protected requests require login/API authentication and permission; cross-owner access is rejected; no data mutation or disclosure. Public routes expose only their approved public scope.

**TC-0171 [P1] Presentation and response contract**

Steps: Open available screens at 1440px and 390px; use keyboard Tab/Enter/Escape; inspect labels, errors, console and network. For API-only module inspect JSON headers/schema instead.

Expected: Readable layout and reachable controls; no clipped actions or blocking JavaScript errors; APIs have documented schema/content type and no stack traces.

**TC-0172 [P1] Reset token lifecycle**

Steps: Reset with valid, altered, expired and already-used tokens; try mismatched password confirmation.

Expected: Only a valid unused token resets the intended account; invalid requests preserve the old password.

### BalanceAmount

**TC-0175 [P0] Guest, limited-role and record ownership access**

Steps: Call module URLs as guest, authorized role and role without permission; replace IDs with another tenant/user records; compare response and database.

Expected: Protected requests require login/API authentication and permission; cross-owner access is rejected; no data mutation or disclosure. Public routes expose only their approved public scope.

**TC-0176 [P1] Presentation and response contract**

Steps: Open available screens at 1440px and 390px; use keyboard Tab/Enter/Escape; inspect labels, errors, console and network. For API-only module inspect JSON headers/schema instead.

Expected: Readable layout and reachable controls; no clipped actions or blocking JavaScript errors; APIs have documented schema/content type and no stack traces.

**TC-0177 [P1] List, search, sort and pagination**

Steps: Seed at least 25 records with mixed text/numeric names; search a match and no match; sort both directions; change page/size; clear filters; reload.

Expected: Rows/counts match filters; correct sort order; pagination stable; useful empty state. Apply only controls exposed by this module; record unsupported controls as N/A.

**TC-0178 [P1] Create and edit persistence**

Steps: Create valid unique QA fixture using exposed form/API; refresh detail/list; edit one field; refresh again; inspect related records.

Expected: One record is created; intended edit persists; related records and totals stay consistent; success feedback and navigation work.

**TC-0179 [P1] Required fields, boundaries and unexpected input**

Steps: Submit empty required fields, whitespace, duplicate unique values, maximum length +/-1, invalid dates/numbers/foreign IDs and extra privileged fields; resubmit valid input.

Expected: Server rejects invalid input with field-specific feedback and no partial write; permitted values save; privileged extra fields cannot escalate access. Derive exact limits from module request rules before execution.

**TC-0180 [P1] Retry and failed-save behavior**

Steps: Double-submit intended creation; simulate slow/failing request; refresh/back; retry after failure; inspect row count and dependencies.

Expected: Feedback is visible; entered values recoverable; no unintended duplicate or partial business transaction; retry outcome is clear.

**TC-0181 [P0] Delete confirmation and dependency safety**

Steps: Cancel deletion of disposable fixture; confirm deletion of unused fixture; attempt deleting linked fixture and missing ID; repeat deletion.

Expected: Cancel preserves record; allowed delete removes only selected fixture; in-use dependencies are protected or follow explicitly approved cascade; repeat/missing IDs never cause server error.

**TC-0182 [P1] Balance reconciliation**

Steps: Seed known opening balance, earnings, expenses and payments; inspect balance then edit an entry.

Expected: Displayed balance equals independently calculated opening balance plus income minus outgoings, under documented sign convention.

### BookingRequest

**TC-0194 [P0] Guest, limited-role and record ownership access**

Steps: Call module URLs as guest, authorized role and role without permission; replace IDs with another tenant/user records; compare response and database.

Expected: Protected requests require login/API authentication and permission; cross-owner access is rejected; no data mutation or disclosure. Public routes expose only their approved public scope.

**TC-0195 [P1] Presentation and response contract**

Steps: Open available screens at 1440px and 390px; use keyboard Tab/Enter/Escape; inspect labels, errors, console and network. For API-only module inspect JSON headers/schema instead.

Expected: Readable layout and reachable controls; no clipped actions or blocking JavaScript errors; APIs have documented schema/content type and no stack traces.

**TC-0196 [P1] Booking links and responses**

Steps: Generate supplier link; respond as intended supplier; alter encrypted token and package ID; submit twice.

Expected: Valid response updates only intended offer; tampered/cross-supplier access rejected; retries do not duplicate booking.

**TC-0197 [P1] Booking emails**

Steps: Add valid/invalid recipient addresses and open add-email form; use test mail sink for any send.

Expected: Validation identifies bad addresses; only selected recipients and intended package data appear.

### Bus

**TC-0204 [P0] Guest, limited-role and record ownership access**

Steps: Call module URLs as guest, authorized role and role without permission; replace IDs with another tenant/user records; compare response and database.

Expected: Protected requests require login/API authentication and permission; cross-owner access is rejected; no data mutation or disclosure. Public routes expose only their approved public scope.

**TC-0205 [P1] Presentation and response contract**

Steps: Open available screens at 1440px and 390px; use keyboard Tab/Enter/Escape; inspect labels, errors, console and network. For API-only module inspect JSON headers/schema instead.

Expected: Readable layout and reachable controls; no clipped actions or blocking JavaScript errors; APIs have documented schema/content type and no stack traces.

**TC-0206 [P1] List, search, sort and pagination**

Steps: Seed at least 25 records with mixed text/numeric names; search a match and no match; sort both directions; change page/size; clear filters; reload.

Expected: Rows/counts match filters; correct sort order; pagination stable; useful empty state. Apply only controls exposed by this module; record unsupported controls as N/A.

**TC-0207 [P1] Create and edit persistence**

Steps: Create valid unique QA fixture using exposed form/API; refresh detail/list; edit one field; refresh again; inspect related records.

Expected: One record is created; intended edit persists; related records and totals stay consistent; success feedback and navigation work.

**TC-0208 [P1] Required fields, boundaries and unexpected input**

Steps: Submit empty required fields, whitespace, duplicate unique values, maximum length +/-1, invalid dates/numbers/foreign IDs and extra privileged fields; resubmit valid input.

Expected: Server rejects invalid input with field-specific feedback and no partial write; permitted values save; privileged extra fields cannot escalate access. Derive exact limits from module request rules before execution.

**TC-0209 [P1] Retry and failed-save behavior**

Steps: Double-submit intended creation; simulate slow/failing request; refresh/back; retry after failure; inspect row count and dependencies.

Expected: Feedback is visible; entered values recoverable; no unintended duplicate or partial business transaction; retry outcome is clear.

**TC-0210 [P0] Delete confirmation and dependency safety**

Steps: Cancel deletion of disposable fixture; confirm deletion of unused fixture; attempt deleting linked fixture and missing ID; repeat deletion.

Expected: Cancel preserves record; allowed delete removes only selected fixture; in-use dependencies are protected or follow explicitly approved cascade; repeat/missing IDs never cause server error.

**TC-0211 [P1] Unique bus number**

Steps: Create bus QA-BUS-001; repeat number; edit same bus without changing number; change to another bus number.

Expected: Duplicate create/update rejected; unchanged unique number accepted; no partial attachment save.

**TC-0212 [P0] Trip overlap**

Steps: Assign bus and driver across several days; try overlapping dates and conflicting driver; edit original trip.

Expected: Conflicts rejected across the whole interval; trip excludes itself during availability checks.

**TC-0213 [P1] Single-day versus full-trip edit**

Steps: Edit one day of a multi-day trip, then edit whole trip; repeat for tour-owned bus days.

Expected: Single-day changes leave other days untouched; whole-trip changes update only the selected trip.

**TC-0214 [P0] Delete integrity**

Steps: Delete an unused bus; attempt deleting a scheduled bus; delete one day and then one full trip.

Expected: Unused bus removed; scheduled bus preserved; deletion scope is correct; no orphan records.

**TC-0215 [P1] Calendar boundaries and async errors**

Steps: Navigate Dec/Jan, leap February, filters and legend; delay modal load and trigger save; simulate failed request.

Expected: Correct month/day alignment; filters update current data; stale form cannot save; failures are visible.

### Chat

**TC-0238 [P0] Guest, limited-role and record ownership access**

Steps: Call module URLs as guest, authorized role and role without permission; replace IDs with another tenant/user records; compare response and database.

Expected: Protected requests require login/API authentication and permission; cross-owner access is rejected; no data mutation or disclosure. Public routes expose only their approved public scope.

**TC-0239 [P1] Presentation and response contract**

Steps: Open available screens at 1440px and 390px; use keyboard Tab/Enter/Escape; inspect labels, errors, console and network. For API-only module inspect JSON headers/schema instead.

Expected: Readable layout and reachable controls; no clipped actions or blocking JavaScript errors; APIs have documented schema/content type and no stack traces.

**TC-0240 [P1] List, search, sort and pagination**

Steps: Seed at least 25 records with mixed text/numeric names; search a match and no match; sort both directions; change page/size; clear filters; reload.

Expected: Rows/counts match filters; correct sort order; pagination stable; useful empty state. Apply only controls exposed by this module; record unsupported controls as N/A.

**TC-0241 [P1] Create and edit persistence**

Steps: Create valid unique QA fixture using exposed form/API; refresh detail/list; edit one field; refresh again; inspect related records.

Expected: One record is created; intended edit persists; related records and totals stay consistent; success feedback and navigation work.

**TC-0242 [P1] Required fields, boundaries and unexpected input**

Steps: Submit empty required fields, whitespace, duplicate unique values, maximum length +/-1, invalid dates/numbers/foreign IDs and extra privileged fields; resubmit valid input.

Expected: Server rejects invalid input with field-specific feedback and no partial write; permitted values save; privileged extra fields cannot escalate access. Derive exact limits from module request rules before execution.

**TC-0243 [P1] Retry and failed-save behavior**

Steps: Double-submit intended creation; simulate slow/failing request; refresh/back; retry after failure; inspect row count and dependencies.

Expected: Feedback is visible; entered values recoverable; no unintended duplicate or partial business transaction; retry outcome is clear.

**TC-0244 [P0] Delete confirmation and dependency safety**

Steps: Cancel deletion of disposable fixture; confirm deletion of unused fixture; attempt deleting linked fixture and missing ID; repeat deletion.

Expected: Cancel preserves record; allowed delete removes only selected fixture; in-use dependencies are protected or follow explicitly approved cascade; repeat/missing IDs never cause server error.

**TC-0245 [P1] Chat membership and messages**

Steps: Create direct and group chats with test users A/B; add/remove B; send text; request removed chat via direct URL.

Expected: Messages stay in intended chat; removed/nonmember users cannot read or post; membership updates consistently.

### Client

**TC-0271 [P0] Guest, limited-role and record ownership access**

Steps: Call module URLs as guest, authorized role and role without permission; replace IDs with another tenant/user records; compare response and database.

Expected: Protected requests require login/API authentication and permission; cross-owner access is rejected; no data mutation or disclosure. Public routes expose only their approved public scope.

**TC-0272 [P1] Presentation and response contract**

Steps: Open available screens at 1440px and 390px; use keyboard Tab/Enter/Escape; inspect labels, errors, console and network. For API-only module inspect JSON headers/schema instead.

Expected: Readable layout and reachable controls; no clipped actions or blocking JavaScript errors; APIs have documented schema/content type and no stack traces.

**TC-0273 [P1] List, search, sort and pagination**

Steps: Seed at least 25 records with mixed text/numeric names; search a match and no match; sort both directions; change page/size; clear filters; reload.

Expected: Rows/counts match filters; correct sort order; pagination stable; useful empty state. Apply only controls exposed by this module; record unsupported controls as N/A.

**TC-0274 [P1] Create and edit persistence**

Steps: Create valid unique QA fixture using exposed form/API; refresh detail/list; edit one field; refresh again; inspect related records.

Expected: One record is created; intended edit persists; related records and totals stay consistent; success feedback and navigation work.

**TC-0275 [P1] Required fields, boundaries and unexpected input**

Steps: Submit empty required fields, whitespace, duplicate unique values, maximum length +/-1, invalid dates/numbers/foreign IDs and extra privileged fields; resubmit valid input.

Expected: Server rejects invalid input with field-specific feedback and no partial write; permitted values save; privileged extra fields cannot escalate access. Derive exact limits from module request rules before execution.

**TC-0276 [P1] Retry and failed-save behavior**

Steps: Double-submit intended creation; simulate slow/failing request; refresh/back; retry after failure; inspect row count and dependencies.

Expected: Feedback is visible; entered values recoverable; no unintended duplicate or partial business transaction; retry outcome is clear.

**TC-0277 [P0] Delete confirmation and dependency safety**

Steps: Cancel deletion of disposable fixture; confirm deletion of unused fixture; attempt deleting linked fixture and missing ID; repeat deletion.

Expected: Cancel preserves record; allowed delete removes only selected fixture; in-use dependencies are protected or follow explicitly approved cascade; repeat/missing IDs never cause server error.

**TC-0278 [P1] Contacts and geography**

Steps: Add multiple contacts; change country/state; load contact dropdown; clear optional fields.

Expected: Correct client contacts and country/state options load; stale state clears; optional blanks save correctly.

### ClientInvoice

**TC-0292 [P0] Guest, limited-role and record ownership access**

Steps: Call module URLs as guest, authorized role and role without permission; replace IDs with another tenant/user records; compare response and database.

Expected: Protected requests require login/API authentication and permission; cross-owner access is rejected; no data mutation or disclosure. Public routes expose only their approved public scope.

**TC-0293 [P1] Presentation and response contract**

Steps: Open available screens at 1440px and 390px; use keyboard Tab/Enter/Escape; inspect labels, errors, console and network. For API-only module inspect JSON headers/schema instead.

Expected: Readable layout and reachable controls; no clipped actions or blocking JavaScript errors; APIs have documented schema/content type and no stack traces.

**TC-0294 [P1] List, search, sort and pagination**

Steps: Seed at least 25 records with mixed text/numeric names; search a match and no match; sort both directions; change page/size; clear filters; reload.

Expected: Rows/counts match filters; correct sort order; pagination stable; useful empty state. Apply only controls exposed by this module; record unsupported controls as N/A.

**TC-0295 [P1] Create and edit persistence**

Steps: Create valid unique QA fixture using exposed form/API; refresh detail/list; edit one field; refresh again; inspect related records.

Expected: One record is created; intended edit persists; related records and totals stay consistent; success feedback and navigation work.

**TC-0296 [P1] Required fields, boundaries and unexpected input**

Steps: Submit empty required fields, whitespace, duplicate unique values, maximum length +/-1, invalid dates/numbers/foreign IDs and extra privileged fields; resubmit valid input.

Expected: Server rejects invalid input with field-specific feedback and no partial write; permitted values save; privileged extra fields cannot escalate access. Derive exact limits from module request rules before execution.

**TC-0297 [P1] Retry and failed-save behavior**

Steps: Double-submit intended creation; simulate slow/failing request; refresh/back; retry after failure; inspect row count and dependencies.

Expected: Feedback is visible; entered values recoverable; no unintended duplicate or partial business transaction; retry outcome is clear.

**TC-0298 [P0] Delete confirmation and dependency safety**

Steps: Cancel deletion of disposable fixture; confirm deletion of unused fixture; attempt deleting linked fixture and missing ID; repeat deletion.

Expected: Cancel preserves record; allowed delete removes only selected fixture; in-use dependencies are protected or follow explicitly approved cascade; repeat/missing IDs never cause server error.

**TC-0299 [P0] Invoice calculation and payment**

Steps: Create two lines with known quantities, rates and taxes; add partial then final payment; export PDF/Excel.

Expected: Calculated totals match independent arithmetic and rounding; balances reduce exactly; exports match stored invoice.

**TC-0300 [P0] Payment failure and replay**

Steps: Submit zero, negative and excessive payment; double-click valid payment and retry after simulated timeout.

Expected: Invalid amounts rejected; one intended payment is recorded once; ledger and balance stay consistent.

### Comment

**TC-0321 [P0] Guest, limited-role and record ownership access**

Steps: Call module URLs as guest, authorized role and role without permission; replace IDs with another tenant/user records; compare response and database.

Expected: Protected requests require login/API authentication and permission; cross-owner access is rejected; no data mutation or disclosure. Public routes expose only their approved public scope.

**TC-0322 [P1] Presentation and response contract**

Steps: Open available screens at 1440px and 390px; use keyboard Tab/Enter/Escape; inspect labels, errors, console and network. For API-only module inspect JSON headers/schema instead.

Expected: Readable layout and reachable controls; no clipped actions or blocking JavaScript errors; APIs have documented schema/content type and no stack traces.

**TC-0323 [P1] List, search, sort and pagination**

Steps: Seed at least 25 records with mixed text/numeric names; search a match and no match; sort both directions; change page/size; clear filters; reload.

Expected: Rows/counts match filters; correct sort order; pagination stable; useful empty state. Apply only controls exposed by this module; record unsupported controls as N/A.

**TC-0324 [P1] Create and edit persistence**

Steps: Create valid unique QA fixture using exposed form/API; refresh detail/list; edit one field; refresh again; inspect related records.

Expected: One record is created; intended edit persists; related records and totals stay consistent; success feedback and navigation work.

**TC-0325 [P1] Required fields, boundaries and unexpected input**

Steps: Submit empty required fields, whitespace, duplicate unique values, maximum length +/-1, invalid dates/numbers/foreign IDs and extra privileged fields; resubmit valid input.

Expected: Server rejects invalid input with field-specific feedback and no partial write; permitted values save; privileged extra fields cannot escalate access. Derive exact limits from module request rules before execution.

**TC-0326 [P1] Retry and failed-save behavior**

Steps: Double-submit intended creation; simulate slow/failing request; refresh/back; retry after failure; inspect row count and dependencies.

Expected: Feedback is visible; entered values recoverable; no unintended duplicate or partial business transaction; retry outcome is clear.

**TC-0327 [P0] Delete confirmation and dependency safety**

Steps: Cancel deletion of disposable fixture; confirm deletion of unused fixture; attempt deleting linked fixture and missing ID; repeat deletion.

Expected: Cancel preserves record; allowed delete removes only selected fixture; in-use dependencies are protected or follow explicitly approved cascade; repeat/missing IDs never cause server error.

**TC-0328 [P1] Comment threading and safety**

Steps: Post parent and reply containing HTML/script text; edit/delete own and another user comment.

Expected: Threading remains correct; executable content is escaped; authorization applies to edits/deletions.

### Comparison

**TC-0340 [P0] Guest, limited-role and record ownership access**

Steps: Call module URLs as guest, authorized role and role without permission; replace IDs with another tenant/user records; compare response and database.

Expected: Protected requests require login/API authentication and permission; cross-owner access is rejected; no data mutation or disclosure. Public routes expose only their approved public scope.

**TC-0341 [P1] Presentation and response contract**

Steps: Open available screens at 1440px and 390px; use keyboard Tab/Enter/Escape; inspect labels, errors, console and network. For API-only module inspect JSON headers/schema instead.

Expected: Readable layout and reachable controls; no clipped actions or blocking JavaScript errors; APIs have documented schema/content type and no stack traces.

**TC-0342 [P1] List, search, sort and pagination**

Steps: Seed at least 25 records with mixed text/numeric names; search a match and no match; sort both directions; change page/size; clear filters; reload.

Expected: Rows/counts match filters; correct sort order; pagination stable; useful empty state. Apply only controls exposed by this module; record unsupported controls as N/A.

**TC-0343 [P1] Create and edit persistence**

Steps: Create valid unique QA fixture using exposed form/API; refresh detail/list; edit one field; refresh again; inspect related records.

Expected: One record is created; intended edit persists; related records and totals stay consistent; success feedback and navigation work.

**TC-0344 [P1] Required fields, boundaries and unexpected input**

Steps: Submit empty required fields, whitespace, duplicate unique values, maximum length +/-1, invalid dates/numbers/foreign IDs and extra privileged fields; resubmit valid input.

Expected: Server rejects invalid input with field-specific feedback and no partial write; permitted values save; privileged extra fields cannot escalate access. Derive exact limits from module request rules before execution.

**TC-0345 [P1] Retry and failed-save behavior**

Steps: Double-submit intended creation; simulate slow/failing request; refresh/back; retry after failure; inspect row count and dependencies.

Expected: Feedback is visible; entered values recoverable; no unintended duplicate or partial business transaction; retry outcome is clear.

**TC-0346 [P0] Delete confirmation and dependency safety**

Steps: Cancel deletion of disposable fixture; confirm deletion of unused fixture; attempt deleting linked fixture and missing ID; repeat deletion.

Expected: Cancel preserves record; allowed delete removes only selected fixture; in-use dependencies are protected or follow explicitly approved cascade; repeat/missing IDs never cause server error.

**TC-0347 [P1] Supplier comparison**

Steps: Compare two suppliers; add comparison comment; edit/delete comparison; view as another restricted user.

Expected: Values align with chosen suppliers; comments persist safely; access follows permissions.

### Criteria

**TC-0356 [P0] Guest, limited-role and record ownership access**

Steps: Call module URLs as guest, authorized role and role without permission; replace IDs with another tenant/user records; compare response and database.

Expected: Protected requests require login/API authentication and permission; cross-owner access is rejected; no data mutation or disclosure. Public routes expose only their approved public scope.

**TC-0357 [P1] Presentation and response contract**

Steps: Open available screens at 1440px and 390px; use keyboard Tab/Enter/Escape; inspect labels, errors, console and network. For API-only module inspect JSON headers/schema instead.

Expected: Readable layout and reachable controls; no clipped actions or blocking JavaScript errors; APIs have documented schema/content type and no stack traces.

**TC-0358 [P1] List, search, sort and pagination**

Steps: Seed at least 25 records with mixed text/numeric names; search a match and no match; sort both directions; change page/size; clear filters; reload.

Expected: Rows/counts match filters; correct sort order; pagination stable; useful empty state. Apply only controls exposed by this module; record unsupported controls as N/A.

**TC-0359 [P1] Create and edit persistence**

Steps: Create valid unique QA fixture using exposed form/API; refresh detail/list; edit one field; refresh again; inspect related records.

Expected: One record is created; intended edit persists; related records and totals stay consistent; success feedback and navigation work.

**TC-0360 [P1] Required fields, boundaries and unexpected input**

Steps: Submit empty required fields, whitespace, duplicate unique values, maximum length +/-1, invalid dates/numbers/foreign IDs and extra privileged fields; resubmit valid input.

Expected: Server rejects invalid input with field-specific feedback and no partial write; permitted values save; privileged extra fields cannot escalate access. Derive exact limits from module request rules before execution.

**TC-0361 [P1] Retry and failed-save behavior**

Steps: Double-submit intended creation; simulate slow/failing request; refresh/back; retry after failure; inspect row count and dependencies.

Expected: Feedback is visible; entered values recoverable; no unintended duplicate or partial business transaction; retry outcome is clear.

**TC-0362 [P0] Delete confirmation and dependency safety**

Steps: Cancel deletion of disposable fixture; confirm deletion of unused fixture; attempt deleting linked fixture and missing ID; repeat deletion.

Expected: Cancel preserves record; allowed delete removes only selected fixture; in-use dependencies are protected or follow explicitly approved cascade; repeat/missing IDs never cause server error.

**TC-0363 [P1] Reference data consistency**

Steps: Create valid Criteria reference; attempt duplicate/invalid value; use in dependent form; edit then attempt delete while in use.

Expected: Valid reference appears in dependent selectors; validation applies; existing records retain consistent meaning and links.

### Cruises

**TC-0374 [P0] Guest, limited-role and record ownership access**

Steps: Call module URLs as guest, authorized role and role without permission; replace IDs with another tenant/user records; compare response and database.

Expected: Protected requests require login/API authentication and permission; cross-owner access is rejected; no data mutation or disclosure. Public routes expose only their approved public scope.

**TC-0375 [P1] Presentation and response contract**

Steps: Open available screens at 1440px and 390px; use keyboard Tab/Enter/Escape; inspect labels, errors, console and network. For API-only module inspect JSON headers/schema instead.

Expected: Readable layout and reachable controls; no clipped actions or blocking JavaScript errors; APIs have documented schema/content type and no stack traces.

**TC-0376 [P1] List, search, sort and pagination**

Steps: Seed at least 25 records with mixed text/numeric names; search a match and no match; sort both directions; change page/size; clear filters; reload.

Expected: Rows/counts match filters; correct sort order; pagination stable; useful empty state. Apply only controls exposed by this module; record unsupported controls as N/A.

**TC-0377 [P1] Create and edit persistence**

Steps: Create valid unique QA fixture using exposed form/API; refresh detail/list; edit one field; refresh again; inspect related records.

Expected: One record is created; intended edit persists; related records and totals stay consistent; success feedback and navigation work.

**TC-0378 [P1] Required fields, boundaries and unexpected input**

Steps: Submit empty required fields, whitespace, duplicate unique values, maximum length +/-1, invalid dates/numbers/foreign IDs and extra privileged fields; resubmit valid input.

Expected: Server rejects invalid input with field-specific feedback and no partial write; permitted values save; privileged extra fields cannot escalate access. Derive exact limits from module request rules before execution.

**TC-0379 [P1] Retry and failed-save behavior**

Steps: Double-submit intended creation; simulate slow/failing request; refresh/back; retry after failure; inspect row count and dependencies.

Expected: Feedback is visible; entered values recoverable; no unintended duplicate or partial business transaction; retry outcome is clear.

**TC-0380 [P0] Delete confirmation and dependency safety**

Steps: Cancel deletion of disposable fixture; confirm deletion of unused fixture; attempt deleting linked fixture and missing ID; repeat deletion.

Expected: Cancel preserves record; allowed delete removes only selected fixture; in-use dependencies are protected or follow explicitly approved cascade; repeat/missing IDs never cause server error.

**TC-0381 [P1] Supplier data and dependencies**

Steps: Create Cruises with location, contact and applicable rates/attachments; link to tour service; edit; attempt deletion while linked.

Expected: Supplier-specific details persist; dropdowns resolve correct record; used supplier cannot be removed leaving broken tour services.

### Currencies

**TC-0392 [P0] Guest, limited-role and record ownership access**

Steps: Call module URLs as guest, authorized role and role without permission; replace IDs with another tenant/user records; compare response and database.

Expected: Protected requests require login/API authentication and permission; cross-owner access is rejected; no data mutation or disclosure. Public routes expose only their approved public scope.

**TC-0393 [P1] Presentation and response contract**

Steps: Open available screens at 1440px and 390px; use keyboard Tab/Enter/Escape; inspect labels, errors, console and network. For API-only module inspect JSON headers/schema instead.

Expected: Readable layout and reachable controls; no clipped actions or blocking JavaScript errors; APIs have documented schema/content type and no stack traces.

**TC-0394 [P1] List, search, sort and pagination**

Steps: Seed at least 25 records with mixed text/numeric names; search a match and no match; sort both directions; change page/size; clear filters; reload.

Expected: Rows/counts match filters; correct sort order; pagination stable; useful empty state. Apply only controls exposed by this module; record unsupported controls as N/A.

**TC-0395 [P1] Create and edit persistence**

Steps: Create valid unique QA fixture using exposed form/API; refresh detail/list; edit one field; refresh again; inspect related records.

Expected: One record is created; intended edit persists; related records and totals stay consistent; success feedback and navigation work.

**TC-0396 [P1] Required fields, boundaries and unexpected input**

Steps: Submit empty required fields, whitespace, duplicate unique values, maximum length +/-1, invalid dates/numbers/foreign IDs and extra privileged fields; resubmit valid input.

Expected: Server rejects invalid input with field-specific feedback and no partial write; permitted values save; privileged extra fields cannot escalate access. Derive exact limits from module request rules before execution.

**TC-0397 [P1] Retry and failed-save behavior**

Steps: Double-submit intended creation; simulate slow/failing request; refresh/back; retry after failure; inspect row count and dependencies.

Expected: Feedback is visible; entered values recoverable; no unintended duplicate or partial business transaction; retry outcome is clear.

**TC-0398 [P0] Delete confirmation and dependency safety**

Steps: Cancel deletion of disposable fixture; confirm deletion of unused fixture; attempt deleting linked fixture and missing ID; repeat deletion.

Expected: Cancel preserves record; allowed delete removes only selected fixture; in-use dependencies are protected or follow explicitly approved cascade; repeat/missing IDs never cause server error.

**TC-0399 [P1] Reference data consistency**

Steps: Create valid Currencies reference; attempt duplicate/invalid value; use in dependent form; edit then attempt delete while in use.

Expected: Valid reference appears in dependent selectors; validation applies; existing records retain consistent meaning and links.

### CurrencyRate

**TC-0410 [P0] Guest, limited-role and record ownership access**

Steps: Call module URLs as guest, authorized role and role without permission; replace IDs with another tenant/user records; compare response and database.

Expected: Protected requests require login/API authentication and permission; cross-owner access is rejected; no data mutation or disclosure. Public routes expose only their approved public scope.

**TC-0411 [P1] Presentation and response contract**

Steps: Open available screens at 1440px and 390px; use keyboard Tab/Enter/Escape; inspect labels, errors, console and network. For API-only module inspect JSON headers/schema instead.

Expected: Readable layout and reachable controls; no clipped actions or blocking JavaScript errors; APIs have documented schema/content type and no stack traces.

**TC-0412 [P1] List, search, sort and pagination**

Steps: Seed at least 25 records with mixed text/numeric names; search a match and no match; sort both directions; change page/size; clear filters; reload.

Expected: Rows/counts match filters; correct sort order; pagination stable; useful empty state. Apply only controls exposed by this module; record unsupported controls as N/A.

**TC-0413 [P1] Create and edit persistence**

Steps: Create valid unique QA fixture using exposed form/API; refresh detail/list; edit one field; refresh again; inspect related records.

Expected: One record is created; intended edit persists; related records and totals stay consistent; success feedback and navigation work.

**TC-0414 [P1] Required fields, boundaries and unexpected input**

Steps: Submit empty required fields, whitespace, duplicate unique values, maximum length +/-1, invalid dates/numbers/foreign IDs and extra privileged fields; resubmit valid input.

Expected: Server rejects invalid input with field-specific feedback and no partial write; permitted values save; privileged extra fields cannot escalate access. Derive exact limits from module request rules before execution.

**TC-0415 [P1] Retry and failed-save behavior**

Steps: Double-submit intended creation; simulate slow/failing request; refresh/back; retry after failure; inspect row count and dependencies.

Expected: Feedback is visible; entered values recoverable; no unintended duplicate or partial business transaction; retry outcome is clear.

**TC-0416 [P0] Delete confirmation and dependency safety**

Steps: Cancel deletion of disposable fixture; confirm deletion of unused fixture; attempt deleting linked fixture and missing ID; repeat deletion.

Expected: Cancel preserves record; allowed delete removes only selected fixture; in-use dependencies are protected or follow explicitly approved cascade; repeat/missing IDs never cause server error.

**TC-0417 [P1] Reference data consistency**

Steps: Create valid CurrencyRate reference; attempt duplicate/invalid value; use in dependent form; edit then attempt delete while in use.

Expected: Valid reference appears in dependent selectors; validation applies; existing records retain consistent meaning and links.

**TC-0418 [P1] Conversion precision**

Steps: Use known base/quote currencies and fractional exchange rate; try zero/negative rate; convert a known invoice amount.

Expected: Conversion direction and rounding match independent arithmetic; invalid rates rejected; historical amounts follow documented rate policy.

### Driver

**TC-0429 [P0] Guest, limited-role and record ownership access**

Steps: Call module URLs as guest, authorized role and role without permission; replace IDs with another tenant/user records; compare response and database.

Expected: Protected requests require login/API authentication and permission; cross-owner access is rejected; no data mutation or disclosure. Public routes expose only their approved public scope.

**TC-0430 [P1] Presentation and response contract**

Steps: Open available screens at 1440px and 390px; use keyboard Tab/Enter/Escape; inspect labels, errors, console and network. For API-only module inspect JSON headers/schema instead.

Expected: Readable layout and reachable controls; no clipped actions or blocking JavaScript errors; APIs have documented schema/content type and no stack traces.

**TC-0431 [P1] List, search, sort and pagination**

Steps: Seed at least 25 records with mixed text/numeric names; search a match and no match; sort both directions; change page/size; clear filters; reload.

Expected: Rows/counts match filters; correct sort order; pagination stable; useful empty state. Apply only controls exposed by this module; record unsupported controls as N/A.

**TC-0432 [P1] Create and edit persistence**

Steps: Create valid unique QA fixture using exposed form/API; refresh detail/list; edit one field; refresh again; inspect related records.

Expected: One record is created; intended edit persists; related records and totals stay consistent; success feedback and navigation work.

**TC-0433 [P1] Required fields, boundaries and unexpected input**

Steps: Submit empty required fields, whitespace, duplicate unique values, maximum length +/-1, invalid dates/numbers/foreign IDs and extra privileged fields; resubmit valid input.

Expected: Server rejects invalid input with field-specific feedback and no partial write; permitted values save; privileged extra fields cannot escalate access. Derive exact limits from module request rules before execution.

**TC-0434 [P1] Retry and failed-save behavior**

Steps: Double-submit intended creation; simulate slow/failing request; refresh/back; retry after failure; inspect row count and dependencies.

Expected: Feedback is visible; entered values recoverable; no unintended duplicate or partial business transaction; retry outcome is clear.

**TC-0435 [P0] Delete confirmation and dependency safety**

Steps: Cancel deletion of disposable fixture; confirm deletion of unused fixture; attempt deleting linked fixture and missing ID; repeat deletion.

Expected: Cancel preserves record; allowed delete removes only selected fixture; in-use dependencies are protected or follow explicitly approved cascade; repeat/missing IDs never cause server error.

**TC-0436 [P1] Driver availability**

Steps: Assign driver to a trip; attempt overlapping assignment via form and calendar; delete an assigned driver.

Expected: Overlaps and destructive removal of active assignments are prevented without altering other trips.

### Email

**TC-0446 [P0] Guest, limited-role and record ownership access**

Steps: Call module URLs as guest, authorized role and role without permission; replace IDs with another tenant/user records; compare response and database.

Expected: Protected requests require login/API authentication and permission; cross-owner access is rejected; no data mutation or disclosure. Public routes expose only their approved public scope.

**TC-0447 [P1] Presentation and response contract**

Steps: Open available screens at 1440px and 390px; use keyboard Tab/Enter/Escape; inspect labels, errors, console and network. For API-only module inspect JSON headers/schema instead.

Expected: Readable layout and reachable controls; no clipped actions or blocking JavaScript errors; APIs have documented schema/content type and no stack traces.

**TC-0448 [P1] List, search, sort and pagination**

Steps: Seed at least 25 records with mixed text/numeric names; search a match and no match; sort both directions; change page/size; clear filters; reload.

Expected: Rows/counts match filters; correct sort order; pagination stable; useful empty state. Apply only controls exposed by this module; record unsupported controls as N/A.

**TC-0449 [P1] Mailbox workflow**

Steps: Using sandbox mailbox, compose, reply, search, mark read, create folder, move and delete test messages; download attachment.

Expected: Recipients, thread, folder and read state remain correct; only intended mailbox changes; attachment bytes are correct.

**TC-0450 [P1] Mail failure**

Steps: Use invalid recipient and simulate SMTP/IMAP timeout; retry one test message.

Expected: Visible error and preserved compose data; retry behavior avoids unintended duplicate sends.

### EmployesSalary

**TC-0476 [P0] Guest, limited-role and record ownership access**

Steps: Call module URLs as guest, authorized role and role without permission; replace IDs with another tenant/user records; compare response and database.

Expected: Protected requests require login/API authentication and permission; cross-owner access is rejected; no data mutation or disclosure. Public routes expose only their approved public scope.

**TC-0477 [P1] Presentation and response contract**

Steps: Open available screens at 1440px and 390px; use keyboard Tab/Enter/Escape; inspect labels, errors, console and network. For API-only module inspect JSON headers/schema instead.

Expected: Readable layout and reachable controls; no clipped actions or blocking JavaScript errors; APIs have documented schema/content type and no stack traces.

**TC-0478 [P1] List, search, sort and pagination**

Steps: Seed at least 25 records with mixed text/numeric names; search a match and no match; sort both directions; change page/size; clear filters; reload.

Expected: Rows/counts match filters; correct sort order; pagination stable; useful empty state. Apply only controls exposed by this module; record unsupported controls as N/A.

**TC-0479 [P1] Create and edit persistence**

Steps: Create valid unique QA fixture using exposed form/API; refresh detail/list; edit one field; refresh again; inspect related records.

Expected: One record is created; intended edit persists; related records and totals stay consistent; success feedback and navigation work.

**TC-0480 [P1] Required fields, boundaries and unexpected input**

Steps: Submit empty required fields, whitespace, duplicate unique values, maximum length +/-1, invalid dates/numbers/foreign IDs and extra privileged fields; resubmit valid input.

Expected: Server rejects invalid input with field-specific feedback and no partial write; permitted values save; privileged extra fields cannot escalate access. Derive exact limits from module request rules before execution.

**TC-0481 [P1] Retry and failed-save behavior**

Steps: Double-submit intended creation; simulate slow/failing request; refresh/back; retry after failure; inspect row count and dependencies.

Expected: Feedback is visible; entered values recoverable; no unintended duplicate or partial business transaction; retry outcome is clear.

**TC-0482 [P0] Delete confirmation and dependency safety**

Steps: Cancel deletion of disposable fixture; confirm deletion of unused fixture; attempt deleting linked fixture and missing ID; repeat deletion.

Expected: Cancel preserves record; allowed delete removes only selected fixture; in-use dependencies are protected or follow explicitly approved cascade; repeat/missing IDs never cause server error.

**TC-0483 [P1] Salary allocation**

Steps: Create salary for employee/office/period; try duplicate period and negative amount; inspect totals.

Expected: Valid salary is correctly allocated; duplicate handling follows approved policy; negative amount rejected.

### Event

**TC-0495 [P0] Guest, limited-role and record ownership access**

Steps: Call module URLs as guest, authorized role and role without permission; replace IDs with another tenant/user records; compare response and database.

Expected: Protected requests require login/API authentication and permission; cross-owner access is rejected; no data mutation or disclosure. Public routes expose only their approved public scope.

**TC-0496 [P1] Presentation and response contract**

Steps: Open available screens at 1440px and 390px; use keyboard Tab/Enter/Escape; inspect labels, errors, console and network. For API-only module inspect JSON headers/schema instead.

Expected: Readable layout and reachable controls; no clipped actions or blocking JavaScript errors; APIs have documented schema/content type and no stack traces.

**TC-0497 [P1] List, search, sort and pagination**

Steps: Seed at least 25 records with mixed text/numeric names; search a match and no match; sort both directions; change page/size; clear filters; reload.

Expected: Rows/counts match filters; correct sort order; pagination stable; useful empty state. Apply only controls exposed by this module; record unsupported controls as N/A.

**TC-0498 [P1] Create and edit persistence**

Steps: Create valid unique QA fixture using exposed form/API; refresh detail/list; edit one field; refresh again; inspect related records.

Expected: One record is created; intended edit persists; related records and totals stay consistent; success feedback and navigation work.

**TC-0499 [P1] Required fields, boundaries and unexpected input**

Steps: Submit empty required fields, whitespace, duplicate unique values, maximum length +/-1, invalid dates/numbers/foreign IDs and extra privileged fields; resubmit valid input.

Expected: Server rejects invalid input with field-specific feedback and no partial write; permitted values save; privileged extra fields cannot escalate access. Derive exact limits from module request rules before execution.

**TC-0500 [P1] Retry and failed-save behavior**

Steps: Double-submit intended creation; simulate slow/failing request; refresh/back; retry after failure; inspect row count and dependencies.

Expected: Feedback is visible; entered values recoverable; no unintended duplicate or partial business transaction; retry outcome is clear.

**TC-0501 [P0] Delete confirmation and dependency safety**

Steps: Cancel deletion of disposable fixture; confirm deletion of unused fixture; attempt deleting linked fixture and missing ID; repeat deletion.

Expected: Cancel preserves record; allowed delete removes only selected fixture; in-use dependencies are protected or follow explicitly approved cascade; repeat/missing IDs never cause server error.

**TC-0502 [P1] Supplier data and dependencies**

Steps: Create Event with location, contact and applicable rates/attachments; link to tour service; edit; attempt deletion while linked.

Expected: Supplier-specific details persist; dropdowns resolve correct record; used supplier cannot be removed leaving broken tour services.

### Export

**TC-0513 [P0] Guest, limited-role and record ownership access**

Steps: Call module URLs as guest, authorized role and role without permission; replace IDs with another tenant/user records; compare response and database.

Expected: Protected requests require login/API authentication and permission; cross-owner access is rejected; no data mutation or disclosure. Public routes expose only their approved public scope.

**TC-0514 [P1] Presentation and response contract**

Steps: Open available screens at 1440px and 390px; use keyboard Tab/Enter/Escape; inspect labels, errors, console and network. For API-only module inspect JSON headers/schema instead.

Expected: Readable layout and reachable controls; no clipped actions or blocking JavaScript errors; APIs have documented schema/content type and no stack traces.

**TC-0515 [P1] Export round trip**

Steps: Export services/seasons with commas, quotes, line breaks and Unicode; open in spreadsheet and reimport into isolated fixtures.

Expected: Columns and values remain intact; no spreadsheet formula execution from user-entered text; selected scope is honored.

### File

**TC-0518 [P0] Guest, limited-role and record ownership access**

Steps: Call module URLs as guest, authorized role and role without permission; replace IDs with another tenant/user records; compare response and database.

Expected: Protected requests require login/API authentication and permission; cross-owner access is rejected; no data mutation or disclosure. Public routes expose only their approved public scope.

**TC-0519 [P1] Presentation and response contract**

Steps: Open available screens at 1440px and 390px; use keyboard Tab/Enter/Escape; inspect labels, errors, console and network. For API-only module inspect JSON headers/schema instead.

Expected: Readable layout and reachable controls; no clipped actions or blocking JavaScript errors; APIs have documented schema/content type and no stack traces.

**TC-0520 [P0] Delete confirmation and dependency safety**

Steps: Cancel deletion of disposable fixture; confirm deletion of unused fixture; attempt deleting linked fixture and missing ID; repeat deletion.

Expected: Cancel preserves record; allowed delete removes only selected fixture; in-use dependencies are protected or follow explicitly approved cascade; repeat/missing IDs never cause server error.

**TC-0521 [P1] Download/delete authorization**

Steps: Download image/document without storage symlink; try missing file and another user file ID; cancel then confirm delete.

Expected: Correct bytes, filename and content type; unauthorized/missing files handled safely; cancel preserves file.

### Flight

**TC-0524 [P0] Guest, limited-role and record ownership access**

Steps: Call module URLs as guest, authorized role and role without permission; replace IDs with another tenant/user records; compare response and database.

Expected: Protected requests require login/API authentication and permission; cross-owner access is rejected; no data mutation or disclosure. Public routes expose only their approved public scope.

**TC-0525 [P1] Presentation and response contract**

Steps: Open available screens at 1440px and 390px; use keyboard Tab/Enter/Escape; inspect labels, errors, console and network. For API-only module inspect JSON headers/schema instead.

Expected: Readable layout and reachable controls; no clipped actions or blocking JavaScript errors; APIs have documented schema/content type and no stack traces.

**TC-0526 [P1] List, search, sort and pagination**

Steps: Seed at least 25 records with mixed text/numeric names; search a match and no match; sort both directions; change page/size; clear filters; reload.

Expected: Rows/counts match filters; correct sort order; pagination stable; useful empty state. Apply only controls exposed by this module; record unsupported controls as N/A.

**TC-0527 [P1] Create and edit persistence**

Steps: Create valid unique QA fixture using exposed form/API; refresh detail/list; edit one field; refresh again; inspect related records.

Expected: One record is created; intended edit persists; related records and totals stay consistent; success feedback and navigation work.

**TC-0528 [P1] Required fields, boundaries and unexpected input**

Steps: Submit empty required fields, whitespace, duplicate unique values, maximum length +/-1, invalid dates/numbers/foreign IDs and extra privileged fields; resubmit valid input.

Expected: Server rejects invalid input with field-specific feedback and no partial write; permitted values save; privileged extra fields cannot escalate access. Derive exact limits from module request rules before execution.

**TC-0529 [P1] Retry and failed-save behavior**

Steps: Double-submit intended creation; simulate slow/failing request; refresh/back; retry after failure; inspect row count and dependencies.

Expected: Feedback is visible; entered values recoverable; no unintended duplicate or partial business transaction; retry outcome is clear.

**TC-0530 [P0] Delete confirmation and dependency safety**

Steps: Cancel deletion of disposable fixture; confirm deletion of unused fixture; attempt deleting linked fixture and missing ID; repeat deletion.

Expected: Cancel preserves record; allowed delete removes only selected fixture; in-use dependencies are protected or follow explicitly approved cascade; repeat/missing IDs never cause server error.

**TC-0531 [P1] Supplier data and dependencies**

Steps: Create Flight with location, contact and applicable rates/attachments; link to tour service; edit; attempt deletion while linked.

Expected: Supplier-specific details persist; dropdowns resolve correct record; used supplier cannot be removed leaving broken tour services.

### GuestList

**TC-0542 [P0] Guest, limited-role and record ownership access**

Steps: Call module URLs as guest, authorized role and role without permission; replace IDs with another tenant/user records; compare response and database.

Expected: Protected requests require login/API authentication and permission; cross-owner access is rejected; no data mutation or disclosure. Public routes expose only their approved public scope.

**TC-0543 [P1] Presentation and response contract**

Steps: Open available screens at 1440px and 390px; use keyboard Tab/Enter/Escape; inspect labels, errors, console and network. For API-only module inspect JSON headers/schema instead.

Expected: Readable layout and reachable controls; no clipped actions or blocking JavaScript errors; APIs have documented schema/content type and no stack traces.

**TC-0544 [P1] List, search, sort and pagination**

Steps: Seed at least 25 records with mixed text/numeric names; search a match and no match; sort both directions; change page/size; clear filters; reload.

Expected: Rows/counts match filters; correct sort order; pagination stable; useful empty state. Apply only controls exposed by this module; record unsupported controls as N/A.

**TC-0545 [P1] Create and edit persistence**

Steps: Create valid unique QA fixture using exposed form/API; refresh detail/list; edit one field; refresh again; inspect related records.

Expected: One record is created; intended edit persists; related records and totals stay consistent; success feedback and navigation work.

**TC-0546 [P1] Required fields, boundaries and unexpected input**

Steps: Submit empty required fields, whitespace, duplicate unique values, maximum length +/-1, invalid dates/numbers/foreign IDs and extra privileged fields; resubmit valid input.

Expected: Server rejects invalid input with field-specific feedback and no partial write; permitted values save; privileged extra fields cannot escalate access. Derive exact limits from module request rules before execution.

**TC-0547 [P1] Retry and failed-save behavior**

Steps: Double-submit intended creation; simulate slow/failing request; refresh/back; retry after failure; inspect row count and dependencies.

Expected: Feedback is visible; entered values recoverable; no unintended duplicate or partial business transaction; retry outcome is clear.

**TC-0548 [P0] Delete confirmation and dependency safety**

Steps: Cancel deletion of disposable fixture; confirm deletion of unused fixture; attempt deleting linked fixture and missing ID; repeat deletion.

Expected: Cancel preserves record; allowed delete removes only selected fixture; in-use dependencies are protected or follow explicitly approved cascade; repeat/missing IDs never cause server error.

**TC-0549 [P1] Guest list and hotel distribution**

Steps: Add/edit/remove guests; inspect hotel-specific list and recipients; send only to mail sink.

Expected: Counts and names match tour; each hotel receives only intended guests; failed send is reported without losing data.

### Guide

**TC-0562 [P0] Guest, limited-role and record ownership access**

Steps: Call module URLs as guest, authorized role and role without permission; replace IDs with another tenant/user records; compare response and database.

Expected: Protected requests require login/API authentication and permission; cross-owner access is rejected; no data mutation or disclosure. Public routes expose only their approved public scope.

**TC-0563 [P1] Presentation and response contract**

Steps: Open available screens at 1440px and 390px; use keyboard Tab/Enter/Escape; inspect labels, errors, console and network. For API-only module inspect JSON headers/schema instead.

Expected: Readable layout and reachable controls; no clipped actions or blocking JavaScript errors; APIs have documented schema/content type and no stack traces.

**TC-0564 [P1] List, search, sort and pagination**

Steps: Seed at least 25 records with mixed text/numeric names; search a match and no match; sort both directions; change page/size; clear filters; reload.

Expected: Rows/counts match filters; correct sort order; pagination stable; useful empty state. Apply only controls exposed by this module; record unsupported controls as N/A.

**TC-0565 [P1] Create and edit persistence**

Steps: Create valid unique QA fixture using exposed form/API; refresh detail/list; edit one field; refresh again; inspect related records.

Expected: One record is created; intended edit persists; related records and totals stay consistent; success feedback and navigation work.

**TC-0566 [P1] Required fields, boundaries and unexpected input**

Steps: Submit empty required fields, whitespace, duplicate unique values, maximum length +/-1, invalid dates/numbers/foreign IDs and extra privileged fields; resubmit valid input.

Expected: Server rejects invalid input with field-specific feedback and no partial write; permitted values save; privileged extra fields cannot escalate access. Derive exact limits from module request rules before execution.

**TC-0567 [P1] Retry and failed-save behavior**

Steps: Double-submit intended creation; simulate slow/failing request; refresh/back; retry after failure; inspect row count and dependencies.

Expected: Feedback is visible; entered values recoverable; no unintended duplicate or partial business transaction; retry outcome is clear.

**TC-0568 [P0] Delete confirmation and dependency safety**

Steps: Cancel deletion of disposable fixture; confirm deletion of unused fixture; attempt deleting linked fixture and missing ID; repeat deletion.

Expected: Cancel preserves record; allowed delete removes only selected fixture; in-use dependencies are protected or follow explicitly approved cascade; repeat/missing IDs never cause server error.

**TC-0569 [P1] Supplier data and dependencies**

Steps: Create Guide with location, contact and applicable rates/attachments; link to tour service; edit; attempt deletion while linked.

Expected: Supplier-specific details persist; dropdowns resolve correct record; used supplier cannot be removed leaving broken tour services.

### Holiday

**TC-0580 [P0] Guest, limited-role and record ownership access**

Steps: Call module URLs as guest, authorized role and role without permission; replace IDs with another tenant/user records; compare response and database.

Expected: Protected requests require login/API authentication and permission; cross-owner access is rejected; no data mutation or disclosure. Public routes expose only their approved public scope.

**TC-0581 [P1] Presentation and response contract**

Steps: Open available screens at 1440px and 390px; use keyboard Tab/Enter/Escape; inspect labels, errors, console and network. For API-only module inspect JSON headers/schema instead.

Expected: Readable layout and reachable controls; no clipped actions or blocking JavaScript errors; APIs have documented schema/content type and no stack traces.

**TC-0582 [P1] List, search, sort and pagination**

Steps: Seed at least 25 records with mixed text/numeric names; search a match and no match; sort both directions; change page/size; clear filters; reload.

Expected: Rows/counts match filters; correct sort order; pagination stable; useful empty state. Apply only controls exposed by this module; record unsupported controls as N/A.

**TC-0583 [P1] Create and edit persistence**

Steps: Create valid unique QA fixture using exposed form/API; refresh detail/list; edit one field; refresh again; inspect related records.

Expected: One record is created; intended edit persists; related records and totals stay consistent; success feedback and navigation work.

**TC-0584 [P1] Required fields, boundaries and unexpected input**

Steps: Submit empty required fields, whitespace, duplicate unique values, maximum length +/-1, invalid dates/numbers/foreign IDs and extra privileged fields; resubmit valid input.

Expected: Server rejects invalid input with field-specific feedback and no partial write; permitted values save; privileged extra fields cannot escalate access. Derive exact limits from module request rules before execution.

**TC-0585 [P1] Retry and failed-save behavior**

Steps: Double-submit intended creation; simulate slow/failing request; refresh/back; retry after failure; inspect row count and dependencies.

Expected: Feedback is visible; entered values recoverable; no unintended duplicate or partial business transaction; retry outcome is clear.

**TC-0586 [P0] Delete confirmation and dependency safety**

Steps: Cancel deletion of disposable fixture; confirm deletion of unused fixture; attempt deleting linked fixture and missing ID; repeat deletion.

Expected: Cancel preserves record; allowed delete removes only selected fixture; in-use dependencies are protected or follow explicitly approved cascade; repeat/missing IDs never cause server error.

**TC-0587 [P1] Holiday dates**

Steps: Create holiday spanning year boundary; load dashboard calendar; edit/delete holiday.

Expected: Correct inclusive dates appear; edits refresh calendar; unrelated events remain unchanged.

### Hotel

**TC-0598 [P0] Guest, limited-role and record ownership access**

Steps: Call module URLs as guest, authorized role and role without permission; replace IDs with another tenant/user records; compare response and database.

Expected: Protected requests require login/API authentication and permission; cross-owner access is rejected; no data mutation or disclosure. Public routes expose only their approved public scope.

**TC-0599 [P1] Presentation and response contract**

Steps: Open available screens at 1440px and 390px; use keyboard Tab/Enter/Escape; inspect labels, errors, console and network. For API-only module inspect JSON headers/schema instead.

Expected: Readable layout and reachable controls; no clipped actions or blocking JavaScript errors; APIs have documented schema/content type and no stack traces.

**TC-0600 [P1] List, search, sort and pagination**

Steps: Seed at least 25 records with mixed text/numeric names; search a match and no match; sort both directions; change page/size; clear filters; reload.

Expected: Rows/counts match filters; correct sort order; pagination stable; useful empty state. Apply only controls exposed by this module; record unsupported controls as N/A.

**TC-0601 [P1] Create and edit persistence**

Steps: Create valid unique QA fixture using exposed form/API; refresh detail/list; edit one field; refresh again; inspect related records.

Expected: One record is created; intended edit persists; related records and totals stay consistent; success feedback and navigation work.

**TC-0602 [P1] Required fields, boundaries and unexpected input**

Steps: Submit empty required fields, whitespace, duplicate unique values, maximum length +/-1, invalid dates/numbers/foreign IDs and extra privileged fields; resubmit valid input.

Expected: Server rejects invalid input with field-specific feedback and no partial write; permitted values save; privileged extra fields cannot escalate access. Derive exact limits from module request rules before execution.

**TC-0603 [P1] Retry and failed-save behavior**

Steps: Double-submit intended creation; simulate slow/failing request; refresh/back; retry after failure; inspect row count and dependencies.

Expected: Feedback is visible; entered values recoverable; no unintended duplicate or partial business transaction; retry outcome is clear.

**TC-0604 [P0] Delete confirmation and dependency safety**

Steps: Cancel deletion of disposable fixture; confirm deletion of unused fixture; attempt deleting linked fixture and missing ID; repeat deletion.

Expected: Cancel preserves record; allowed delete removes only selected fixture; in-use dependencies are protected or follow explicitly approved cascade; repeat/missing IDs never cause server error.

**TC-0605 [P1] Supplier data and dependencies**

Steps: Create Hotel with location, contact and applicable rates/attachments; link to tour service; edit; attempt deletion while linked.

Expected: Supplier-specific details persist; dropdowns resolve correct record; used supplier cannot be removed leaving broken tour services.

### Import

**TC-0618 [P0] Guest, limited-role and record ownership access**

Steps: Call module URLs as guest, authorized role and role without permission; replace IDs with another tenant/user records; compare response and database.

Expected: Protected requests require login/API authentication and permission; cross-owner access is rejected; no data mutation or disclosure. Public routes expose only their approved public scope.

**TC-0619 [P1] Presentation and response contract**

Steps: Open available screens at 1440px and 390px; use keyboard Tab/Enter/Escape; inspect labels, errors, console and network. For API-only module inspect JSON headers/schema instead.

Expected: Readable layout and reachable controls; no clipped actions or blocking JavaScript errors; APIs have documented schema/content type and no stack traces.

**TC-0620 [P0] Import integrity**

Steps: Use sample with valid, duplicate, missing-column, Unicode and malformed rows; preview then import; retry same file.

Expected: Row-level errors are actionable; valid mapping preserved; retry does not silently duplicate records; failures do not partially corrupt data.

### Invoices

**TC-0629 [P0] Guest, limited-role and record ownership access**

Steps: Call module URLs as guest, authorized role and role without permission; replace IDs with another tenant/user records; compare response and database.

Expected: Protected requests require login/API authentication and permission; cross-owner access is rejected; no data mutation or disclosure. Public routes expose only their approved public scope.

**TC-0630 [P1] Presentation and response contract**

Steps: Open available screens at 1440px and 390px; use keyboard Tab/Enter/Escape; inspect labels, errors, console and network. For API-only module inspect JSON headers/schema instead.

Expected: Readable layout and reachable controls; no clipped actions or blocking JavaScript errors; APIs have documented schema/content type and no stack traces.

**TC-0631 [P1] List, search, sort and pagination**

Steps: Seed at least 25 records with mixed text/numeric names; search a match and no match; sort both directions; change page/size; clear filters; reload.

Expected: Rows/counts match filters; correct sort order; pagination stable; useful empty state. Apply only controls exposed by this module; record unsupported controls as N/A.

**TC-0632 [P1] Create and edit persistence**

Steps: Create valid unique QA fixture using exposed form/API; refresh detail/list; edit one field; refresh again; inspect related records.

Expected: One record is created; intended edit persists; related records and totals stay consistent; success feedback and navigation work.

**TC-0633 [P1] Required fields, boundaries and unexpected input**

Steps: Submit empty required fields, whitespace, duplicate unique values, maximum length +/-1, invalid dates/numbers/foreign IDs and extra privileged fields; resubmit valid input.

Expected: Server rejects invalid input with field-specific feedback and no partial write; permitted values save; privileged extra fields cannot escalate access. Derive exact limits from module request rules before execution.

**TC-0634 [P1] Retry and failed-save behavior**

Steps: Double-submit intended creation; simulate slow/failing request; refresh/back; retry after failure; inspect row count and dependencies.

Expected: Feedback is visible; entered values recoverable; no unintended duplicate or partial business transaction; retry outcome is clear.

**TC-0635 [P0] Delete confirmation and dependency safety**

Steps: Cancel deletion of disposable fixture; confirm deletion of unused fixture; attempt deleting linked fixture and missing ID; repeat deletion.

Expected: Cancel preserves record; allowed delete removes only selected fixture; in-use dependencies are protected or follow explicitly approved cascade; repeat/missing IDs never cause server error.

**TC-0636 [P0] Supplier invoice and payments**

Steps: Select tour then supplier; create invoice with known totals; pay partially then fully; inspect transactions.

Expected: Supplier options match context; payable balance and payment history reconcile; duplicate submission does not double-charge.

### Menu

**TC-0650 [P0] Guest, limited-role and record ownership access**

Steps: Call module URLs as guest, authorized role and role without permission; replace IDs with another tenant/user records; compare response and database.

Expected: Protected requests require login/API authentication and permission; cross-owner access is rejected; no data mutation or disclosure. Public routes expose only their approved public scope.

**TC-0651 [P1] Presentation and response contract**

Steps: Open available screens at 1440px and 390px; use keyboard Tab/Enter/Escape; inspect labels, errors, console and network. For API-only module inspect JSON headers/schema instead.

Expected: Readable layout and reachable controls; no clipped actions or blocking JavaScript errors; APIs have documented schema/content type and no stack traces.

**TC-0652 [P1] List, search, sort and pagination**

Steps: Seed at least 25 records with mixed text/numeric names; search a match and no match; sort both directions; change page/size; clear filters; reload.

Expected: Rows/counts match filters; correct sort order; pagination stable; useful empty state. Apply only controls exposed by this module; record unsupported controls as N/A.

**TC-0653 [P1] Create and edit persistence**

Steps: Create valid unique QA fixture using exposed form/API; refresh detail/list; edit one field; refresh again; inspect related records.

Expected: One record is created; intended edit persists; related records and totals stay consistent; success feedback and navigation work.

**TC-0654 [P1] Required fields, boundaries and unexpected input**

Steps: Submit empty required fields, whitespace, duplicate unique values, maximum length +/-1, invalid dates/numbers/foreign IDs and extra privileged fields; resubmit valid input.

Expected: Server rejects invalid input with field-specific feedback and no partial write; permitted values save; privileged extra fields cannot escalate access. Derive exact limits from module request rules before execution.

**TC-0655 [P1] Retry and failed-save behavior**

Steps: Double-submit intended creation; simulate slow/failing request; refresh/back; retry after failure; inspect row count and dependencies.

Expected: Feedback is visible; entered values recoverable; no unintended duplicate or partial business transaction; retry outcome is clear.

**TC-0656 [P0] Delete confirmation and dependency safety**

Steps: Cancel deletion of disposable fixture; confirm deletion of unused fixture; attempt deleting linked fixture and missing ID; repeat deletion.

Expected: Cancel preserves record; allowed delete removes only selected fixture; in-use dependencies are protected or follow explicitly approved cascade; repeat/missing IDs never cause server error.

**TC-0657 [P0] Menu permission and ordering**

Steps: Create/reorder allowed menu item; assign route and parent; try invalid route and cyclic parent.

Expected: Navigation renders usable links with permission filtering; invalid hierarchy is rejected or clearly reported.

### Notifications

**TC-0667 [P0] Guest, limited-role and record ownership access**

Steps: Call module URLs as guest, authorized role and role without permission; replace IDs with another tenant/user records; compare response and database.

Expected: Protected requests require login/API authentication and permission; cross-owner access is rejected; no data mutation or disclosure. Public routes expose only their approved public scope.

**TC-0668 [P1] Presentation and response contract**

Steps: Open available screens at 1440px and 390px; use keyboard Tab/Enter/Escape; inspect labels, errors, console and network. For API-only module inspect JSON headers/schema instead.

Expected: Readable layout and reachable controls; no clipped actions or blocking JavaScript errors; APIs have documented schema/content type and no stack traces.

**TC-0669 [P0] Delete confirmation and dependency safety**

Steps: Cancel deletion of disposable fixture; confirm deletion of unused fixture; attempt deleting linked fixture and missing ID; repeat deletion.

Expected: Cancel preserves record; allowed delete removes only selected fixture; in-use dependencies are protected or follow explicitly approved cascade; repeat/missing IDs never cause server error.

**TC-0670 [P1] Notification ownership**

Steps: Create notifications for A/B; read all and delete one/all as A; submit B notification ID.

Expected: Only A notifications change; counts match; B notification remains protected.

### Offer

**TC-0677 [P0] Guest, limited-role and record ownership access**

Steps: Call module URLs as guest, authorized role and role without permission; replace IDs with another tenant/user records; compare response and database.

Expected: Protected requests require login/API authentication and permission; cross-owner access is rejected; no data mutation or disclosure. Public routes expose only their approved public scope.

**TC-0678 [P1] Presentation and response contract**

Steps: Open available screens at 1440px and 390px; use keyboard Tab/Enter/Escape; inspect labels, errors, console and network. For API-only module inspect JSON headers/schema instead.

Expected: Readable layout and reachable controls; no clipped actions or blocking JavaScript errors; APIs have documented schema/content type and no stack traces.

**TC-0679 [P0] Delete confirmation and dependency safety**

Steps: Cancel deletion of disposable fixture; confirm deletion of unused fixture; attempt deleting linked fixture and missing ID; repeat deletion.

Expected: Cancel preserves record; allowed delete removes only selected fixture; in-use dependencies are protected or follow explicitly approved cascade; repeat/missing IDs never cause server error.

**TC-0680 [P1] Offer lifecycle and assignment**

Steps: Create current and expired offers; assign one to tour; change status; inspect bookings and cancellation policy.

Expected: Current/past lists and booking state are correct; assignment does not duplicate services; policy remains linked.

### Office

**TC-0696 [P0] Guest, limited-role and record ownership access**

Steps: Call module URLs as guest, authorized role and role without permission; replace IDs with another tenant/user records; compare response and database.

Expected: Protected requests require login/API authentication and permission; cross-owner access is rejected; no data mutation or disclosure. Public routes expose only their approved public scope.

**TC-0697 [P1] Presentation and response contract**

Steps: Open available screens at 1440px and 390px; use keyboard Tab/Enter/Escape; inspect labels, errors, console and network. For API-only module inspect JSON headers/schema instead.

Expected: Readable layout and reachable controls; no clipped actions or blocking JavaScript errors; APIs have documented schema/content type and no stack traces.

**TC-0698 [P1] List, search, sort and pagination**

Steps: Seed at least 25 records with mixed text/numeric names; search a match and no match; sort both directions; change page/size; clear filters; reload.

Expected: Rows/counts match filters; correct sort order; pagination stable; useful empty state. Apply only controls exposed by this module; record unsupported controls as N/A.

**TC-0699 [P1] Create and edit persistence**

Steps: Create valid unique QA fixture using exposed form/API; refresh detail/list; edit one field; refresh again; inspect related records.

Expected: One record is created; intended edit persists; related records and totals stay consistent; success feedback and navigation work.

**TC-0700 [P1] Required fields, boundaries and unexpected input**

Steps: Submit empty required fields, whitespace, duplicate unique values, maximum length +/-1, invalid dates/numbers/foreign IDs and extra privileged fields; resubmit valid input.

Expected: Server rejects invalid input with field-specific feedback and no partial write; permitted values save; privileged extra fields cannot escalate access. Derive exact limits from module request rules before execution.

**TC-0701 [P1] Retry and failed-save behavior**

Steps: Double-submit intended creation; simulate slow/failing request; refresh/back; retry after failure; inspect row count and dependencies.

Expected: Feedback is visible; entered values recoverable; no unintended duplicate or partial business transaction; retry outcome is clear.

**TC-0702 [P0] Delete confirmation and dependency safety**

Steps: Cancel deletion of disposable fixture; confirm deletion of unused fixture; attempt deleting linked fixture and missing ID; repeat deletion.

Expected: Cancel preserves record; allowed delete removes only selected fixture; in-use dependencies are protected or follow explicitly approved cascade; repeat/missing IDs never cause server error.

**TC-0703 [P0] Office isolation**

Steps: Create offices A/B with invoices, expenses and payments; switch office; attempt deletion with dependents.

Expected: Records and totals stay scoped to selected office; linked financial records are protected.

### OfficeEarning

**TC-0714 [P0] Guest, limited-role and record ownership access**

Steps: Call module URLs as guest, authorized role and role without permission; replace IDs with another tenant/user records; compare response and database.

Expected: Protected requests require login/API authentication and permission; cross-owner access is rejected; no data mutation or disclosure. Public routes expose only their approved public scope.

**TC-0715 [P1] Presentation and response contract**

Steps: Open available screens at 1440px and 390px; use keyboard Tab/Enter/Escape; inspect labels, errors, console and network. For API-only module inspect JSON headers/schema instead.

Expected: Readable layout and reachable controls; no clipped actions or blocking JavaScript errors; APIs have documented schema/content type and no stack traces.

**TC-0716 [P1] List, search, sort and pagination**

Steps: Seed at least 25 records with mixed text/numeric names; search a match and no match; sort both directions; change page/size; clear filters; reload.

Expected: Rows/counts match filters; correct sort order; pagination stable; useful empty state. Apply only controls exposed by this module; record unsupported controls as N/A.

**TC-0717 [P1] Create and edit persistence**

Steps: Create valid unique QA fixture using exposed form/API; refresh detail/list; edit one field; refresh again; inspect related records.

Expected: One record is created; intended edit persists; related records and totals stay consistent; success feedback and navigation work.

**TC-0718 [P1] Required fields, boundaries and unexpected input**

Steps: Submit empty required fields, whitespace, duplicate unique values, maximum length +/-1, invalid dates/numbers/foreign IDs and extra privileged fields; resubmit valid input.

Expected: Server rejects invalid input with field-specific feedback and no partial write; permitted values save; privileged extra fields cannot escalate access. Derive exact limits from module request rules before execution.

**TC-0719 [P1] Retry and failed-save behavior**

Steps: Double-submit intended creation; simulate slow/failing request; refresh/back; retry after failure; inspect row count and dependencies.

Expected: Feedback is visible; entered values recoverable; no unintended duplicate or partial business transaction; retry outcome is clear.

**TC-0720 [P0] Delete confirmation and dependency safety**

Steps: Cancel deletion of disposable fixture; confirm deletion of unused fixture; attempt deleting linked fixture and missing ID; repeat deletion.

Expected: Cancel preserves record; allowed delete removes only selected fixture; in-use dependencies are protected or follow explicitly approved cascade; repeat/missing IDs never cause server error.

**TC-0721 [P1] Earnings aggregation**

Steps: Record earnings in two offices and periods; edit one entry; compare office summaries.

Expected: Only corresponding office/period changes; no duplicate income is counted.

### OfficeInvoice

**TC-0733 [P0] Guest, limited-role and record ownership access**

Steps: Call module URLs as guest, authorized role and role without permission; replace IDs with another tenant/user records; compare response and database.

Expected: Protected requests require login/API authentication and permission; cross-owner access is rejected; no data mutation or disclosure. Public routes expose only their approved public scope.

**TC-0734 [P1] Presentation and response contract**

Steps: Open available screens at 1440px and 390px; use keyboard Tab/Enter/Escape; inspect labels, errors, console and network. For API-only module inspect JSON headers/schema instead.

Expected: Readable layout and reachable controls; no clipped actions or blocking JavaScript errors; APIs have documented schema/content type and no stack traces.

**TC-0735 [P1] List, search, sort and pagination**

Steps: Seed at least 25 records with mixed text/numeric names; search a match and no match; sort both directions; change page/size; clear filters; reload.

Expected: Rows/counts match filters; correct sort order; pagination stable; useful empty state. Apply only controls exposed by this module; record unsupported controls as N/A.

**TC-0736 [P1] Create and edit persistence**

Steps: Create valid unique QA fixture using exposed form/API; refresh detail/list; edit one field; refresh again; inspect related records.

Expected: One record is created; intended edit persists; related records and totals stay consistent; success feedback and navigation work.

**TC-0737 [P1] Required fields, boundaries and unexpected input**

Steps: Submit empty required fields, whitespace, duplicate unique values, maximum length +/-1, invalid dates/numbers/foreign IDs and extra privileged fields; resubmit valid input.

Expected: Server rejects invalid input with field-specific feedback and no partial write; permitted values save; privileged extra fields cannot escalate access. Derive exact limits from module request rules before execution.

**TC-0738 [P1] Retry and failed-save behavior**

Steps: Double-submit intended creation; simulate slow/failing request; refresh/back; retry after failure; inspect row count and dependencies.

Expected: Feedback is visible; entered values recoverable; no unintended duplicate or partial business transaction; retry outcome is clear.

**TC-0739 [P0] Delete confirmation and dependency safety**

Steps: Cancel deletion of disposable fixture; confirm deletion of unused fixture; attempt deleting linked fixture and missing ID; repeat deletion.

Expected: Cancel preserves record; allowed delete removes only selected fixture; in-use dependencies are protected or follow explicitly approved cascade; repeat/missing IDs never cause server error.

**TC-0740 [P1] Office invoice details and PDF**

Steps: Create invoice for office A; open detail endpoint and PDF; attempt office B identifier substitution.

Expected: Details and export agree; unauthorized office records are inaccessible; totals match line arithmetic.

### Profile

**TC-0755 [P0] Guest, limited-role and record ownership access**

Steps: Call module URLs as guest, authorized role and role without permission; replace IDs with another tenant/user records; compare response and database.

Expected: Protected requests require login/API authentication and permission; cross-owner access is rejected; no data mutation or disclosure. Public routes expose only their approved public scope.

**TC-0756 [P1] Presentation and response contract**

Steps: Open available screens at 1440px and 390px; use keyboard Tab/Enter/Escape; inspect labels, errors, console and network. For API-only module inspect JSON headers/schema instead.

Expected: Readable layout and reachable controls; no clipped actions or blocking JavaScript errors; APIs have documented schema/content type and no stack traces.

**TC-0757 [P1] Profile and scoped tabs**

Steps: Open own profile and tours/tasks/notifications tabs; request another user identifier with limited role.

Expected: Correct authorized data appears; private fields and unrelated notifications are not exposed.

### Quotation

**TC-0764 [P0] Guest, limited-role and record ownership access**

Steps: Call module URLs as guest, authorized role and role without permission; replace IDs with another tenant/user records; compare response and database.

Expected: Protected requests require login/API authentication and permission; cross-owner access is rejected; no data mutation or disclosure. Public routes expose only their approved public scope.

**TC-0765 [P1] Presentation and response contract**

Steps: Open available screens at 1440px and 390px; use keyboard Tab/Enter/Escape; inspect labels, errors, console and network. For API-only module inspect JSON headers/schema instead.

Expected: Readable layout and reachable controls; no clipped actions or blocking JavaScript errors; APIs have documented schema/content type and no stack traces.

**TC-0766 [P1] List, search, sort and pagination**

Steps: Seed at least 25 records with mixed text/numeric names; search a match and no match; sort both directions; change page/size; clear filters; reload.

Expected: Rows/counts match filters; correct sort order; pagination stable; useful empty state. Apply only controls exposed by this module; record unsupported controls as N/A.

**TC-0767 [P1] Create and edit persistence**

Steps: Create valid unique QA fixture using exposed form/API; refresh detail/list; edit one field; refresh again; inspect related records.

Expected: One record is created; intended edit persists; related records and totals stay consistent; success feedback and navigation work.

**TC-0768 [P1] Required fields, boundaries and unexpected input**

Steps: Submit empty required fields, whitespace, duplicate unique values, maximum length +/-1, invalid dates/numbers/foreign IDs and extra privileged fields; resubmit valid input.

Expected: Server rejects invalid input with field-specific feedback and no partial write; permitted values save; privileged extra fields cannot escalate access. Derive exact limits from module request rules before execution.

**TC-0769 [P1] Retry and failed-save behavior**

Steps: Double-submit intended creation; simulate slow/failing request; refresh/back; retry after failure; inspect row count and dependencies.

Expected: Feedback is visible; entered values recoverable; no unintended duplicate or partial business transaction; retry outcome is clear.

**TC-0770 [P0] Delete confirmation and dependency safety**

Steps: Cancel deletion of disposable fixture; confirm deletion of unused fixture; attempt deleting linked fixture and missing ID; repeat deletion.

Expected: Cancel preserves record; allowed delete removes only selected fixture; in-use dependencies are protected or follow explicitly approved cascade; repeat/missing IDs never cause server error.

**TC-0771 [P1] Quote confirmation and export**

Steps: Create quote with known lines; save; confirm/cancel; edit column message; export Excel/PDF.

Expected: Totals and messages persist; confirmation state is consistent; exports match quotation and contain no hidden internal fields.

### Rate

**TC-0787 [P0] Guest, limited-role and record ownership access**

Steps: Call module URLs as guest, authorized role and role without permission; replace IDs with another tenant/user records; compare response and database.

Expected: Protected requests require login/API authentication and permission; cross-owner access is rejected; no data mutation or disclosure. Public routes expose only their approved public scope.

**TC-0788 [P1] Presentation and response contract**

Steps: Open available screens at 1440px and 390px; use keyboard Tab/Enter/Escape; inspect labels, errors, console and network. For API-only module inspect JSON headers/schema instead.

Expected: Readable layout and reachable controls; no clipped actions or blocking JavaScript errors; APIs have documented schema/content type and no stack traces.

**TC-0789 [P1] List, search, sort and pagination**

Steps: Seed at least 25 records with mixed text/numeric names; search a match and no match; sort both directions; change page/size; clear filters; reload.

Expected: Rows/counts match filters; correct sort order; pagination stable; useful empty state. Apply only controls exposed by this module; record unsupported controls as N/A.

**TC-0790 [P1] Create and edit persistence**

Steps: Create valid unique QA fixture using exposed form/API; refresh detail/list; edit one field; refresh again; inspect related records.

Expected: One record is created; intended edit persists; related records and totals stay consistent; success feedback and navigation work.

**TC-0791 [P1] Required fields, boundaries and unexpected input**

Steps: Submit empty required fields, whitespace, duplicate unique values, maximum length +/-1, invalid dates/numbers/foreign IDs and extra privileged fields; resubmit valid input.

Expected: Server rejects invalid input with field-specific feedback and no partial write; permitted values save; privileged extra fields cannot escalate access. Derive exact limits from module request rules before execution.

**TC-0792 [P1] Retry and failed-save behavior**

Steps: Double-submit intended creation; simulate slow/failing request; refresh/back; retry after failure; inspect row count and dependencies.

Expected: Feedback is visible; entered values recoverable; no unintended duplicate or partial business transaction; retry outcome is clear.

**TC-0793 [P0] Delete confirmation and dependency safety**

Steps: Cancel deletion of disposable fixture; confirm deletion of unused fixture; attempt deleting linked fixture and missing ID; repeat deletion.

Expected: Cancel preserves record; allowed delete removes only selected fixture; in-use dependencies are protected or follow explicitly approved cascade; repeat/missing IDs never cause server error.

**TC-0794 [P1] Reference data consistency**

Steps: Create valid Rate reference; attempt duplicate/invalid value; use in dependent form; edit then attempt delete while in use.

Expected: Valid reference appears in dependent selectors; validation applies; existing records retain consistent meaning and links.

### Reporting

**TC-0805 [P0] Guest, limited-role and record ownership access**

Steps: Call module URLs as guest, authorized role and role without permission; replace IDs with another tenant/user records; compare response and database.

Expected: Protected requests require login/API authentication and permission; cross-owner access is rejected; no data mutation or disclosure. Public routes expose only their approved public scope.

**TC-0806 [P1] Presentation and response contract**

Steps: Open available screens at 1440px and 390px; use keyboard Tab/Enter/Escape; inspect labels, errors, console and network. For API-only module inspect JSON headers/schema instead.

Expected: Readable layout and reachable controls; no clipped actions or blocking JavaScript errors; APIs have documented schema/content type and no stack traces.

**TC-0807 [P1] List, search, sort and pagination**

Steps: Seed at least 25 records with mixed text/numeric names; search a match and no match; sort both directions; change page/size; clear filters; reload.

Expected: Rows/counts match filters; correct sort order; pagination stable; useful empty state. Apply only controls exposed by this module; record unsupported controls as N/A.

**TC-0808 [P1] Create and edit persistence**

Steps: Create valid unique QA fixture using exposed form/API; refresh detail/list; edit one field; refresh again; inspect related records.

Expected: One record is created; intended edit persists; related records and totals stay consistent; success feedback and navigation work.

**TC-0809 [P1] Required fields, boundaries and unexpected input**

Steps: Submit empty required fields, whitespace, duplicate unique values, maximum length +/-1, invalid dates/numbers/foreign IDs and extra privileged fields; resubmit valid input.

Expected: Server rejects invalid input with field-specific feedback and no partial write; permitted values save; privileged extra fields cannot escalate access. Derive exact limits from module request rules before execution.

**TC-0810 [P1] Retry and failed-save behavior**

Steps: Double-submit intended creation; simulate slow/failing request; refresh/back; retry after failure; inspect row count and dependencies.

Expected: Feedback is visible; entered values recoverable; no unintended duplicate or partial business transaction; retry outcome is clear.

**TC-0811 [P0] Delete confirmation and dependency safety**

Steps: Cancel deletion of disposable fixture; confirm deletion of unused fixture; attempt deleting linked fixture and missing ID; repeat deletion.

Expected: Cancel preserves record; allowed delete removes only selected fixture; in-use dependencies are protected or follow explicitly approved cascade; repeat/missing IDs never cause server error.

**TC-0812 [P1] Supplier report reconciliation**

Steps: Filter hotel/event/guide/restaurant reports by dates and supplier; include empty period and boundary dates.

Expected: Rows belong to selected supplier/period; totals match source records; no-data state is clear.

### Restaurant

**TC-0828 [P0] Guest, limited-role and record ownership access**

Steps: Call module URLs as guest, authorized role and role without permission; replace IDs with another tenant/user records; compare response and database.

Expected: Protected requests require login/API authentication and permission; cross-owner access is rejected; no data mutation or disclosure. Public routes expose only their approved public scope.

**TC-0829 [P1] Presentation and response contract**

Steps: Open available screens at 1440px and 390px; use keyboard Tab/Enter/Escape; inspect labels, errors, console and network. For API-only module inspect JSON headers/schema instead.

Expected: Readable layout and reachable controls; no clipped actions or blocking JavaScript errors; APIs have documented schema/content type and no stack traces.

**TC-0830 [P1] List, search, sort and pagination**

Steps: Seed at least 25 records with mixed text/numeric names; search a match and no match; sort both directions; change page/size; clear filters; reload.

Expected: Rows/counts match filters; correct sort order; pagination stable; useful empty state. Apply only controls exposed by this module; record unsupported controls as N/A.

**TC-0831 [P1] Create and edit persistence**

Steps: Create valid unique QA fixture using exposed form/API; refresh detail/list; edit one field; refresh again; inspect related records.

Expected: One record is created; intended edit persists; related records and totals stay consistent; success feedback and navigation work.

**TC-0832 [P1] Required fields, boundaries and unexpected input**

Steps: Submit empty required fields, whitespace, duplicate unique values, maximum length +/-1, invalid dates/numbers/foreign IDs and extra privileged fields; resubmit valid input.

Expected: Server rejects invalid input with field-specific feedback and no partial write; permitted values save; privileged extra fields cannot escalate access. Derive exact limits from module request rules before execution.

**TC-0833 [P1] Retry and failed-save behavior**

Steps: Double-submit intended creation; simulate slow/failing request; refresh/back; retry after failure; inspect row count and dependencies.

Expected: Feedback is visible; entered values recoverable; no unintended duplicate or partial business transaction; retry outcome is clear.

**TC-0834 [P0] Delete confirmation and dependency safety**

Steps: Cancel deletion of disposable fixture; confirm deletion of unused fixture; attempt deleting linked fixture and missing ID; repeat deletion.

Expected: Cancel preserves record; allowed delete removes only selected fixture; in-use dependencies are protected or follow explicitly approved cascade; repeat/missing IDs never cause server error.

**TC-0835 [P1] Supplier data and dependencies**

Steps: Create Restaurant with location, contact and applicable rates/attachments; link to tour service; edit; attempt deletion while linked.

Expected: Supplier-specific details persist; dropdowns resolve correct record; used supplier cannot be removed leaving broken tour services.

### RoomList

**TC-0846 [P0] Guest, limited-role and record ownership access**

Steps: Call module URLs as guest, authorized role and role without permission; replace IDs with another tenant/user records; compare response and database.

Expected: Protected requests require login/API authentication and permission; cross-owner access is rejected; no data mutation or disclosure. Public routes expose only their approved public scope.

**TC-0847 [P1] Presentation and response contract**

Steps: Open available screens at 1440px and 390px; use keyboard Tab/Enter/Escape; inspect labels, errors, console and network. For API-only module inspect JSON headers/schema instead.

Expected: Readable layout and reachable controls; no clipped actions or blocking JavaScript errors; APIs have documented schema/content type and no stack traces.

**TC-0848 [P1] Create and edit persistence**

Steps: Create valid unique QA fixture using exposed form/API; refresh detail/list; edit one field; refresh again; inspect related records.

Expected: One record is created; intended edit persists; related records and totals stay consistent; success feedback and navigation work.

**TC-0849 [P1] Required fields, boundaries and unexpected input**

Steps: Submit empty required fields, whitespace, duplicate unique values, maximum length +/-1, invalid dates/numbers/foreign IDs and extra privileged fields; resubmit valid input.

Expected: Server rejects invalid input with field-specific feedback and no partial write; permitted values save; privileged extra fields cannot escalate access. Derive exact limits from module request rules before execution.

**TC-0850 [P1] Retry and failed-save behavior**

Steps: Double-submit intended creation; simulate slow/failing request; refresh/back; retry after failure; inspect row count and dependencies.

Expected: Feedback is visible; entered values recoverable; no unintended duplicate or partial business transaction; retry outcome is clear.

**TC-0851 [P1] Room occupancy and distribution**

Steps: Assign guests to room types; change occupancy; export/show and send to sink.

Expected: No duplicate guest assignment; occupancy follows room capacity; output matches selected tour/hotel.

### RoomTypes

**TC-0856 [P0] Guest, limited-role and record ownership access**

Steps: Call module URLs as guest, authorized role and role without permission; replace IDs with another tenant/user records; compare response and database.

Expected: Protected requests require login/API authentication and permission; cross-owner access is rejected; no data mutation or disclosure. Public routes expose only their approved public scope.

**TC-0857 [P1] Presentation and response contract**

Steps: Open available screens at 1440px and 390px; use keyboard Tab/Enter/Escape; inspect labels, errors, console and network. For API-only module inspect JSON headers/schema instead.

Expected: Readable layout and reachable controls; no clipped actions or blocking JavaScript errors; APIs have documented schema/content type and no stack traces.

**TC-0858 [P1] List, search, sort and pagination**

Steps: Seed at least 25 records with mixed text/numeric names; search a match and no match; sort both directions; change page/size; clear filters; reload.

Expected: Rows/counts match filters; correct sort order; pagination stable; useful empty state. Apply only controls exposed by this module; record unsupported controls as N/A.

**TC-0859 [P1] Create and edit persistence**

Steps: Create valid unique QA fixture using exposed form/API; refresh detail/list; edit one field; refresh again; inspect related records.

Expected: One record is created; intended edit persists; related records and totals stay consistent; success feedback and navigation work.

**TC-0860 [P1] Required fields, boundaries and unexpected input**

Steps: Submit empty required fields, whitespace, duplicate unique values, maximum length +/-1, invalid dates/numbers/foreign IDs and extra privileged fields; resubmit valid input.

Expected: Server rejects invalid input with field-specific feedback and no partial write; permitted values save; privileged extra fields cannot escalate access. Derive exact limits from module request rules before execution.

**TC-0861 [P1] Retry and failed-save behavior**

Steps: Double-submit intended creation; simulate slow/failing request; refresh/back; retry after failure; inspect row count and dependencies.

Expected: Feedback is visible; entered values recoverable; no unintended duplicate or partial business transaction; retry outcome is clear.

**TC-0862 [P0] Delete confirmation and dependency safety**

Steps: Cancel deletion of disposable fixture; confirm deletion of unused fixture; attempt deleting linked fixture and missing ID; repeat deletion.

Expected: Cancel preserves record; allowed delete removes only selected fixture; in-use dependencies are protected or follow explicitly approved cascade; repeat/missing IDs never cause server error.

**TC-0863 [P1] Reference data consistency**

Steps: Create valid RoomTypes reference; attempt duplicate/invalid value; use in dependent form; edit then attempt delete while in use.

Expected: Valid reference appears in dependent selectors; validation applies; existing records retain consistent meaning and links.

### ScaffoldInterface\App

**TC-0874 [P0] Guest, limited-role and record ownership access**

Steps: Call module URLs as guest, authorized role and role without permission; replace IDs with another tenant/user records; compare response and database.

Expected: Protected requests require login/API authentication and permission; cross-owner access is rejected; no data mutation or disclosure. Public routes expose only their approved public scope.

**TC-0875 [P1] Presentation and response contract**

Steps: Open available screens at 1440px and 390px; use keyboard Tab/Enter/Escape; inspect labels, errors, console and network. For API-only module inspect JSON headers/schema instead.

Expected: Readable layout and reachable controls; no clipped actions or blocking JavaScript errors; APIs have documented schema/content type and no stack traces.

**TC-0876 [P1] Dashboard consistency**

Steps: Compare tasks, tours, holidays and announcements with source modules; switch calendar period; quick-create event.

Expected: Counts and events agree with scoped source data; quick-create saves once and displays on correct date.

### ScaffoldInterface\Permission

**TC-0886 [P0] Guest, limited-role and record ownership access**

Steps: Call module URLs as guest, authorized role and role without permission; replace IDs with another tenant/user records; compare response and database.

Expected: Protected requests require login/API authentication and permission; cross-owner access is rejected; no data mutation or disclosure. Public routes expose only their approved public scope.

**TC-0887 [P1] Presentation and response contract**

Steps: Open available screens at 1440px and 390px; use keyboard Tab/Enter/Escape; inspect labels, errors, console and network. For API-only module inspect JSON headers/schema instead.

Expected: Readable layout and reachable controls; no clipped actions or blocking JavaScript errors; APIs have documented schema/content type and no stack traces.

**TC-0888 [P1] List, search, sort and pagination**

Steps: Seed at least 25 records with mixed text/numeric names; search a match and no match; sort both directions; change page/size; clear filters; reload.

Expected: Rows/counts match filters; correct sort order; pagination stable; useful empty state. Apply only controls exposed by this module; record unsupported controls as N/A.

**TC-0889 [P1] Create and edit persistence**

Steps: Create valid unique QA fixture using exposed form/API; refresh detail/list; edit one field; refresh again; inspect related records.

Expected: One record is created; intended edit persists; related records and totals stay consistent; success feedback and navigation work.

**TC-0890 [P1] Required fields, boundaries and unexpected input**

Steps: Submit empty required fields, whitespace, duplicate unique values, maximum length +/-1, invalid dates/numbers/foreign IDs and extra privileged fields; resubmit valid input.

Expected: Server rejects invalid input with field-specific feedback and no partial write; permitted values save; privileged extra fields cannot escalate access. Derive exact limits from module request rules before execution.

**TC-0891 [P1] Retry and failed-save behavior**

Steps: Double-submit intended creation; simulate slow/failing request; refresh/back; retry after failure; inspect row count and dependencies.

Expected: Feedback is visible; entered values recoverable; no unintended duplicate or partial business transaction; retry outcome is clear.

**TC-0892 [P0] Delete confirmation and dependency safety**

Steps: Cancel deletion of disposable fixture; confirm deletion of unused fixture; attempt deleting linked fixture and missing ID; repeat deletion.

Expected: Cancel preserves record; allowed delete removes only selected fixture; in-use dependencies are protected or follow explicitly approved cascade; repeat/missing IDs never cause server error.

**TC-0893 [P0] Permission enforcement**

Steps: Create permission and assign through role/direct grant; revoke; call protected route directly.

Expected: Server enforces permission independently of menu visibility; revoked permission cannot authorize requests.

### ScaffoldInterface\Role

**TC-0904 [P0] Guest, limited-role and record ownership access**

Steps: Call module URLs as guest, authorized role and role without permission; replace IDs with another tenant/user records; compare response and database.

Expected: Protected requests require login/API authentication and permission; cross-owner access is rejected; no data mutation or disclosure. Public routes expose only their approved public scope.

**TC-0905 [P1] Presentation and response contract**

Steps: Open available screens at 1440px and 390px; use keyboard Tab/Enter/Escape; inspect labels, errors, console and network. For API-only module inspect JSON headers/schema instead.

Expected: Readable layout and reachable controls; no clipped actions or blocking JavaScript errors; APIs have documented schema/content type and no stack traces.

**TC-0906 [P1] List, search, sort and pagination**

Steps: Seed at least 25 records with mixed text/numeric names; search a match and no match; sort both directions; change page/size; clear filters; reload.

Expected: Rows/counts match filters; correct sort order; pagination stable; useful empty state. Apply only controls exposed by this module; record unsupported controls as N/A.

**TC-0907 [P1] Create and edit persistence**

Steps: Create valid unique QA fixture using exposed form/API; refresh detail/list; edit one field; refresh again; inspect related records.

Expected: One record is created; intended edit persists; related records and totals stay consistent; success feedback and navigation work.

**TC-0908 [P1] Required fields, boundaries and unexpected input**

Steps: Submit empty required fields, whitespace, duplicate unique values, maximum length +/-1, invalid dates/numbers/foreign IDs and extra privileged fields; resubmit valid input.

Expected: Server rejects invalid input with field-specific feedback and no partial write; permitted values save; privileged extra fields cannot escalate access. Derive exact limits from module request rules before execution.

**TC-0909 [P1] Retry and failed-save behavior**

Steps: Double-submit intended creation; simulate slow/failing request; refresh/back; retry after failure; inspect row count and dependencies.

Expected: Feedback is visible; entered values recoverable; no unintended duplicate or partial business transaction; retry outcome is clear.

**TC-0910 [P0] Delete confirmation and dependency safety**

Steps: Cancel deletion of disposable fixture; confirm deletion of unused fixture; attempt deleting linked fixture and missing ID; repeat deletion.

Expected: Cancel preserves record; allowed delete removes only selected fixture; in-use dependencies are protected or follow explicitly approved cascade; repeat/missing IDs never cause server error.

**TC-0911 [P0] Role permission propagation**

Steps: Assign role to A/B; add/revoke permission; refresh sessions; delete role used by users.

Expected: Both users reflect permission changes; no cached excess access; role deletion follows documented dependency rules.

### ScaffoldInterface\User

**TC-0924 [P0] Guest, limited-role and record ownership access**

Steps: Call module URLs as guest, authorized role and role without permission; replace IDs with another tenant/user records; compare response and database.

Expected: Protected requests require login/API authentication and permission; cross-owner access is rejected; no data mutation or disclosure. Public routes expose only their approved public scope.

**TC-0925 [P1] Presentation and response contract**

Steps: Open available screens at 1440px and 390px; use keyboard Tab/Enter/Escape; inspect labels, errors, console and network. For API-only module inspect JSON headers/schema instead.

Expected: Readable layout and reachable controls; no clipped actions or blocking JavaScript errors; APIs have documented schema/content type and no stack traces.

**TC-0926 [P1] List, search, sort and pagination**

Steps: Seed at least 25 records with mixed text/numeric names; search a match and no match; sort both directions; change page/size; clear filters; reload.

Expected: Rows/counts match filters; correct sort order; pagination stable; useful empty state. Apply only controls exposed by this module; record unsupported controls as N/A.

**TC-0927 [P1] Create and edit persistence**

Steps: Create valid unique QA fixture using exposed form/API; refresh detail/list; edit one field; refresh again; inspect related records.

Expected: One record is created; intended edit persists; related records and totals stay consistent; success feedback and navigation work.

**TC-0928 [P1] Required fields, boundaries and unexpected input**

Steps: Submit empty required fields, whitespace, duplicate unique values, maximum length +/-1, invalid dates/numbers/foreign IDs and extra privileged fields; resubmit valid input.

Expected: Server rejects invalid input with field-specific feedback and no partial write; permitted values save; privileged extra fields cannot escalate access. Derive exact limits from module request rules before execution.

**TC-0929 [P1] Retry and failed-save behavior**

Steps: Double-submit intended creation; simulate slow/failing request; refresh/back; retry after failure; inspect row count and dependencies.

Expected: Feedback is visible; entered values recoverable; no unintended duplicate or partial business transaction; retry outcome is clear.

**TC-0930 [P0] Delete confirmation and dependency safety**

Steps: Cancel deletion of disposable fixture; confirm deletion of unused fixture; attempt deleting linked fixture and missing ID; repeat deletion.

Expected: Cancel preserves record; allowed delete removes only selected fixture; in-use dependencies are protected or follow explicitly approved cascade; repeat/missing IDs never cause server error.

**TC-0931 [P0] User roles and permissions**

Steps: Create test user; grant/revoke role and direct permission; sign in separately; try granting own privilege without admin rights.

Expected: Effective permissions update consistently; revoked access disappears; self-escalation is rejected.

### Seasons

**TC-0945 [P0] Guest, limited-role and record ownership access**

Steps: Call module URLs as guest, authorized role and role without permission; replace IDs with another tenant/user records; compare response and database.

Expected: Protected requests require login/API authentication and permission; cross-owner access is rejected; no data mutation or disclosure. Public routes expose only their approved public scope.

**TC-0946 [P1] Presentation and response contract**

Steps: Open available screens at 1440px and 390px; use keyboard Tab/Enter/Escape; inspect labels, errors, console and network. For API-only module inspect JSON headers/schema instead.

Expected: Readable layout and reachable controls; no clipped actions or blocking JavaScript errors; APIs have documented schema/content type and no stack traces.

**TC-0947 [P1] Create and edit persistence**

Steps: Create valid unique QA fixture using exposed form/API; refresh detail/list; edit one field; refresh again; inspect related records.

Expected: One record is created; intended edit persists; related records and totals stay consistent; success feedback and navigation work.

**TC-0948 [P1] Required fields, boundaries and unexpected input**

Steps: Submit empty required fields, whitespace, duplicate unique values, maximum length +/-1, invalid dates/numbers/foreign IDs and extra privileged fields; resubmit valid input.

Expected: Server rejects invalid input with field-specific feedback and no partial write; permitted values save; privileged extra fields cannot escalate access. Derive exact limits from module request rules before execution.

**TC-0949 [P1] Retry and failed-save behavior**

Steps: Double-submit intended creation; simulate slow/failing request; refresh/back; retry after failure; inspect row count and dependencies.

Expected: Feedback is visible; entered values recoverable; no unintended duplicate or partial business transaction; retry outcome is clear.

**TC-0950 [P0] Delete confirmation and dependency safety**

Steps: Cancel deletion of disposable fixture; confirm deletion of unused fixture; attempt deleting linked fixture and missing ID; repeat deletion.

Expected: Cancel preserves record; allowed delete removes only selected fixture; in-use dependencies are protected or follow explicitly approved cascade; repeat/missing IDs never cause server error.

**TC-0951 [P1] Season price boundaries**

Steps: Create seasons spanning month/year edges; assign hotel room types; attempt reversed and overlapping ranges.

Expected: Date and overlap policy is enforced; rates resolve to intended season and room type.

### Services

**TC-0958 [P0] Guest, limited-role and record ownership access**

Steps: Call module URLs as guest, authorized role and role without permission; replace IDs with another tenant/user records; compare response and database.

Expected: Protected requests require login/API authentication and permission; cross-owner access is rejected; no data mutation or disclosure. Public routes expose only their approved public scope.

**TC-0959 [P1] Presentation and response contract**

Steps: Open available screens at 1440px and 390px; use keyboard Tab/Enter/Escape; inspect labels, errors, console and network. For API-only module inspect JSON headers/schema instead.

Expected: Readable layout and reachable controls; no clipped actions or blocking JavaScript errors; APIs have documented schema/content type and no stack traces.

**TC-0960 [P1] List, search, sort and pagination**

Steps: Seed at least 25 records with mixed text/numeric names; search a match and no match; sort both directions; change page/size; clear filters; reload.

Expected: Rows/counts match filters; correct sort order; pagination stable; useful empty state. Apply only controls exposed by this module; record unsupported controls as N/A.

**TC-0961 [P1] Service history**

Steps: Modify service status and price; open history and datatable with paging/filtering.

Expected: History identifies correct service changes; datatable totals and filtered rows agree.

### Setting

**TC-0964 [P0] Guest, limited-role and record ownership access**

Steps: Call module URLs as guest, authorized role and role without permission; replace IDs with another tenant/user records; compare response and database.

Expected: Protected requests require login/API authentication and permission; cross-owner access is rejected; no data mutation or disclosure. Public routes expose only their approved public scope.

**TC-0965 [P1] Presentation and response contract**

Steps: Open available screens at 1440px and 390px; use keyboard Tab/Enter/Escape; inspect labels, errors, console and network. For API-only module inspect JSON headers/schema instead.

Expected: Readable layout and reachable controls; no clipped actions or blocking JavaScript errors; APIs have documented schema/content type and no stack traces.

**TC-0966 [P1] List, search, sort and pagination**

Steps: Seed at least 25 records with mixed text/numeric names; search a match and no match; sort both directions; change page/size; clear filters; reload.

Expected: Rows/counts match filters; correct sort order; pagination stable; useful empty state. Apply only controls exposed by this module; record unsupported controls as N/A.

**TC-0967 [P1] Create and edit persistence**

Steps: Create valid unique QA fixture using exposed form/API; refresh detail/list; edit one field; refresh again; inspect related records.

Expected: One record is created; intended edit persists; related records and totals stay consistent; success feedback and navigation work.

**TC-0968 [P1] Required fields, boundaries and unexpected input**

Steps: Submit empty required fields, whitespace, duplicate unique values, maximum length +/-1, invalid dates/numbers/foreign IDs and extra privileged fields; resubmit valid input.

Expected: Server rejects invalid input with field-specific feedback and no partial write; permitted values save; privileged extra fields cannot escalate access. Derive exact limits from module request rules before execution.

**TC-0969 [P1] Retry and failed-save behavior**

Steps: Double-submit intended creation; simulate slow/failing request; refresh/back; retry after failure; inspect row count and dependencies.

Expected: Feedback is visible; entered values recoverable; no unintended duplicate or partial business transaction; retry outcome is clear.

**TC-0970 [P0] Delete confirmation and dependency safety**

Steps: Cancel deletion of disposable fixture; confirm deletion of unused fixture; attempt deleting linked fixture and missing ID; repeat deletion.

Expected: Cancel preserves record; allowed delete removes only selected fixture; in-use dependencies are protected or follow explicitly approved cascade; repeat/missing IDs never cause server error.

**TC-0971 [P1] Settings persistence**

Steps: Change allowed setting, refresh and restore; submit invalid value and extra administrative field as non-admin.

Expected: Valid setting persists; invalid/unauthorized changes are rejected; credentials are not returned.

### SnappyMail

**TC-0979 [P0] Guest, limited-role and record ownership access**

Steps: Call module URLs as guest, authorized role and role without permission; replace IDs with another tenant/user records; compare response and database.

Expected: Protected requests require login/API authentication and permission; cross-owner access is rejected; no data mutation or disclosure. Public routes expose only their approved public scope.

**TC-0980 [P1] Presentation and response contract**

Steps: Open available screens at 1440px and 390px; use keyboard Tab/Enter/Escape; inspect labels, errors, console and network. For API-only module inspect JSON headers/schema instead.

Expected: Readable layout and reachable controls; no clipped actions or blocking JavaScript errors; APIs have documented schema/content type and no stack traces.

**TC-0981 [P1] Webmail and configuration**

Steps: Open SSO/direct webmail; view status; save valid and invalid sandbox configuration as admin; try as regular user.

Expected: Only intended mailbox opens; admin configuration is protected; secrets never appear in response or logs.

### Status

**TC-0988 [P0] Guest, limited-role and record ownership access**

Steps: Call module URLs as guest, authorized role and role without permission; replace IDs with another tenant/user records; compare response and database.

Expected: Protected requests require login/API authentication and permission; cross-owner access is rejected; no data mutation or disclosure. Public routes expose only their approved public scope.

**TC-0989 [P1] Presentation and response contract**

Steps: Open available screens at 1440px and 390px; use keyboard Tab/Enter/Escape; inspect labels, errors, console and network. For API-only module inspect JSON headers/schema instead.

Expected: Readable layout and reachable controls; no clipped actions or blocking JavaScript errors; APIs have documented schema/content type and no stack traces.

**TC-0990 [P1] List, search, sort and pagination**

Steps: Seed at least 25 records with mixed text/numeric names; search a match and no match; sort both directions; change page/size; clear filters; reload.

Expected: Rows/counts match filters; correct sort order; pagination stable; useful empty state. Apply only controls exposed by this module; record unsupported controls as N/A.

**TC-0991 [P1] Create and edit persistence**

Steps: Create valid unique QA fixture using exposed form/API; refresh detail/list; edit one field; refresh again; inspect related records.

Expected: One record is created; intended edit persists; related records and totals stay consistent; success feedback and navigation work.

**TC-0992 [P1] Required fields, boundaries and unexpected input**

Steps: Submit empty required fields, whitespace, duplicate unique values, maximum length +/-1, invalid dates/numbers/foreign IDs and extra privileged fields; resubmit valid input.

Expected: Server rejects invalid input with field-specific feedback and no partial write; permitted values save; privileged extra fields cannot escalate access. Derive exact limits from module request rules before execution.

**TC-0993 [P1] Retry and failed-save behavior**

Steps: Double-submit intended creation; simulate slow/failing request; refresh/back; retry after failure; inspect row count and dependencies.

Expected: Feedback is visible; entered values recoverable; no unintended duplicate or partial business transaction; retry outcome is clear.

**TC-0994 [P0] Delete confirmation and dependency safety**

Steps: Cancel deletion of disposable fixture; confirm deletion of unused fixture; attempt deleting linked fixture and missing ID; repeat deletion.

Expected: Cancel preserves record; allowed delete removes only selected fixture; in-use dependencies are protected or follow explicitly approved cascade; repeat/missing IDs never cause server error.

**TC-0995 [P1] Reference data consistency**

Steps: Create valid Status reference; attempt duplicate/invalid value; use in dependent form; edit then attempt delete while in use.

Expected: Valid reference appears in dependent selectors; validation applies; existing records retain consistent meaning and links.

### SupplierSearch

**TC-1006 [P0] Guest, limited-role and record ownership access**

Steps: Call module URLs as guest, authorized role and role without permission; replace IDs with another tenant/user records; compare response and database.

Expected: Protected requests require login/API authentication and permission; cross-owner access is rejected; no data mutation or disclosure. Public routes expose only their approved public scope.

**TC-1007 [P1] Presentation and response contract**

Steps: Open available screens at 1440px and 390px; use keyboard Tab/Enter/Escape; inspect labels, errors, console and network. For API-only module inspect JSON headers/schema instead.

Expected: Readable layout and reachable controls; no clipped actions or blocking JavaScript errors; APIs have documented schema/content type and no stack traces.

**TC-1008 [P1] List, search, sort and pagination**

Steps: Seed at least 25 records with mixed text/numeric names; search a match and no match; sort both directions; change page/size; clear filters; reload.

Expected: Rows/counts match filters; correct sort order; pagination stable; useful empty state. Apply only controls exposed by this module; record unsupported controls as N/A.

**TC-1009 [P1] Search combinations**

Steps: Search each supplier type using country, city and criteria; combine filters; clear filters; use no-match input.

Expected: Results satisfy all active filters; clearing restores results; empty state and supplier links work.

### System endpoints

**TC-1014 [P0] Guest, limited-role and record ownership access**

Steps: Call module URLs as guest, authorized role and role without permission; replace IDs with another tenant/user records; compare response and database.

Expected: Protected requests require login/API authentication and permission; cross-owner access is rejected; no data mutation or disclosure. Public routes expose only their approved public scope.

**TC-1015 [P1] Presentation and response contract**

Steps: Open available screens at 1440px and 390px; use keyboard Tab/Enter/Escape; inspect labels, errors, console and network. For API-only module inspect JSON headers/schema instead.

Expected: Readable layout and reachable controls; no clipped actions or blocking JavaScript errors; APIs have documented schema/content type and no stack traces.

**TC-1016 [P1] Maintenance and broadcast access**

Steps: Check maintenance endpoints as guest/regular user/admin in staging; authorize private channel as member/nonmember.

Expected: Maintenance is restricted to intended operators; private channels require valid membership; no operational secrets leak.

### TMSClient\Login

**TC-1021 [P0] Guest, limited-role and record ownership access**

Steps: Call module URLs as guest, authorized role and role without permission; replace IDs with another tenant/user records; compare response and database.

Expected: Protected requests require login/API authentication and permission; cross-owner access is rejected; no data mutation or disclosure. Public routes expose only their approved public scope.

**TC-1022 [P1] Presentation and response contract**

Steps: Open available screens at 1440px and 390px; use keyboard Tab/Enter/Escape; inspect labels, errors, console and network. For API-only module inspect JSON headers/schema instead.

Expected: Readable layout and reachable controls; no clipped actions or blocking JavaScript errors; APIs have documented schema/content type and no stack traces.

**TC-1023 [P1] List, search, sort and pagination**

Steps: Seed at least 25 records with mixed text/numeric names; search a match and no match; sort both directions; change page/size; clear filters; reload.

Expected: Rows/counts match filters; correct sort order; pagination stable; useful empty state. Apply only controls exposed by this module; record unsupported controls as N/A.

**TC-1024 [P0] Client portal isolation**

Steps: Login client A and B separately; open home and quotation requests; replace client/tour IDs; logout.

Expected: Each client sees only its own records; cross-client and staff-only actions are rejected; logout removes access.

### TMSClient\Modal

**TC-1030 [P0] Guest, limited-role and record ownership access**

Steps: Call module URLs as guest, authorized role and role without permission; replace IDs with another tenant/user records; compare response and database.

Expected: Protected requests require login/API authentication and permission; cross-owner access is rejected; no data mutation or disclosure. Public routes expose only their approved public scope.

**TC-1031 [P1] Presentation and response contract**

Steps: Open available screens at 1440px and 390px; use keyboard Tab/Enter/Escape; inspect labels, errors, console and network. For API-only module inspect JSON headers/schema instead.

Expected: Readable layout and reachable controls; no clipped actions or blocking JavaScript errors; APIs have documented schema/content type and no stack traces.

**TC-1032 [P1] Client modal data**

Steps: Load selection modal and dependent dropdowns for own tour; substitute another client package/tour identifier.

Expected: Only permitted supplier/package options are returned; cross-client data never appears in HTML or JSON.

### TMSClient\Tour

**TC-1037 [P0] Guest, limited-role and record ownership access**

Steps: Call module URLs as guest, authorized role and role without permission; replace IDs with another tenant/user records; compare response and database.

Expected: Protected requests require login/API authentication and permission; cross-owner access is rejected; no data mutation or disclosure. Public routes expose only their approved public scope.

**TC-1038 [P1] Presentation and response contract**

Steps: Open available screens at 1440px and 390px; use keyboard Tab/Enter/Escape; inspect labels, errors, console and network. For API-only module inspect JSON headers/schema instead.

Expected: Readable layout and reachable controls; no clipped actions or blocking JavaScript errors; APIs have documented schema/content type and no stack traces.

**TC-1039 [P1] List, search, sort and pagination**

Steps: Seed at least 25 records with mixed text/numeric names; search a match and no match; sort both directions; change page/size; clear filters; reload.

Expected: Rows/counts match filters; correct sort order; pagination stable; useful empty state. Apply only controls exposed by this module; record unsupported controls as N/A.

**TC-1040 [P1] Create and edit persistence**

Steps: Create valid unique QA fixture using exposed form/API; refresh detail/list; edit one field; refresh again; inspect related records.

Expected: One record is created; intended edit persists; related records and totals stay consistent; success feedback and navigation work.

**TC-1041 [P1] Required fields, boundaries and unexpected input**

Steps: Submit empty required fields, whitespace, duplicate unique values, maximum length +/-1, invalid dates/numbers/foreign IDs and extra privileged fields; resubmit valid input.

Expected: Server rejects invalid input with field-specific feedback and no partial write; permitted values save; privileged extra fields cannot escalate access. Derive exact limits from module request rules before execution.

**TC-1042 [P1] Retry and failed-save behavior**

Steps: Double-submit intended creation; simulate slow/failing request; refresh/back; retry after failure; inspect row count and dependencies.

Expected: Feedback is visible; entered values recoverable; no unintended duplicate or partial business transaction; retry outcome is clear.

**TC-1043 [P0] Delete confirmation and dependency safety**

Steps: Cancel deletion of disposable fixture; confirm deletion of unused fixture; attempt deleting linked fixture and missing ID; repeat deletion.

Expected: Cancel preserves record; allowed delete removes only selected fixture; in-use dependencies are protected or follow explicitly approved cascade; repeat/missing IDs never cause server error.

**TC-1044 [P1] Client tour workflow**

Steps: As client A create full and simple tours, edit own, inspect package and try client B tour ID.

Expected: Allowed fields persist; ownership is enforced; clients cannot modify internal pricing/status through extra fields.

### TMSClient\TourPackage

**TC-1053 [P0] Guest, limited-role and record ownership access**

Steps: Call module URLs as guest, authorized role and role without permission; replace IDs with another tenant/user records; compare response and database.

Expected: Protected requests require login/API authentication and permission; cross-owner access is rejected; no data mutation or disclosure. Public routes expose only their approved public scope.

**TC-1054 [P1] Presentation and response contract**

Steps: Open available screens at 1440px and 390px; use keyboard Tab/Enter/Escape; inspect labels, errors, console and network. For API-only module inspect JSON headers/schema instead.

Expected: Readable layout and reachable controls; no clipped actions or blocking JavaScript errors; APIs have documented schema/content type and no stack traces.

**TC-1055 [P1] List, search, sort and pagination**

Steps: Seed at least 25 records with mixed text/numeric names; search a match and no match; sort both directions; change page/size; clear filters; reload.

Expected: Rows/counts match filters; correct sort order; pagination stable; useful empty state. Apply only controls exposed by this module; record unsupported controls as N/A.

**TC-1056 [P1] Create and edit persistence**

Steps: Create valid unique QA fixture using exposed form/API; refresh detail/list; edit one field; refresh again; inspect related records.

Expected: One record is created; intended edit persists; related records and totals stay consistent; success feedback and navigation work.

**TC-1057 [P1] Required fields, boundaries and unexpected input**

Steps: Submit empty required fields, whitespace, duplicate unique values, maximum length +/-1, invalid dates/numbers/foreign IDs and extra privileged fields; resubmit valid input.

Expected: Server rejects invalid input with field-specific feedback and no partial write; permitted values save; privileged extra fields cannot escalate access. Derive exact limits from module request rules before execution.

**TC-1058 [P1] Retry and failed-save behavior**

Steps: Double-submit intended creation; simulate slow/failing request; refresh/back; retry after failure; inspect row count and dependencies.

Expected: Feedback is visible; entered values recoverable; no unintended duplicate or partial business transaction; retry outcome is clear.

**TC-1059 [P0] Delete confirmation and dependency safety**

Steps: Cancel deletion of disposable fixture; confirm deletion of unused fixture; attempt deleting linked fixture and missing ID; repeat deletion.

Expected: Cancel preserves record; allowed delete removes only selected fixture; in-use dependencies are protected or follow explicitly approved cascade; repeat/missing IDs never cause server error.

**TC-1060 [P0] Client package isolation**

Steps: View/create/edit own tour package; replace package or parent tour ID with client B identifiers.

Expected: Only owned permitted packages are accessible; staff-only fields and unrelated packages stay unchanged.

### TMSClient\Upload

**TC-1068 [P0] Guest, limited-role and record ownership access**

Steps: Call module URLs as guest, authorized role and role without permission; replace IDs with another tenant/user records; compare response and database.

Expected: Protected requests require login/API authentication and permission; cross-owner access is rejected; no data mutation or disclosure. Public routes expose only their approved public scope.

**TC-1069 [P1] Presentation and response contract**

Steps: Open available screens at 1440px and 390px; use keyboard Tab/Enter/Escape; inspect labels, errors, console and network. For API-only module inspect JSON headers/schema instead.

Expected: Readable layout and reachable controls; no clipped actions or blocking JavaScript errors; APIs have documented schema/content type and no stack traces.

**TC-1070 [P1] Client spreadsheet upload**

Steps: Download sample; preview and upload valid sheet; try malformed/oversized file and another client tour ID.

Expected: Template matches parser; useful errors appear; imported tours belong only to signed-in client.

### TMSSupplier\Login

**TC-1074 [P0] Guest, limited-role and record ownership access**

Steps: Call module URLs as guest, authorized role and role without permission; replace IDs with another tenant/user records; compare response and database.

Expected: Protected requests require login/API authentication and permission; cross-owner access is rejected; no data mutation or disclosure. Public routes expose only their approved public scope.

**TC-1075 [P1] Presentation and response contract**

Steps: Open available screens at 1440px and 390px; use keyboard Tab/Enter/Escape; inspect labels, errors, console and network. For API-only module inspect JSON headers/schema instead.

Expected: Readable layout and reachable controls; no clipped actions or blocking JavaScript errors; APIs have documented schema/content type and no stack traces.

**TC-1076 [P1] List, search, sort and pagination**

Steps: Seed at least 25 records with mixed text/numeric names; search a match and no match; sort both directions; change page/size; clear filters; reload.

Expected: Rows/counts match filters; correct sort order; pagination stable; useful empty state. Apply only controls exposed by this module; record unsupported controls as N/A.

**TC-1077 [P0] Supplier portal isolation**

Steps: Login supplier A/B separately; inspect home; try client/staff routes; logout and revisit home.

Expected: Only supplier-owned records are visible; portal boundaries hold; logged-out requests require authentication.

### TMSSupplier\Offer

**TC-1082 [P0] Guest, limited-role and record ownership access**

Steps: Call module URLs as guest, authorized role and role without permission; replace IDs with another tenant/user records; compare response and database.

Expected: Protected requests require login/API authentication and permission; cross-owner access is rejected; no data mutation or disclosure. Public routes expose only their approved public scope.

**TC-1083 [P1] Presentation and response contract**

Steps: Open available screens at 1440px and 390px; use keyboard Tab/Enter/Escape; inspect labels, errors, console and network. For API-only module inspect JSON headers/schema instead.

Expected: Readable layout and reachable controls; no clipped actions or blocking JavaScript errors; APIs have documented schema/content type and no stack traces.

**TC-1084 [P1] Supplier offer comment**

Steps: Post comment for own offer; try another supplier offer ID and unauthenticated post.

Expected: Only authorized supplier can comment; unrelated offers remain unchanged; text is safely rendered.

### Task

**TC-1086 [P0] Guest, limited-role and record ownership access**

Steps: Call module URLs as guest, authorized role and role without permission; replace IDs with another tenant/user records; compare response and database.

Expected: Protected requests require login/API authentication and permission; cross-owner access is rejected; no data mutation or disclosure. Public routes expose only their approved public scope.

**TC-1087 [P1] Presentation and response contract**

Steps: Open available screens at 1440px and 390px; use keyboard Tab/Enter/Escape; inspect labels, errors, console and network. For API-only module inspect JSON headers/schema instead.

Expected: Readable layout and reachable controls; no clipped actions or blocking JavaScript errors; APIs have documented schema/content type and no stack traces.

**TC-1088 [P1] List, search, sort and pagination**

Steps: Seed at least 25 records with mixed text/numeric names; search a match and no match; sort both directions; change page/size; clear filters; reload.

Expected: Rows/counts match filters; correct sort order; pagination stable; useful empty state. Apply only controls exposed by this module; record unsupported controls as N/A.

**TC-1089 [P1] Create and edit persistence**

Steps: Create valid unique QA fixture using exposed form/API; refresh detail/list; edit one field; refresh again; inspect related records.

Expected: One record is created; intended edit persists; related records and totals stay consistent; success feedback and navigation work.

**TC-1090 [P1] Required fields, boundaries and unexpected input**

Steps: Submit empty required fields, whitespace, duplicate unique values, maximum length +/-1, invalid dates/numbers/foreign IDs and extra privileged fields; resubmit valid input.

Expected: Server rejects invalid input with field-specific feedback and no partial write; permitted values save; privileged extra fields cannot escalate access. Derive exact limits from module request rules before execution.

**TC-1091 [P1] Retry and failed-save behavior**

Steps: Double-submit intended creation; simulate slow/failing request; refresh/back; retry after failure; inspect row count and dependencies.

Expected: Feedback is visible; entered values recoverable; no unintended duplicate or partial business transaction; retry outcome is clear.

**TC-1092 [P0] Delete confirmation and dependency safety**

Steps: Cancel deletion of disposable fixture; confirm deletion of unused fixture; attempt deleting linked fixture and missing ID; repeat deletion.

Expected: Cancel preserves record; allowed delete removes only selected fixture; in-use dependencies are protected or follow explicitly approved cascade; repeat/missing IDs never cause server error.

**TC-1093 [P1] Task lifecycle and calendar**

Steps: Create task with assignee/due date; change status via list, inline field and calendar; check overdue list.

Expected: All views show same task state; overdue rules respect timezone; unauthorized reassignment is blocked.

### Tax

**TC-1107 [P0] Guest, limited-role and record ownership access**

Steps: Call module URLs as guest, authorized role and role without permission; replace IDs with another tenant/user records; compare response and database.

Expected: Protected requests require login/API authentication and permission; cross-owner access is rejected; no data mutation or disclosure. Public routes expose only their approved public scope.

**TC-1108 [P1] Presentation and response contract**

Steps: Open available screens at 1440px and 390px; use keyboard Tab/Enter/Escape; inspect labels, errors, console and network. For API-only module inspect JSON headers/schema instead.

Expected: Readable layout and reachable controls; no clipped actions or blocking JavaScript errors; APIs have documented schema/content type and no stack traces.

**TC-1109 [P1] List, search, sort and pagination**

Steps: Seed at least 25 records with mixed text/numeric names; search a match and no match; sort both directions; change page/size; clear filters; reload.

Expected: Rows/counts match filters; correct sort order; pagination stable; useful empty state. Apply only controls exposed by this module; record unsupported controls as N/A.

**TC-1110 [P1] Create and edit persistence**

Steps: Create valid unique QA fixture using exposed form/API; refresh detail/list; edit one field; refresh again; inspect related records.

Expected: One record is created; intended edit persists; related records and totals stay consistent; success feedback and navigation work.

**TC-1111 [P1] Required fields, boundaries and unexpected input**

Steps: Submit empty required fields, whitespace, duplicate unique values, maximum length +/-1, invalid dates/numbers/foreign IDs and extra privileged fields; resubmit valid input.

Expected: Server rejects invalid input with field-specific feedback and no partial write; permitted values save; privileged extra fields cannot escalate access. Derive exact limits from module request rules before execution.

**TC-1112 [P1] Retry and failed-save behavior**

Steps: Double-submit intended creation; simulate slow/failing request; refresh/back; retry after failure; inspect row count and dependencies.

Expected: Feedback is visible; entered values recoverable; no unintended duplicate or partial business transaction; retry outcome is clear.

**TC-1113 [P0] Delete confirmation and dependency safety**

Steps: Cancel deletion of disposable fixture; confirm deletion of unused fixture; attempt deleting linked fixture and missing ID; repeat deletion.

Expected: Cancel preserves record; allowed delete removes only selected fixture; in-use dependencies are protected or follow explicitly approved cascade; repeat/missing IDs never cause server error.

**TC-1114 [P1] Tax arithmetic**

Steps: Create zero and fractional percentage taxes; attempt negative and invalid rates; apply to invoice lines.

Expected: Valid tax calculates with consistent rounding; invalid values rejected; existing invoice history is not silently rewritten.

### Templates

**TC-1124 [P0] Guest, limited-role and record ownership access**

Steps: Call module URLs as guest, authorized role and role without permission; replace IDs with another tenant/user records; compare response and database.

Expected: Protected requests require login/API authentication and permission; cross-owner access is rejected; no data mutation or disclosure. Public routes expose only their approved public scope.

**TC-1125 [P1] Presentation and response contract**

Steps: Open available screens at 1440px and 390px; use keyboard Tab/Enter/Escape; inspect labels, errors, console and network. For API-only module inspect JSON headers/schema instead.

Expected: Readable layout and reachable controls; no clipped actions or blocking JavaScript errors; APIs have documented schema/content type and no stack traces.

**TC-1126 [P1] List, search, sort and pagination**

Steps: Seed at least 25 records with mixed text/numeric names; search a match and no match; sort both directions; change page/size; clear filters; reload.

Expected: Rows/counts match filters; correct sort order; pagination stable; useful empty state. Apply only controls exposed by this module; record unsupported controls as N/A.

**TC-1127 [P1] Create and edit persistence**

Steps: Create valid unique QA fixture using exposed form/API; refresh detail/list; edit one field; refresh again; inspect related records.

Expected: One record is created; intended edit persists; related records and totals stay consistent; success feedback and navigation work.

**TC-1128 [P1] Required fields, boundaries and unexpected input**

Steps: Submit empty required fields, whitespace, duplicate unique values, maximum length +/-1, invalid dates/numbers/foreign IDs and extra privileged fields; resubmit valid input.

Expected: Server rejects invalid input with field-specific feedback and no partial write; permitted values save; privileged extra fields cannot escalate access. Derive exact limits from module request rules before execution.

**TC-1129 [P1] Retry and failed-save behavior**

Steps: Double-submit intended creation; simulate slow/failing request; refresh/back; retry after failure; inspect row count and dependencies.

Expected: Feedback is visible; entered values recoverable; no unintended duplicate or partial business transaction; retry outcome is clear.

**TC-1130 [P0] Delete confirmation and dependency safety**

Steps: Cancel deletion of disposable fixture; confirm deletion of unused fixture; attempt deleting linked fixture and missing ID; repeat deletion.

Expected: Cancel preserves record; allowed delete removes only selected fixture; in-use dependencies are protected or follow explicitly approved cascade; repeat/missing IDs never cause server error.

**TC-1131 [P1] Template variables and sending**

Steps: Load service/description template; render missing and Unicode values; reply and send through sink.

Expected: Placeholders resolve safely; missing values are handled; recipients and context match intended service.

### Tour

**TC-1146 [P0] Guest, limited-role and record ownership access**

Steps: Call module URLs as guest, authorized role and role without permission; replace IDs with another tenant/user records; compare response and database.

Expected: Protected requests require login/API authentication and permission; cross-owner access is rejected; no data mutation or disclosure. Public routes expose only their approved public scope.

**TC-1147 [P1] Presentation and response contract**

Steps: Open available screens at 1440px and 390px; use keyboard Tab/Enter/Escape; inspect labels, errors, console and network. For API-only module inspect JSON headers/schema instead.

Expected: Readable layout and reachable controls; no clipped actions or blocking JavaScript errors; APIs have documented schema/content type and no stack traces.

**TC-1148 [P1] List, search, sort and pagination**

Steps: Seed at least 25 records with mixed text/numeric names; search a match and no match; sort both directions; change page/size; clear filters; reload.

Expected: Rows/counts match filters; correct sort order; pagination stable; useful empty state. Apply only controls exposed by this module; record unsupported controls as N/A.

**TC-1149 [P1] Create and edit persistence**

Steps: Create valid unique QA fixture using exposed form/API; refresh detail/list; edit one field; refresh again; inspect related records.

Expected: One record is created; intended edit persists; related records and totals stay consistent; success feedback and navigation work.

**TC-1150 [P1] Required fields, boundaries and unexpected input**

Steps: Submit empty required fields, whitespace, duplicate unique values, maximum length +/-1, invalid dates/numbers/foreign IDs and extra privileged fields; resubmit valid input.

Expected: Server rejects invalid input with field-specific feedback and no partial write; permitted values save; privileged extra fields cannot escalate access. Derive exact limits from module request rules before execution.

**TC-1151 [P1] Retry and failed-save behavior**

Steps: Double-submit intended creation; simulate slow/failing request; refresh/back; retry after failure; inspect row count and dependencies.

Expected: Feedback is visible; entered values recoverable; no unintended duplicate or partial business transaction; retry outcome is clear.

**TC-1152 [P0] Delete confirmation and dependency safety**

Steps: Cancel deletion of disposable fixture; confirm deletion of unused fixture; attempt deleting linked fixture and missing ID; repeat deletion.

Expected: Cancel preserves record; allowed delete removes only selected fixture; in-use dependencies are protected or follow explicitly approved cascade; repeat/missing IDs never cause server error.

**TC-1153 [P1] Tour lifecycle**

Steps: Create a future multi-day tour with client, passengers and services; edit dates; convert to quotation and back; clone.

Expected: Dates, references and status remain consistent; clone has independent editable records and no duplicated financial entries.

**TC-1154 [P1] Price and passenger boundaries**

Steps: Try negative price; zero passengers; free passengers exceeding passengers; reversed dates; edit price then refresh.

Expected: Invalid values are rejected; valid price persists and dependent totals recalculate consistently.

**TC-1155 [P1] Confirmed hotel protection**

Steps: Attempt to remove or reschedule a day with a confirmed hotel service; cancel confirmation and retry.

Expected: Confirmed booking rules prevent accidental loss; allowed changes affect only the chosen tour/day.

**TC-1156 [P1] Exports and public page**

Steps: Export PDF, document and HTML with long descriptions and Unicode; open landing page as guest.

Expected: Downloads open with correct data and layout; public page reveals only approved public fields.

### TourExpense

**TC-1197 [P0] Guest, limited-role and record ownership access**

Steps: Call module URLs as guest, authorized role and role without permission; replace IDs with another tenant/user records; compare response and database.

Expected: Protected requests require login/API authentication and permission; cross-owner access is rejected; no data mutation or disclosure. Public routes expose only their approved public scope.

**TC-1198 [P1] Presentation and response contract**

Steps: Open available screens at 1440px and 390px; use keyboard Tab/Enter/Escape; inspect labels, errors, console and network. For API-only module inspect JSON headers/schema instead.

Expected: Readable layout and reachable controls; no clipped actions or blocking JavaScript errors; APIs have documented schema/content type and no stack traces.

**TC-1199 [P1] List, search, sort and pagination**

Steps: Seed at least 25 records with mixed text/numeric names; search a match and no match; sort both directions; change page/size; clear filters; reload.

Expected: Rows/counts match filters; correct sort order; pagination stable; useful empty state. Apply only controls exposed by this module; record unsupported controls as N/A.

**TC-1200 [P1] Create and edit persistence**

Steps: Create valid unique QA fixture using exposed form/API; refresh detail/list; edit one field; refresh again; inspect related records.

Expected: One record is created; intended edit persists; related records and totals stay consistent; success feedback and navigation work.

**TC-1201 [P1] Required fields, boundaries and unexpected input**

Steps: Submit empty required fields, whitespace, duplicate unique values, maximum length +/-1, invalid dates/numbers/foreign IDs and extra privileged fields; resubmit valid input.

Expected: Server rejects invalid input with field-specific feedback and no partial write; permitted values save; privileged extra fields cannot escalate access. Derive exact limits from module request rules before execution.

**TC-1202 [P1] Retry and failed-save behavior**

Steps: Double-submit intended creation; simulate slow/failing request; refresh/back; retry after failure; inspect row count and dependencies.

Expected: Feedback is visible; entered values recoverable; no unintended duplicate or partial business transaction; retry outcome is clear.

**TC-1203 [P0] Delete confirmation and dependency safety**

Steps: Cancel deletion of disposable fixture; confirm deletion of unused fixture; attempt deleting linked fixture and missing ID; repeat deletion.

Expected: Cancel preserves record; allowed delete removes only selected fixture; in-use dependencies are protected or follow explicitly approved cascade; repeat/missing IDs never cause server error.

**TC-1204 [P1] Tour expense allocation**

Steps: Create expense on tour A in office A; edit amount; try missing tour and foreign office reference.

Expected: Expense affects only correct tour/office; invalid relationships rejected; balances update once.

### TourPackage

**TC-1216 [P0] Guest, limited-role and record ownership access**

Steps: Call module URLs as guest, authorized role and role without permission; replace IDs with another tenant/user records; compare response and database.

Expected: Protected requests require login/API authentication and permission; cross-owner access is rejected; no data mutation or disclosure. Public routes expose only their approved public scope.

**TC-1217 [P1] Presentation and response contract**

Steps: Open available screens at 1440px and 390px; use keyboard Tab/Enter/Escape; inspect labels, errors, console and network. For API-only module inspect JSON headers/schema instead.

Expected: Readable layout and reachable controls; no clipped actions or blocking JavaScript errors; APIs have documented schema/content type and no stack traces.

**TC-1218 [P1] List, search, sort and pagination**

Steps: Seed at least 25 records with mixed text/numeric names; search a match and no match; sort both directions; change page/size; clear filters; reload.

Expected: Rows/counts match filters; correct sort order; pagination stable; useful empty state. Apply only controls exposed by this module; record unsupported controls as N/A.

**TC-1219 [P1] Create and edit persistence**

Steps: Create valid unique QA fixture using exposed form/API; refresh detail/list; edit one field; refresh again; inspect related records.

Expected: One record is created; intended edit persists; related records and totals stay consistent; success feedback and navigation work.

**TC-1220 [P1] Required fields, boundaries and unexpected input**

Steps: Submit empty required fields, whitespace, duplicate unique values, maximum length +/-1, invalid dates/numbers/foreign IDs and extra privileged fields; resubmit valid input.

Expected: Server rejects invalid input with field-specific feedback and no partial write; permitted values save; privileged extra fields cannot escalate access. Derive exact limits from module request rules before execution.

**TC-1221 [P1] Retry and failed-save behavior**

Steps: Double-submit intended creation; simulate slow/failing request; refresh/back; retry after failure; inspect row count and dependencies.

Expected: Feedback is visible; entered values recoverable; no unintended duplicate or partial business transaction; retry outcome is clear.

**TC-1222 [P0] Delete confirmation and dependency safety**

Steps: Cancel deletion of disposable fixture; confirm deletion of unused fixture; attempt deleting linked fixture and missing ID; repeat deletion.

Expected: Cancel preserves record; allowed delete removes only selected fixture; in-use dependencies are protected or follow explicitly approved cascade; repeat/missing IDs never cause server error.

**TC-1223 [P1] Service scheduling**

Steps: Add hotel, event, guide, transfer, restaurant, cruise and flight services to days; reorder and change times.

Expected: Each supported type saves correct supplier/day/time; ordering persists and totals remain consistent.

**TC-1224 [P1] Hotel dates and room types**

Steps: Set hotel check-in/out, main hotel and room types; change day; inspect last-date and date lookup responses.

Expected: Room options match hotel; dates stay within intended itinerary; day references remain valid.

**TC-1225 [P1] Status and cancellation**

Steps: Confirm service, cancel it, delete cancelled packages and remove one transfer; inspect history.

Expected: Only selected eligible services change; confirmed bookings remain protected; history identifies changes.

### Transaction

**TC-1259 [P0] Guest, limited-role and record ownership access**

Steps: Call module URLs as guest, authorized role and role without permission; replace IDs with another tenant/user records; compare response and database.

Expected: Protected requests require login/API authentication and permission; cross-owner access is rejected; no data mutation or disclosure. Public routes expose only their approved public scope.

**TC-1260 [P1] Presentation and response contract**

Steps: Open available screens at 1440px and 390px; use keyboard Tab/Enter/Escape; inspect labels, errors, console and network. For API-only module inspect JSON headers/schema instead.

Expected: Readable layout and reachable controls; no clipped actions or blocking JavaScript errors; APIs have documented schema/content type and no stack traces.

**TC-1261 [P1] List, search, sort and pagination**

Steps: Seed at least 25 records with mixed text/numeric names; search a match and no match; sort both directions; change page/size; clear filters; reload.

Expected: Rows/counts match filters; correct sort order; pagination stable; useful empty state. Apply only controls exposed by this module; record unsupported controls as N/A.

**TC-1262 [P1] Create and edit persistence**

Steps: Create valid unique QA fixture using exposed form/API; refresh detail/list; edit one field; refresh again; inspect related records.

Expected: One record is created; intended edit persists; related records and totals stay consistent; success feedback and navigation work.

**TC-1263 [P1] Required fields, boundaries and unexpected input**

Steps: Submit empty required fields, whitespace, duplicate unique values, maximum length +/-1, invalid dates/numbers/foreign IDs and extra privileged fields; resubmit valid input.

Expected: Server rejects invalid input with field-specific feedback and no partial write; permitted values save; privileged extra fields cannot escalate access. Derive exact limits from module request rules before execution.

**TC-1264 [P1] Retry and failed-save behavior**

Steps: Double-submit intended creation; simulate slow/failing request; refresh/back; retry after failure; inspect row count and dependencies.

Expected: Feedback is visible; entered values recoverable; no unintended duplicate or partial business transaction; retry outcome is clear.

**TC-1265 [P0] Delete confirmation and dependency safety**

Steps: Cancel deletion of disposable fixture; confirm deletion of unused fixture; attempt deleting linked fixture and missing ID; repeat deletion.

Expected: Cancel preserves record; allowed delete removes only selected fixture; in-use dependencies are protected or follow explicitly approved cascade; repeat/missing IDs never cause server error.

**TC-1266 [P1] Ledger reconciliation**

Steps: Record credit and debit with known amounts; edit or delete one allowed entry; compare invoice and office balances.

Expected: Exactly one intended ledger change occurs; linked balances and reports reconcile with preserved audit history.

### Transfer

**TC-1276 [P0] Guest, limited-role and record ownership access**

Steps: Call module URLs as guest, authorized role and role without permission; replace IDs with another tenant/user records; compare response and database.

Expected: Protected requests require login/API authentication and permission; cross-owner access is rejected; no data mutation or disclosure. Public routes expose only their approved public scope.

**TC-1277 [P1] Presentation and response contract**

Steps: Open available screens at 1440px and 390px; use keyboard Tab/Enter/Escape; inspect labels, errors, console and network. For API-only module inspect JSON headers/schema instead.

Expected: Readable layout and reachable controls; no clipped actions or blocking JavaScript errors; APIs have documented schema/content type and no stack traces.

**TC-1278 [P1] List, search, sort and pagination**

Steps: Seed at least 25 records with mixed text/numeric names; search a match and no match; sort both directions; change page/size; clear filters; reload.

Expected: Rows/counts match filters; correct sort order; pagination stable; useful empty state. Apply only controls exposed by this module; record unsupported controls as N/A.

**TC-1279 [P1] Create and edit persistence**

Steps: Create valid unique QA fixture using exposed form/API; refresh detail/list; edit one field; refresh again; inspect related records.

Expected: One record is created; intended edit persists; related records and totals stay consistent; success feedback and navigation work.

**TC-1280 [P1] Required fields, boundaries and unexpected input**

Steps: Submit empty required fields, whitespace, duplicate unique values, maximum length +/-1, invalid dates/numbers/foreign IDs and extra privileged fields; resubmit valid input.

Expected: Server rejects invalid input with field-specific feedback and no partial write; permitted values save; privileged extra fields cannot escalate access. Derive exact limits from module request rules before execution.

**TC-1281 [P1] Retry and failed-save behavior**

Steps: Double-submit intended creation; simulate slow/failing request; refresh/back; retry after failure; inspect row count and dependencies.

Expected: Feedback is visible; entered values recoverable; no unintended duplicate or partial business transaction; retry outcome is clear.

**TC-1282 [P0] Delete confirmation and dependency safety**

Steps: Cancel deletion of disposable fixture; confirm deletion of unused fixture; attempt deleting linked fixture and missing ID; repeat deletion.

Expected: Cancel preserves record; allowed delete removes only selected fixture; in-use dependencies are protected or follow explicitly approved cascade; repeat/missing IDs never cause server error.

**TC-1283 [P1] Company-dependent resources**

Steps: Choose company A then B in trip form; inspect buses/drivers; edit a company with linked resources.

Expected: Options belong to selected company; stale selections clear; linked resource consistency is preserved.

### UtilityExpense

**TC-1294 [P0] Guest, limited-role and record ownership access**

Steps: Call module URLs as guest, authorized role and role without permission; replace IDs with another tenant/user records; compare response and database.

Expected: Protected requests require login/API authentication and permission; cross-owner access is rejected; no data mutation or disclosure. Public routes expose only their approved public scope.

**TC-1295 [P1] Presentation and response contract**

Steps: Open available screens at 1440px and 390px; use keyboard Tab/Enter/Escape; inspect labels, errors, console and network. For API-only module inspect JSON headers/schema instead.

Expected: Readable layout and reachable controls; no clipped actions or blocking JavaScript errors; APIs have documented schema/content type and no stack traces.

**TC-1296 [P1] List, search, sort and pagination**

Steps: Seed at least 25 records with mixed text/numeric names; search a match and no match; sort both directions; change page/size; clear filters; reload.

Expected: Rows/counts match filters; correct sort order; pagination stable; useful empty state. Apply only controls exposed by this module; record unsupported controls as N/A.

**TC-1297 [P1] Create and edit persistence**

Steps: Create valid unique QA fixture using exposed form/API; refresh detail/list; edit one field; refresh again; inspect related records.

Expected: One record is created; intended edit persists; related records and totals stay consistent; success feedback and navigation work.

**TC-1298 [P1] Required fields, boundaries and unexpected input**

Steps: Submit empty required fields, whitespace, duplicate unique values, maximum length +/-1, invalid dates/numbers/foreign IDs and extra privileged fields; resubmit valid input.

Expected: Server rejects invalid input with field-specific feedback and no partial write; permitted values save; privileged extra fields cannot escalate access. Derive exact limits from module request rules before execution.

**TC-1299 [P1] Retry and failed-save behavior**

Steps: Double-submit intended creation; simulate slow/failing request; refresh/back; retry after failure; inspect row count and dependencies.

Expected: Feedback is visible; entered values recoverable; no unintended duplicate or partial business transaction; retry outcome is clear.

**TC-1300 [P0] Delete confirmation and dependency safety**

Steps: Cancel deletion of disposable fixture; confirm deletion of unused fixture; attempt deleting linked fixture and missing ID; repeat deletion.

Expected: Cancel preserves record; allowed delete removes only selected fixture; in-use dependencies are protected or follow explicitly approved cascade; repeat/missing IDs never cause server error.

**TC-1301 [P1] Utility expense totals**

Steps: Record fractional expense; edit and delete it; compare office report before/after.

Expected: Expense contributes once to correct office and reporting period with consistent rounding.

### gCalendar

**TC-1313 [P0] Guest, limited-role and record ownership access**

Steps: Call module URLs as guest, authorized role and role without permission; replace IDs with another tenant/user records; compare response and database.

Expected: Protected requests require login/API authentication and permission; cross-owner access is rejected; no data mutation or disclosure. Public routes expose only their approved public scope.

**TC-1314 [P1] Presentation and response contract**

Steps: Open available screens at 1440px and 390px; use keyboard Tab/Enter/Escape; inspect labels, errors, console and network. For API-only module inspect JSON headers/schema instead.

Expected: Readable layout and reachable controls; no clipped actions or blocking JavaScript errors; APIs have documented schema/content type and no stack traces.

**TC-1315 [P1] Calendar OAuth and sync**

Steps: Use sandbox Google account; authorize, deny, expire token and reconnect; list calendars.

Expected: State/token validation applies; chosen calendar is correct; denial/expiry show recoverable errors; tokens stay private.

## Cross-module scenarios

**TC-1319 [P0] Google Places configuration and fallback**

Steps: Test no key, valid restricted sandbox key, rejected key and blocked provider; choose city then change country; open repeated dynamic forms.

Expected: Manual entry remains usable when unavailable; valid autocomplete writes correct city/place ID; country changes clear stale data; widgets initialize once.

**TC-1320 [P0] Pusher notification delivery**

Steps: Test no credentials, valid sandbox credentials and disconnect/reconnect; deliver event to A/B sessions on localhost with port and staging domain.

Expected: App remains usable without provider; only intended users receive notification once; configured key/cluster are used; secret remains server-side.

**TC-1321 [P0] Cross-module booking to settlement**

Steps: Create client and supplier; create tour/quote; add services and offer; confirm booking; assign bus/driver; generate invoice; pay; inspect reports and history.

Expected: All relationships and statuses remain consistent; availability holds; invoice/payment/report totals reconcile; no duplicate financial transaction.

**TC-1322 [P0] Accessibility and locale**

Steps: Keyboard-test menu, modal, form errors and tables; check zoom 200%, Unicode, long names and date/currency formatting across modules.

Expected: Visible focus and meaningful labels; modal focus can exit; layout remains operable; no raw translation keys; locale formatting is consistent.

**TC-1323 [P0] Session expiry and request protection**

Steps: Expire session during form edit; submit missing/invalid CSRF token; revisit saved link after logout; try external redirect parameter.

Expected: Expired session recovers clearly; cookie-authenticated mutations reject invalid CSRF; protected data stays inaccessible; redirects remain trusted.

## Existing automation

BusValidationTest.php and BusSafetyTest.php contain focused bus regressions; tests/Browser/bus-integrations.cjs checks local fallback and simulated Google/Pusher behavior. The newly generated manual cases are not marked passed by these tests. Other existing tests must be reviewed for scope and database safety before execution.

## Regenerating

From the repository root:

```powershell
php artisan route:list --json > storage/app/test-case-routes.json
python tests/qa/generate_test_cases.py
```

Regeneration overwrites the generated documents and resets result columns. Save completed execution sheets separately first. Route-derived IDs can shift when routes change; link results to the exact build and exported sheet.
