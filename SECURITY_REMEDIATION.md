# GitGuardian remediation for PR #1

The flagged reports and compiled view were already deleted at the branch tip.
The exposed database credential was not found in current tracked files. Deleting
files in a later commit does not remove their contents from earlier commits.

## Credentials and deployment

- Rotate the database password disclosed in the two reports using the database
  administrator account. Update the application's private environment and any
  other consumers, then verify database connectivity. Rotation must happen even
  after history cleanup; copies of the old commits may still exist.
- Reset any existing administrator account created with the former fixed seeder
  password. Changing the seeder does not change existing database accounts.
- Set ADMIN_SEED_EMAIL and ADMIN_SEED_PASSWORD in the private environment before
  running AdminUserSeeder. Use a unique password of at least 16 characters. Clear
  or rebuild Laravel's configuration cache after changing the environment.
- Set SNAPPYMAIL_ADMIN_PASSWORD explicitly if needed and change the actual
  SnappyMail administrator password through its admin interface. Configuration
  changes alone do not rotate the password in SnappyMail.
- The compiled view contained a CSRF token. Invalidate the affected session if it
  still exists. Do not rotate APP_KEY solely for this token; doing so can break
  decryption of application data. Run `php artisan view:clear` on deployment.

## Publishing cleaned history

History cleanup changes commit IDs. Coordinate with other contributors before
publishing. No remote push or credential rotation is performed by this change.
After reviewing the rewritten local branch, record the current remote dev SHA
and publish with an explicit lease:

```powershell
$remoteDev = ((git ls-remote origin refs/heads/dev) -split '\s+')[0]
git push "--force-with-lease=refs/heads/dev:$remoteDev" origin dev:dev
```

Rerun GitGuardian for PR #1 and resolve the incidents after verifying rotation.
Other branches, tags, PR refs, forks, and local clones can retain old commits;
coordinate their cleanup separately. Store any pre-cleanup recovery bundle
privately because it contains the exposed credentials; never upload it.

## Future commits

Generated views, local environment files, the flagged reports, and the diagnostic
endpoint are ignored. Ignore rules do not block `git add -f` or sanitize history.
Install ggshield in the development environment and configure its pre-commit
secret scan, and retain GitGuardian's pull request checks.
