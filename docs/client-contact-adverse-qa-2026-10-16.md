# Client/contact adverse-input QA — October 16, 2026

Scope: client edit and contact saving, including `/clients/9/edit`. No test submission was sent to the application's existing clients. Save and rollback tests used in-memory SQLite; browser writes were blocked or answered with simulated validation responses.

## Results

- 74 client/contact tests passed, including 30 invalid input cases checked both through FormRequest validation and actual Laravel HTTP requests.
- The combined client, accounting, bus and database-error suite passed: 109 tests, 311 assertions.
- Browser checks passed without JavaScript errors: rapid additions, unique indices, failed list loading, retry, failed row loading, text containing HTML, removal of all rows, and a simulated 422 response.
- The actual client 9 form with two unsaved test contacts passed read-only server validation.
- Client 9 and its saved contacts still match the pre-test database baseline. The temporary browser session was revoked.

## Defects fixed

| Scenario | Previous behavior | Result |
| --- | --- | --- |
| Arrays in ordinary text fields | PHP TypeError during trimming | HTTP 422 validation error |
| Invalid contact structure, blank names, invalid email/phone, oversized values | Missing validation; bad data could reach persistence | Field-specific validation; existing records retained |
| Oversized address or account number | Could exceed database column limits | Rejected before saving |
| Contact list absent after a failed load | Existing contacts deleted | Existing contacts preserved |
| User deliberately removes all loaded contacts | Indistinguishable from failed load | Explicit loaded marker supports intentional removal |
| Failure partway through saving contacts | Client changes and deleted contacts could remain | Database transaction rolls back client, password and contact changes |
| Internal attributes included in payload | Could be passed to the model update | Only supported client form fields are written |
| Failed contact-list or row request | No useful recovery feedback | Visible error and retry behavior |
| Nested validation errors | Contact errors were not matched to bracketed input names | Exact contact fields highlighted; typed values remain available |
| Upload using the actual `attach[]` field | Only the separate `files` field was validated | Executables and oversized uploads rejected; small PDF accepted |
| Long or multibyte portal password | Encrypted value could exceed VARCHAR(250) and be truncated | Oversized ciphertext rejected; boundary-length password saves and decrypts correctly |

Other checks covered script/HTML names, punctuation-only names and phones, Unicode names, sparse contact indices, duplicate emails, unchanged legacy duplicate emails, unknown cities, malformed load markers, excessive contact rows and missing clients.

Valid saves return HTTP 200, invalid input returns HTTP 422 and missing clients return HTTP 404. Existing passwords remain unchanged when omitted. Invalid fields are not silently corrected into valid-looking data.

## Repeat isolated tests

```powershell
php vendor/phpunit/phpunit/phpunit --testsuite Unit --filter 'ClientContactUpdateTest|AccountingSafetyTest|AccountingPermissionsTest|BusSafetyTest|BusValidationTest|DatabaseErrorResponseTest'
```

The browser test is `tests/Browser/client-contacts.cjs`; it requires an authenticated local Playwright state file via `QA_STORAGE_STATE`. Optional module and browser executable overrides are `PLAYWRIGHT_MODULE` and `PLAYWRIGHT_EXECUTABLE_PATH`.
