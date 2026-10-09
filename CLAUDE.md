# CLAUDE.md

## Project overview

This is a Laravel 8 travel-management application. The backend uses PHP 8, Blade views, Laravel Collective forms, Spatie permissions/activity log, PhpWord, DOMPDF, Excel, Intervention Image, and custom scaffold-interface layouts. Frontend assets are managed with Laravel Mix, Bootstrap 5, Tabler-style UI, jQuery, and DataTables.

## Important paths

- Routes: `routes/web.php`
- Controllers: `app/Http/Controllers/`
- Form requests: `app/Http/Requests/`
- Models: `app/`
- Blade views: `resources/views/`
- Shared layouts: `resources/views/scaffold-interface/layouts/`
- Shared components: `resources/views/component/`
- Public JavaScript: `public/js/`
- Public CSS/assets: `public/css/`, `public/assets/`, `resources/assets/`
- Uploads/storage: `storage/`, `public/storage/`

## Local commands

Use these commands when checking changes:

```bash
php -l path/to/file.php
php artisan view:cache
php artisan route:list
php artisan config:clear
php artisan cache:clear
npm run dev
```

For Blade-only or controller-only edits, run `php artisan view:cache` and `php -l` on touched PHP files. Run frontend builds only when changing compiled assets under `resources/assets` or Mix-managed files.

## Coding conventions

- Prefer small, targeted fixes over rewrites.
- Keep existing routes, permissions, translations, and helper usage intact unless the task requires changing them.
- Use Laravel route helpers instead of hard-coded local URLs where possible.
- Use existing helpers such as `LaravelFlashSessionHelper`, `PermissionHelper`, and shared components before adding new patterns.
- Do not remove permission checks or middleware.
- Do not introduce new packages unless explicitly requested.
- Avoid editing `vendor/`.

## UI conventions

- Use toast notifications instead of browser `alert()` for user-facing feedback.
- Keep action icons visually consistent across tables:
  - Preview/view: orange filled button
  - Edit: blue filled button
  - Delete/remove: red filled button
- Icons should remain visible on hover and focus. Avoid hover states that turn icons white on white or add unwanted borders/boxes.
- Form inputs should use white backgrounds and readable dark text unless a specific design needs otherwise.
- Top duplicate Save buttons should be removed from create/edit forms; keep Back on the left and Save at the form end when applicable.
- Dropdowns should open smoothly, stay compact, and should not close when removing an item unless the user explicitly navigates away.

## Laravel/Blade notes

- When using `Str` in Blade, prefer `\Illuminate\Support\Str::limit(...)` or import/prepare values in the controller to avoid `Class "Str" not found` errors.
- Keep Blade fallback expressions as ASCII-safe PHP, for example `{{ $value ?? '-' }}`.
- After changing views, run `php artisan view:cache` to catch Blade syntax issues.

## File upload notes

- Match frontend field names with controller validation. If a controller validates `files`, JavaScript should append `files[]`.
- For AJAX uploads, handle JSON responses and update the visible preview/file name immediately after success.
- Show toast feedback for upload success/failure when the global toast helper is available.

## PhpWord/doc export notes

- PhpWord cannot add nested `TextRun` elements. Sanitize HTML before passing it to `PhpOffice\PhpWord\Shared\Html::addHtml`.
- Convert malformed tags such as bare `<br>` to valid XHTML-style `<br />` before `DOMDocument::loadXML()`.
- Avoid passing deeply nested or invalid editor HTML directly into a TextRun.

## Safety notes

- Do not delete user data or database records while cleaning unused code unless the user explicitly asks for that deletion.
- For “sample data” requests, prefer non-persistent sample/empty-state UI rows unless the user asks for database seeders or records.
- Before broad cleanup, inspect references with `rg` so routes, views, and JavaScript callbacks are not removed while still in use.
