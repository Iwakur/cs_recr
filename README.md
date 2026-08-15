# CanSat 2026–2027 recruitment site

Minimal French-language recruitment website for the CEPES de Jodoigne CanSat team.

## Stack

- Plain PHP
- MySQL/MariaDB through PDO
- DDEV for local development

The application database is `cansat`; recruitment submissions are stored in the `form` table.
Session cookies default to HTTPS-only. Set `SESSION_COOKIE_SECURE=true` explicitly in production.

Malformed-request limits are defined in `.user.ini` for standard PHP deployments and mirrored in
`.ddev/php/cansat-hardening.ini` for DDEV. A custom production PHP image must allow `.user.ini` files
or copy equivalent values into its PHP configuration.

## Main files

- `index.php` — project information and recruitment landing page
- `form.php` — application form, frontend behavior, backend validation, and persistence
- `result.php` — one-time success, persistence-error, or neutral submission status
- `rules.txt` — human-readable validation specification
- `includes/form_rules.php` — executable shared limits
- `sql/schema.sql` — clean database schema
- `tests/form_validation_test.php` — validation and tampering tests
- `tests/database_schema_test.php` — live database contract test
- `EXPORT_GUIDE.md` — DDEV and Docker Compose database/Excel export procedure

## Local verification

```sh
ddev exec php tests/form_validation_test.php
ddev exec php tests/result_page_test.php
ddev exec php tests/database_schema_test.php
```

## Production deployment

The production setup uses a runtime-only FrankenPHP application image and a MariaDB container.
Initialization, private exports, external Caddy integration, password matching, and the CI/CD
strategy are documented in [DEPLOYMENT.md](DEPLOYMENT.md).

## Conventions

- Internal field names, IDs, and code remain in English.
- User-facing copy is French and addresses applicants with “vous”.
- Keep the site functional, polished, and minimal.
- Keep `rules.txt`, `includes/form_rules.php`, `form.php`, tests, and the SQL schema synchronized whenever validation changes.
