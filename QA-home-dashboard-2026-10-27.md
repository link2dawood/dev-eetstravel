# QA Report: `/home` Dashboard, `/users` and `/roles`

- **Date:** 2026-10-27
- **Environment:** local, DB `eets-travel`
  - `/home` was tested on `http://localhost:8000`.
  - `/users` and `/roles` were tested on `http://127.0.0.1:8010`.
- **Method:** logged in over HTTP as a seeded admin, a seeded regular user (role `user`) and a guest. Submitted the real forms and endpoints, then checked responses, rendered HTML, database state and the Laravel logs. Not clicked through in a real browser, so the JS console was not checked.

## Summary

| ID | Severity | Issue | Where it shows up | Request that failed |
|----|----------|-------|-------------------|---------------------|
| H1 | Critical | Opening `/home` logs the user out | `/home` | `GET /api/v1/dashboard/tours` (and every `/api/v1/*`) |
| H2 | Critical | Calendar, Latest Tours and Activities are always empty (403) | `/home` | `GET /home/getToursTasksForCalendar`, `GET /api/v1/dashboard/tours`, `GET /api/v1/dashboard/activities` |
| H3 | Critical | Dashboard chat is completely blocked (403) | `/home` | `GET /chat/{id}/renderChat`, `POST /chat/message`, `GET /chat/getChatNotifications` |
| U1 | Critical | Any logged-in user can create, edit and delete any user | `/users` | `GET /users`, `POST /users`, `POST /users/{id}`, `DELETE /users/{id}` |
| U2 | Critical | Editing user A can change user B (`user_id` tampering) | `/users/{id}/edit` | `POST /users/{id}` |
| U3 | Critical | SVG avatar upload allows stored XSS | `/users/create`, `/users/{id}/edit` | `POST /users`, `GET /uploads/avatars/*.svg` |
| U4 | Critical | Role and permission management is broken for everyone (403) | `/users/{id}/edit` | `POST /users/addRole`, `POST /users/removeRole`, `POST /users/addPermission`, `GET /users/removePermission/{id}/{key}`, `GET /users/{id}/deleteMsg` |
| H4 | High | Stored XSS in the header notifications on every page | `/home` (and every page) | page render |
| H5 | High | The task Delete button on the dashboard doesn't work (405) | `/home` | `DELETE /task/{id}` |
| U5 | High | Saving the user edit form wipes location, education and note | `/users/{id}/edit` | `POST /users/{id}` |
| U6 | High | Saving a user always turns `email_server` on | `/users/{id}/edit` | `POST /users/{id}` |
| U7 | High | Deleting a user shows an error toast even though it worked | `/users` | `DELETE /users/{id}` → redirect → `DELETE /users` (405) |
| H6 | Medium | Tasks without a deadline show as "due now" and overdue | `/home` | page render |
| U8 | Medium | Any password is accepted (e.g. `1`) | `/users/create`, `/users/{id}/edit` | `POST /users` |
| U9 | Medium | Names longer than 191 characters are silently cut | `/users/create` | `POST /users` |
| U10 | Medium | A duplicate email on edit gets a vague database error | `/users/{id}/edit` | `POST /users/{id}` |
| H7 | Low | Announcement order, `quickCreateCalendarEvent` bugs, jQuery `.success()` | `/home` | various |
| U11 | Low | The "No roles" / "No permissions" badges never show | `/users` | page render |
| U12 | Low | The users list has no pagination and runs N+1 queries | `/users` | `GET /users` |
| R1 | Critical | Any logged-in user can create, rename and delete roles | `/roles` | `GET /roles`, `POST /roles/store`, `POST /roles/update`, `DELETE /roles/{id}` |
| R2 | Critical | Any user can give a role they hold more permissions (privilege escalation) | `/roles/{id}/edit` | `POST /roles/addPermission`, `GET /roles/removePermission/{perm}/{role}` |
| R3 | High | Removing a permission is a state-changing GET link (CSRF) | `/roles/{id}/edit` | `GET /roles/removePermission/{perm}/{role}` |
| R4 | High | The `admin` role can be renamed or deleted | `/roles`, `/roles/{id}/edit` | `POST /roles/update`, `DELETE /roles/{id}` |
| R5 | High | Adding several permissions is not all-or-nothing | `/roles/{id}/edit` | `POST /roles/addPermission` |
| R6 | Medium | A duplicate role name crashes with a 500 page | `/roles/create` | `POST /roles/store` |
| R7 | Medium | A permission name that doesn't exist causes a 500 | `/roles/{id}/edit` | `POST /roles/addPermission`, `GET /roles/removePermission/{perm}/{role}` |
| R8 | Medium | Deleting a role in use gives no warning; users silently lose it | `/roles` | `DELETE /roles/{id}` |
| R9 | Medium | Role names longer than 191 characters are silently cut | `/roles/create` | `POST /roles/store` |
| R10 | Medium | Renaming to an existing name gets a vague database error | `/roles/{id}/edit` | `POST /roles/update` |
| R11 | Low | Button styles, "No permissions" badge, empty `deleteMsg` page, legacy delete route always 403 | `/roles` | various |

Fixed during this QA session:

| Issue | URL | Status |
|-------|-----|--------|
| Announcement preview crashed (`foreach() argument must be of type array\|object, null given`) | `/announcements/{id}` (e.g. `/announcements/37`) | Fixed in `resources/views/announcements/childs.blade.php` |
| Task list Previous/Next reloaded the whole page | `/home` | Changed to AJAX (`tasks_list.blade.php`, `AppController@dashboard`) |

---

# Part 1: `/home` dashboard

## H1. Critical: opening `/home` logs the user out

- **URL:** `/home`. The Latest Tours widget calls `GET /api/v1/dashboard/tours` when the page loads.
- **What happens:** every `/api/v1/*` request returns 401 and replaces the user's session cookie with an unencrypted one. On the next request that cookie can't be decrypted, so the user is logged out.

**Steps to reproduce:**
1. Log in.
2. `GET /api/v1/dashboard/tours` returns 401 "Authentication required".
3. `GET /home` now redirects (302) to `/login`.

**Cause:** in `routes/api.php:89` the v1 group uses `['web', 'auth', 'api.permissions']` inside the `api` group. Laravel's middleware sorting then runs `StartSession` before `EncryptCookies`:

```
StartSession → ShareErrorsFromSession → Authenticate → ThrottleRequests → SubstituteBindings → EncryptCookies → ...
```

**Affected widgets:** Latest Tours, Inbox emails, Activities, Chat groups, plus every other `/api/v1/*` call.

**Possible fix:** add `EncryptCookies` (and `AddQueuedCookiesToResponse`) at the top of `$middlewarePriority` in `app/Http/Kernel.php`, or serve these routes through the `web` group only.

---

## H2. Critical: Calendar, Latest Tours and Activities are always empty

- **URL:** `/home`, through these endpoints:

| Endpoint | Result |
|----------|--------|
| `GET /home/getToursTasksForCalendar` | 403 (`PermissionsRequiredMiddleware`, route permission `dashboard.index`) |
| `GET /home/getToursForCalendar` | 403 |
| `GET /api/v1/dashboard/tours` | 403 (`ApiPermissions` maps `DashboardController` to `dashboard.index`) |
| `GET /api/v1/dashboard/activities` | 403 |
| `GET /api/v1/dashboard/chat_groups` | 403 |

- **What happens:** the `dashboard.index` permission does not exist in the `permissions` table, so every user is refused, admins included.
- **Evidence:** the existing admin (user 1) has been refused `dashboard.index` 37 times in `storage/logs`.
- **UX impact:** the calendar JS turns a 403 into an empty event list and only logs to the console. Users see an empty calendar with no message.

**Possible fix:** create a `dashboard.index` permission and assign it to the right roles, or point the routes and `ApiPermissions` at existing `dashboard.*` permissions.

---

## H3. Critical: dashboard chat is completely blocked

- **URL:** `/home` (Main chat / Direct chat widgets).
- **What happens:** `ChatController::__construct` applies `permissions.required` to every action, using the route name as the permission. Most of those names don't exist.

| Route | Missing permission |
|-------|-------------------|
| `GET /chat/{id}/renderChat` | `chat.renderChat` |
| `GET /chat/{id}/renderDirectChat` | `chat.renderDirectChat` |
| `POST /chat/message` (send message) | `chat.post` |
| `GET /chat/getChatNotifications` (polled repeatedly) | `chat.getChatNotifications` |
| `GET /chat/{id}/getMessage` | `chat.getMessage` |
| `GET /chat/getOrCreateChat` | `chat.getOrCreateChat` |
| `GET /chat/createCustomChat`, `addUserToCustomChat`, `removeUserFromChat` | same pattern |
| `POST /chat/deleteChat`, `/chat/{id}/destroy_chat`, `DELETE /chat/{chat}` | same pattern |

**Evidence:** `chat.getChatNotifications` was refused 599 times for user 1 in the logs.

**Possible fix:** give these routes an explicit `'permissions' => 'chat.main'` (or another existing `chat.*` permission), or add the missing permissions.

---

## H4. High: stored XSS in the header notification dropdown

- **URL:** `/home`, and every page that uses the Tabler header or the `app` layout.
- **What happens:** task content is printed without escaping:
  - `resources/views/scaffold-interface/layouts/tabler-header.blade.php:117`: `{!! $task->tourNameNotification() !!}`
  - `resources/views/scaffold-interface/layouts/app.blade.php:113`: same

**Steps to reproduce:** create a task titled `<img src=x onerror=alert(1)>` and open `/home`. The rendered HTML contains:

```html
<div class="d-block text-body">Task [QA] <img src=x onerror=alert("xss-task")> task without tour</div>
```

**Possible fix:** use `{{ $task->tourNameNotification() }}`, or escape `content` and the tour name inside `Task::tourNameNotification()`.

---

## H5. High: the task Delete button on the dashboard doesn't work

- **URL:** `/home` (task list widget), which sends `DELETE /task/{id}`.
- **What happens:** `tasks_list.blade.php` sends `DELETE /task/${taskId}`, but the only delete route is `task/{id}/delete` (`routes/web.php:327`). The server answers `405 Method Not Allowed` and the task is not deleted.

**Possible fix:** change the URL to `/task/${taskId}/delete`, or use `route('task.destroy', ...)`.

---

## H6. Medium: tasks without a deadline show as "due now" and overdue

- **URL:** `/home` (task list and calendar).
- **What happens:** `app/Task.php:115`:

```php
public function getDeadLineAttribute($value)
{
    return Carbon::parse($value)->format('Y-m-d H:i');
}
```

`Carbon::parse(null)` returns the current time. A task with `dead_line = NULL` therefore shows today's date and time, gets the `overdue` class, and appears on the calendar at the current time.

**Possible fix:** `return $value ? Carbon::parse($value)->format('Y-m-d H:i') : null;`

---

## H7. Low

- **URL:** `/home`.
- **Announcement order is random when timestamps tie.** `AppController@dashboard` sorts only by `created_at`, so the 5-item widget may drop the newest announcement. Add `->orderBy('announcements.id', 'desc')`.
- **`quickCreateCalendarEvent` (`POST /calendar/quick-create`) is broken, though the UI doesn't use it.** It calls `$task->users()`, which doesn't exist (the relation is `assigned_users()`), and it never sets `assign`.
- **`$.post(...).success()` in `direct_chat.blade.php:215`.** `.success()` was removed in jQuery 3; use `.done()`.

---

# Part 2: `/users`

## U1. Critical: any logged-in user can create, edit and delete any user

- **URLs:** `/users`, `/users/create`, `/users/{id}`, `/users/{id}/edit`, plus `POST /users`, `POST /users/{id}`, `DELETE /users/{id}`.
- **What happens:** the resource routes (`routes/web.php:641-642`) have only the `web` middleware, and `UserController` adds only `auth`. No permission is checked.

**Result as the regular user (`qa.agent@eets.test`, role `user`):**

| Action | Result |
|--------|--------|
| `GET /users`, `/users/create`, `/users/{id}/edit`, `/users/{id}` | 200 |
| `POST /users` | user created |
| `POST /users/179` with a new password | **admin's password changed** |
| `DELETE /users/183` | **user deleted** |

**Possible fix:** add `permissions.required` to the resource routes, mapped to `users.index`, `users.create`, `users.edit`, `users.destroy`, `users.show`. Those permissions exist, but they would also need to be assigned to the admin role (see U4).

---

## U2. Critical: editing user A can change user B

- **URL:** `/users/{id}/edit`, which posts to `POST /users/{id}`.
- **What happens:** `UserController@update` loads the user with `$request->user_id`, a hidden form field, and ignores the `{id}` in the URL (`UserController.php:154`).
- **Steps to reproduce:** `POST /users/181` with `user_id=183`, `name=[QA] Tampered via user_id`. User 183 is renamed.
- **Impact:** combined with U1, any user can rewrite any account's email and password.

**Possible fix:** use `User::findOrFail($id)` and drop the hidden `user_id`.

---

## U3. Critical: SVG avatar upload allows stored XSS

- **URLs:** `/users/create` and `/users/{id}/edit` (avatar field); the file is served at `/uploads/avatars/{time}_{name}.svg`.
- **What happens:** the `image` validation rule accepts SVG. An SVG containing `<script>alert(...)</script>` was saved to `public/uploads/avatars/`. `GET /uploads/avatars/1793119267_evil.svg` returns 200 with `image/svg+xml`, so the script runs on the app's own domain when the file is opened.
- **Also:** the original client filename is kept in the stored name.

**Possible fix:** validate with `mimes:jpg,jpeg,png,gif,webp`, and store with a generated name (`$file->hashName()`).

---

## U4. Critical: role and permission management is broken for everyone

- **URL:** `/users/{id}/edit` (Roles and Permissions sections, and the delete-confirmation popup).

| Request | Result for the admin |
|---------|---------------------|
| `POST /users/addRole` | 403 |
| `POST /users/removeRole` | 403 |
| `POST /users/addPermission` | 403 |
| `GET /users/removePermission/{id}/{key}` | 403 |
| `GET /users/{id}/deleteMsg` | 403 |

**Cause:**
- These routes sit in the `perm` group, which uses the route name as the permission (`users.addRole`, `user.remove_role`, `users.addPermission`, `users.revokePermission`, `users.deleteMsg`). None of those permissions exist.
- The `admin` role also has **none** of the existing `users.*` permissions (`users.index/create/edit/destroy/show`), so the legacy `POST /users/{id}/delete` (`users.destroy`) is refused too.

**Possible fix:** give these routes explicit permissions (e.g. `users.edit`), and assign the `users.*` permissions to the admin role.

---

## U5. High: saving the edit form wipes profile data

- **URL:** `/users/{id}/edit`, which posts to `POST /users/{id}`.
- **What happens:** the form only sends email, name, password and avatar, but `update()` always sets `location`, `education` and `note` from the request.
- **Steps to reproduce:** set location, education and note on a user, then save their edit form without changes. All three become `null`.

**Possible fix:** only update those fields when they are in the request (`$request->has('location')`), or add them to the form.

---

## U6. High: saving a user always turns `email_server` on

- **URL:** `/users/{id}/edit`, which posts to `POST /users/{id}`.
- **What happens:** `update()` sets `$user->email_server = 1` unconditionally (`UserController.php:161`). A user with `email_server = 0` becomes `1` after any save, so `/home` then treats their mailbox as configured.

**Possible fix:** set it from `$request->email_server` only when that field is sent.

---

## U7. High: deleting a user shows an error toast even though it worked

- **URL:** `/users` (red delete button), which sends `DELETE /users/{id}`.
- **What happens:**
  1. The button sends `fetch(DELETE /users/{id})`.
  2. The user is deleted, and the controller returns `redirect('users')`.
  3. `fetch` follows the redirect still using DELETE, so it requests `DELETE /users`, which returns 405.
  4. The UI shows the error "Method not allowed for this endpoint." although the user is gone.
- **Self-delete:** it is correctly blocked, but the message also comes back through the redirect, so the UI result is confusing.

**Possible fix:** for AJAX requests, return `response()->json(['success' => true, 'message' => ...])` from `destroy()` (and `success: false` for self-delete).

---

## U8. Medium: any password is accepted

- **URLs:** `/users/create` (`POST /users`) and `/users/{id}/edit` (`POST /users/{id}`).
- **What happens:** `password=1` was accepted. There is no minimum length and no confirmation field.

**Possible fix:** `'password' => 'required|min:8|confirmed'` on create, and `nullable|min:8|confirmed` on edit.

---

## U9. Medium: long names are silently cut

- **URL:** `/users/create`, which posts to `POST /users`.
- **What happens:** a 300-character name was accepted and stored as 191 characters, with no error shown.

**Possible fix:** add `max:191` to the `name` rule (and to `email`).

---

## U10. Medium: duplicate email on edit gets a vague database error

- **URL:** `/users/{id}/edit`, which posts to `POST /users/{id}`.
- **What happens:** `update()` validates `email` only as `required|email`. Changing it to another user's email hits the database unique index, and the user sees "This record already exists. Please check your input and try again." instead of a field error.

**Possible fix:** `'email' => 'required|email|unique:users,email,' . $id`.

---

## U11. Low: the "No roles" / "No permissions" badges never show

- **URL:** `/users`.
- **What happens:** `index.blade.php` uses `@if(!empty($user->roles))`. A collection is never `empty()`, so users with no roles get an empty cell instead of the badge.

**Possible fix:** `@if($user->roles->isNotEmpty())`, and the same for permissions.

---

## U12. Low: the users list has no pagination and runs N+1 queries

- **URL:** `/users`.
- **What happens:** `User::all()` loads every user. Roles and permissions are then queried separately for each row. With 42 users, the page is about 245 KB and takes about 1.2 s.

**Possible fix:** `User::with(['roles', 'permissions'])->paginate(25)`.

---

# Part 3: `/roles`

## R1. Critical: any logged-in user can create, rename and delete roles

- **URLs:** `/roles`, `/roles/create`, `/roles/{id}`, `/roles/{id}/edit`, plus `POST /roles/store`, `POST /roles`, `POST /roles/update`, `PUT /roles/{id}`, `DELETE /roles/{id}`.
- **What happens:** the resource routes (`routes/web.php:644-646`) have only the `web` middleware, and `RoleController` adds only `auth`. No permission is checked. Also, no `roles.*` permissions exist in the `permissions` table at all.

**Result as the regular user (`qa.agent@eets.test`, role `user`):**

| Action | Result |
|--------|--------|
| `GET /roles`, `/roles/create`, `/roles/13/edit` | 200 |
| `GET /roles/1/edit` (the **admin** role) | 200 |
| `POST /roles/store` | role created |
| `POST /roles/update` (`role_id=13`) | role renamed |
| `DELETE /roles/21` | role deleted |

**Possible fix:** create `roles.index/create/edit/destroy` permissions, assign them to the admin role, and add `permissions.required` to all role routes.

---

## R2. Critical: any user can give a role they hold more permissions

- **URLs:** `/roles/{id}/edit` (Permissions section), which sends `POST /roles/addPermission` and `GET /roles/removePermission/{perm}/{role}`.
- **What happens:** these two routes (`routes/web.php:522-523`) have no route name and no `permissions` key, so `PermissionsRequiredMiddleware` lets every logged-in user through.

**Steps to reproduce:**
1. `qa.agent` holds `user` plus `[QA] Agent Role`.
2. As `qa.agent`: `POST /roles/addPermission` with `role_id=<[QA] Agent Role>` and `permission_name[]=users.index`.
3. `qa.agent->can('users.index')` is now **true**.

**Impact:** any user can grant themselves any permission through a role they hold. Combined with R1 and R4, they can also rename or delete `admin`.

**Possible fix:** give both routes explicit permissions (e.g. `roles.edit`).

---

## R3. High: removing a permission is a state-changing GET link

- **URL:** `/roles/{id}/edit` (trash icon in the Permissions table), which sends `GET /roles/removePermission/{perm}/{role}`.
- **What happens:** a GET request removes permissions, so it has no CSRF protection. Any link or `<img src>` on a page a logged-in user visits can strip permissions from a role. The same pattern exists at `GET /users/removePermission/{user_id}/{key}`.

**Possible fix:** change it to `POST`/`DELETE` with `@csrf`.

---

## R4. High: the `admin` role can be renamed or deleted

- **URLs:** `/roles/1/edit` → `POST /roles/update`; `/roles` → `DELETE /roles/1`.
- **What happens:** there is no protection for system roles, and 15 places in `app/` and `resources/views` check for a role named exactly `admin` (e.g. `hasRole('admin')` in `AppController` and `Api\DashboardController`). Renaming or deleting it would quietly turn off admin behaviour across the app.
- **Verified on QA roles only:** rename and delete both work with no checks. The real `admin` role was not changed.

**Possible fix:** block renaming and deleting `admin` (and `user`) in `RoleController@update`/`destroy`, and hide those actions in the UI.

---

## R5. High: adding several permissions is not all-or-nothing

- **URL:** `/roles/{id}/edit`, which sends `POST /roles/addPermission`.
- **What happens:** `addPermission()` loops over `permission_name[]` and calls `givePermissionTo()` one at a time. If one name doesn't exist, the request fails with a 500, but the permissions before it are already saved.
- **Evidence:** a request with `users.index`, `roles.index` and `settings.index` saved `users.index`, then failed on `roles.index` with "There is no permission named `roles.index`".

**Possible fix:** validate with `'permission_name.*' => 'exists:permissions,name'`, then call `givePermissionTo($request->permission_name)` once inside a transaction.

---

## R6. Medium: a duplicate role name crashes with a 500 page

- **URL:** `/roles/create`, which sends `POST /roles/store`.
- **What happens:** `store()` validates only `name => required`. A duplicate name throws Spatie's `RoleAlreadyExists`, which shows a 500 error page with the raw exception. The check ignores case, so `[qa] role editor` and `ADMIN` also fail this way.

**Possible fix:** `'name' => 'required|max:191|unique:roles,name'`.

---

## R7. Medium: a permission name that doesn't exist causes a 500

- **URL:** `/roles/{id}/edit`, which sends `POST /roles/addPermission` and `GET /roles/removePermission/no.such.permission/{role}`.
- **What happens:** both return 500 "There is no permission named `no.such.permission` for guard `web`" instead of a validation error.

**Possible fix:** validate against `exists:permissions,name` (see R5).

---

## R8. Medium: deleting a role in use gives no warning

- **URL:** `/roles` (red delete button), which sends `DELETE /roles/{id}`.
- **What happens:** `qa.spaces` held only `[QA] Role ToDelete`. After that role was deleted, `qa.spaces` had **no roles** at all. There was no warning about how many users would be affected.
- **Live usage:** `user` has 30 users, `admin` 4, `Super User` 3.

**Possible fix:** show the number of affected users in the confirmation, or refuse to delete a role that still has users.

---

## R9. Medium: long role names are silently cut

- **URL:** `/roles/create`, which sends `POST /roles/store`.
- **What happens:** a 300-character name was accepted ("Role ... created" shows the full name) but stored as 191 characters.

**Possible fix:** add `max:191` to the `name` rule on create and update.

---

## R10. Medium: renaming to an existing name gets a vague database error

- **URL:** `/roles/{id}/edit`, which sends `POST /roles/update`.
- **What happens:** renaming a role to an existing name hits the database unique index. The user sees "This record already exists. Please check your input and try again." instead of a field error.

**Possible fix:** `'name' => 'required|max:191|unique:roles,name,' . $request->role_id`.

---

## R11. Low

- **URL:** `/roles`.
- **Action button styles:** the index uses `btn-ghost-warning` (orange) for Edit and `btn-ghost-danger` for Delete. CLAUDE.md requires Edit to be a blue filled button and Delete a red filled button.
- **"No permissions" badge never shows:** `@if(!empty($role->permissions))` is always true for a collection. Use `->isNotEmpty()`.
- **`GET /roles/{id}/deleteMsg`** returns an empty 200 page unless it is called by AJAX.
- **Legacy `POST /roles/{id}/delete`** (`routes/web.php:838`) requires `roles.destroy`, which doesn't exist, so it returns 403 for everyone, admin included. The UI doesn't use it; remove the route or fix the permission.

---

## What passed

**`/home`**
- To-do pagination works (10 per page); Previous/Next now load by AJAX.
- The Completed and Aborted tabs show the right tasks.
- Users only see their own data: a regular user sees only their own tours and tasks. `ApiPermissions` overrides any `userId` sent in the request.
- Text containing HTML is escaped in the task list, announcements, the tour table and chat messages.
- Announcement replies are hidden, and the 5-item limit is respected.

**`/users`**
- Creating with valid data works, and the PNG avatar is saved.
- Create shows errors for missing name, email or password, an invalid email, and a duplicate email (case-insensitive).
- Non-image uploads are rejected, including a PHP file renamed to `.png`.
- A name containing `<script>` is shown escaped in the list and the flash message.
- Self-delete is blocked; a guest is redirected to `/login`; a missing user ID returns 404.

**`/roles`**
- Creating and renaming roles work, through both `/roles/store` / `/roles/update` and the resource routes (`POST /roles`, `PUT /roles/{id}`).
- Empty and whitespace-only names are rejected.
- Missing role IDs return 404 (`/roles/999999`, `/roles/999999/edit`, `DELETE /roles/999999`).
- A role name containing `<script>` is escaped on `/roles`, `/roles/{id}`, `/roles/{id}/edit` and the role dropdown on `/users/{id}/edit`.
- An assigned permission really grants access: a user with only `tour.index` could open `/tour`, and `/task` stayed 403.
- A guest is redirected to `/login`.

## Test data

All records are tagged `[QA]` or use `qa.*@eets.test` emails. The password for all QA users is `QaTest#2026`.

| Data | Used for | Details |
|------|----------|---------|
| Users | both | `qa.admin` (admin), `qa.agent` (user), `qa.created`, `qa.long`, `qa.spaces`, `qa.svg`, `qa.byagent` |
| Tours | `/home` | 8: past, current, future, quotation, ends before it starts, script in name, long name, agent-only |
| Tasks | `/home` | 22: pending (paginated), overdue, no deadline, HTML in title, custom status, completed, aborted, private agent task |
| Announcements | `/home` | 7: normal, script in title/body, long, reply |
| Chat messages | `/home` | 2: normal, script |
| Holiday | `/home` | 1 calendar holiday |
| Avatar files | `/users` | 2 in `public/uploads/avatars/` (PNG, malicious SVG) |
| Roles | `/roles` | `[QA] Role Editor (renamed by agent)`, `[QA] Role Renamed PUT`, `[QA] Agent Role`, a `<script>` role, a long-name role, `[QA] Role via resource POST` |

To remove all QA data, run the `qa_cleanup.php` script from the QA session. It also deletes the uploaded avatar files and the QA roles. Roles are matched with `BINARY name LIKE '[QA]%'`, so the real `admin` and `user` roles are never touched.
