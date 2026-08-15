# CanSat 2026–2027 recruitment site

Minimal French-language recruitment website for the CEPES de Jodoigne CanSat team.

## Stack

- Plain PHP
- MySQL/MariaDB through PDO
- DDEV for local development

## Main files

- `index.php` — project information and recruitment landing page
- `form.php` — application form, frontend behavior, backend validation, and persistence
- `success.php` — successful-submission confirmation
- `rules.txt` — human-readable validation specification
- `includes/form_rules.php` — executable shared limits
- `sql/schema.sql` — clean database schema
- `tests/form_validation_test.php` — validation and tampering tests
- `tests/database_schema_test.php` — live database contract test

## Local verification

```sh
ddev exec php tests/form_validation_test.php
ddev exec php tests/database_schema_test.php
```

## Conventions

- Internal field names, IDs, and code remain in English.
- User-facing copy is French and addresses applicants with “vous”.
- Keep the site functional, polished, and minimal.
- Keep `rules.txt`, `includes/form_rules.php`, `form.php`, tests, and the SQL schema synchronized whenever validation changes.
