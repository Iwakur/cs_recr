# CanSat recruitment site readiness report

**Report date:** 2026-08-15
**Scope:** PHP syntax, frontend/backend validation, database schema and persistence, local HTTP pages, internal assets, and project documentation.

## Result

The project is internally consistent and the recruitment submission flow works end to end in DDEV.

## Verified

- Every PHP file passes `php -l` under the project runtime.
- The automated form validation suite passes.
- Scalar/array tampering, unknown options, duplicate checkbox values, invalid contact choices, invalid ages, excessive lengths, missing required values, conditional requirements, duplicate roles, availability, and consent are checked server-side.
- HTML limits and required markers agree with the PHP rules documented in `rules.txt`.
- Telephone is not offered as a recruitment contact method.
- The selected contact method is required and only its visible input is enabled and required.
- Contact data is stored in one `contact` column with a method prefix, for example `discord: test.integration`.
- The clean database schema contains exactly the columns expected by the application.
- A complete HTTP form submission returned `302 Location: success.php` and stored the expected row.
- The end-to-end test row was removed; the applications table is empty.
- `index.php`, `form.php`, and `success.php` return HTTP 200.
- All repository-referenced local images, stylesheets, and media files exist.
- SQL uses a prepared statement with named parameters.
- User-controlled values are escaped before HTML output.
- Database configuration is environment-driven and matches DDEV defaults.

## Current source of truth

- Human-readable requirements: `rules.txt`
- Executable limits: `includes/form_rules.php`
- Frontend and backend form behavior: `form.php`
- Database contract: `sql/schema.sql`
- Validation tests: `tests/form_validation_test.php`
- Live schema test: `tests/database_schema_test.php`

When a rule changes, update and test every applicable layer listed above.

## Deployment considerations

The application is functionally ready in its current DDEV environment. Before accepting public submissions, deployment-specific operational controls should still be decided outside this code audit: backups, retention/deletion policy, logging, rate limiting or spam protection, HTTPS, and security headers at the production web server or reverse proxy.
