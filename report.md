# CanSat recruitment site deployment-readiness report

**Audit date:** 2026-08-15

## Executive result

The PHP application is stable and internally consistent in DDEV. Validation, persistence, the clean `cansat.form` schema, result-page sessions, malformed-request handling, and CSV/SQL export tooling work as intended.

The application code is ready for a controlled staging deployment. Public-production readiness is conditional because the repository does not contain the production `compose.yaml` or image definition, so container security, persistent storage, environment variables, HTTPS integration, restart behavior, and production PHP settings cannot yet be verified.

The main remaining application-level launch issue is that 15 September is displayed as the deadline but is not enforced: the form continues accepting submissions afterward.

## Result-page upgrade

`success.php` was replaced by `result.php`.

- A successfully stored application creates a one-time session status and redirects to `result.php`.
- A database/configuration failure redirects to the same page with a safe retry message.
- Validation errors remain on the form beside the submitted values.
- Direct access, unknown status values, and refresh after viewing a result show “Aucune candidature récente.”
- Only a small status code is stored in the session; no applicant data or technical database error is exposed.
- Session cookies use `HttpOnly` and `SameSite=Lax`. `Secure` is enabled when PHP sees an HTTPS request.

## Verified evidence

| Area | Result |
|---|---|
| PHP syntax for every PHP file | Pass |
| Form validation/tampering suite | Pass |
| Result-page state suite | Pass |
| Exact live database schema test | Pass |
| Complete POST, database insert and redirect | Pass |
| Redirect target | `302 Location: result.php` |
| First result view | “Candidature envoyée” |
| Result refresh/direct access | “Aucune candidature récente” |
| Malformed scalar-as-array request | Controlled HTTP 200 validation response |
| Deeply nested request | Controlled HTTP 200 response; no leaked warning |
| Oversized request | Controlled validation response |
| Runtime request settings | 256 KiB POST, 200 variables, four nesting levels |
| Database | `cansat.form`, exact expected columns |
| Database after tests | Empty; all temporary rows removed |
| Public pages | `index.php`, `form.php`, `result.php`: HTTP 200 |
| Removed page | `success.php`: HTTP 404 |
| CLI CSV exporter over HTTP | HTTP 404 |
| Representative CSS/images/media | HTTP 200 |
| Public information links | Wikipedia, ESA, Wallonia and Telegram: HTTP 200 |
| Git whitespace check | Pass |

## Security and stability already present

- Prepared PDO statement for database insertion.
- Strict server allowlists for selects, radios and checkboxes.
- Unicode-aware per-field length validation.
- Rejection of scalar/array type confusion, nested arrays, excessive checkbox items and duplicates.
- Application and PHP request-size/input-complexity limits.
- PHP errors hidden from HTTP responses and retained for server logging.
- Escaped redisplay of submitted values and result messages.
- Database credentials supplied through environment variables.
- CLI-only Excel-compatible exporter with UTF-8 BOM and correct CSV quoting.
- SQL and CSV export procedure documented in `EXPORT_GUIDE.md`.
- Local phpMyAdmin remains a DDEV development add-on rather than an application page.

## Required before public deployment

### 1. Audit the real Docker Compose deployment

No production `compose.yaml`, Dockerfile, or equivalent image definition is present in this repository. Before launch, verify the actual VPS stack provides:

- `DB_HOST=db`, `DB_NAME=cansat`, and matching private database credentials;
- `MARIADB_DATABASE=cansat` or the MySQL equivalent;
- a persistent database volume;
- restart policies and health checks;
- equivalent PHP limits to `.user.ini`;
- a writable, private PHP session directory;
- no publicly exposed database or phpMyAdmin port;
- HTTPS and trusted reverse-proxy configuration.

### 2. Enforce the application deadline

The homepage clamps the countdown at zero, but `form.php` does not check the date. After 15 September 2026 it will display “0 jours” and still accept applications. Decide whether to close at the start or end of that date and enforce the same timezone-aware rule in the homepage, form display, and POST handler.

### 3. Add production abuse controls

Malformed payloads are safely rejected, but an automated client can still send many structurally valid applications. Add rate limiting at the reverse proxy or hosting layer. A lightweight honeypot or challenge can be added if spam appears. CSRF protection is also advisable, although it is not a substitute for rate limiting.

### 4. Configure operational logging

User-facing database errors are appropriately generic, but caught PDO failures are not currently written with useful server context. Configure container log collection and log persistence failures privately without recording complete application contents or credentials.

### 5. Add production security headers

The tested DDEV response does not include CSP/frame protection, `Referrer-Policy`, or `Permissions-Policy`. Configure these at the production reverse proxy after confirming all required assets and external links.

### 6. Protect secrets and exports

The repository currently has no `.gitignore`. Add one before introducing a VPS `.env`, database dumps, CSV exports, IDE files, or other secrets. Continue storing exports outside the public project directory with restrictive permissions as described in `EXPORT_GUIDE.md`.

## Performance concern

Static assets total approximately 39 MB. The largest files are:

- `preparation_military_base.jpg`: approximately 13.4 MB;
- `launch.gif`: approximately 12.9 MB;
- `start_photo.jpg`: approximately 7.1 MB;
- `starter_kit.jpg`: approximately 5.7 MB.

The page does not load the GIF until interaction, which helps, but the gallery photographs are still unusually large for the web. Resize and compress them, preferably generating WebP/AVIF variants, before expecting good mobile performance.

## Privacy and operations

Before collecting real student information, decide and communicate:

- who can access exports and phpMyAdmin;
- how long unsuccessful and successful applications are retained;
- how applicants can request correction or deletion;
- where backups are stored and encrypted;
- when VPS export copies are deleted.

## Release gate

Before opening the public form:

1. Place the actual Compose/image configuration under review and run the full test suite inside it.
2. Confirm the database volume survives container recreation.
3. Submit, export and delete a test application on the VPS.
4. Enforce or explicitly waive automatic deadline closure.
5. Enable rate limiting, HTTPS, private logs and security headers.
6. Add `.gitignore` before creating a production `.env` or exports.
7. Compress the largest gallery media.
8. Confirm privacy, retention and backup responsibilities.

Once those items are complete, repeat the HTTP, submission, result, database and export checks documented in this report.
