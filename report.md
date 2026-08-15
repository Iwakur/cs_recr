# CanSat recruitment form readiness report

**Report time:** 2026-08-15 10:37:27 CEST  
**Scope:** `form.php` as the source of truth, server-side validation, SQL persistence contract, active DDEV runtime, adversarial submissions, and basic HTTP deployment posture.  
**Change policy:** No application, test, configuration, or schema files were changed during this audit. This report is the only file created.

## Executive summary

**Overall readiness: 4/10 — not ready for production submissions yet.**

The form, PHP state, prepared SQL insert, and repository schema are structurally well connected. The newly added English and communication fields flow through all intended layers. Existing syntax and validation tests pass, allow-listed choices reject tampering, output escaping protects normal error redisplay from stored/reflected HTML injection, and SQL parameters are bound safely.

However, the currently running product cannot save even a normal valid application. Its database configuration targets `localhost`, database `cansat`, user `root`, and an empty password, while DDEV exposes MariaDB through service `db` with database/user/password `db`. The active DDEV database also has no `applications` table. A valid submission consequently returns “La base de données n'est pas encore configurée.”

There is also an externally triggerable PHP fatal error: sending an array for any field expected to be scalar reaches `trim(array)` and terminates the request. Before launch, the database path and input-shape handling are release blockers.

## Source-of-truth consistency

### Correctly connected

All visible form answers have matching server-side state and an intended persistence path:

- Identity: `first_name`, `last_name`, `class`/`other_class`, `age`, and `gender`.
- Contact: `preferred_contact`, `email`, `phone`, `telegram`, `discord`, and `instagram`.
- Interests: `preferred_role`, `second_choice`, and `motivation`.
- Levels: programming, electronics, CAD/3D, sciences, English oral comprehension, and English oral speaking.
- Skills and reasoning: `known_skills`, `problem_solving`, and `role_flexibility`.
- Detailed experience: programming, electronics, CAD/3D, sciences, communication, and other projects.
- Availability, weekly time commitment, and consent.

`other_class` is intentionally not a separate database column: when “Autre” is selected, its value is stored in the `class` column. Consent is checked from the request and stored as integer `1`. Checkbox arrays are serialized as comma-separated text.

The repository schema includes both recent additions:

- `english_listening_level` and `english_speaking_level`
- `communication_experience`

The INSERT column order, named placeholders, and execute bindings agree with one another and with `sql/schema.sql`.

### Consistency gaps

1. **Runtime configuration does not match the active environment (critical).** `includes/config.php` is hard-coded and does not match DDEV. The active database contains no application table.
2. **Browser length rules are not consistently enforced by PHP (high).** `other_class`, email, Telegram, Discord, and Instagram have HTML `maxlength` values but equivalent server checks are absent. A crafted request bypasses HTML limits.
3. **Database limits are not represented in either UI or PHP (high).** First and last names have no maximum despite `VARCHAR(100)`. The six detailed experience fields and `other_projects` have no maximum despite MySQL `TEXT` limits. Oversized input will reach persistence and degrade into the generic save error.
4. **Some invalid level errors are ambiguous (low).** Multiple invalid level fields produce repeated identical messages without identifying which answer is wrong.
5. **Array serialization is lossy (medium).** Skills and availability are stored as comma-separated strings, which is difficult to query reliably and assumes option labels never contain commas.

## Tests executed

| Test | Result | Observation |
|---|---:|---|
| PHP syntax check on `form.php` | Pass | PHP 8.4 reports no syntax errors. |
| Repository validation suite | Pass | “All form validation tests passed.” |
| Normal valid application | **Fail** | Rejected because the database is not configured. |
| Current database schema inspection | **Fail** | Active DDEV database has no `applications` table. |
| Enum tampering across contact, class, gender, roles, levels, skills, flexibility, and availability | Pass | All manipulated selections were rejected server-side. |
| Duplicate preferred/secondary role | Pass | Covered by browser and server validation. |
| Missing consent | Pass | Rejected server-side. |
| Missing availability | Pass | Rejected server-side. |
| Scalar field submitted as an array | **Fail** | Uncaught `TypeError` at `form.php:110`; process exits 255. |
| HTML/script strings in name and motivation with a separate validation failure | Pass by inspection/runtime behavior | Redisplayed values go through `htmlspecialchars(..., ENT_QUOTES, 'UTF-8')`; no raw execution path was found. |
| SQL-injection-shaped values | Pass by design | Fixed choices are allow-listed and free text uses prepared named parameters. |
| Boundary values: motivation 800 and time commitment 600 characters | Pass | Accepted at documented maximums. |
| Overlong names/contact/details sent outside the browser | **Fail policy check** | No PHP length errors are produced; values proceed toward the unavailable database. |
| HTTP response header review | Needs work | Response is 200 and UTF-8, but no application-visible CSP, frame protection, referrer policy, or similar hardening headers are present. |

The stress runner could not prove a successful insert, redirect, or stored row because the live database connection is nonfunctional. This is an important test limitation, not a passing result.

## Detailed findings

### Critical: valid applications cannot be stored

`db()` catches the connection exception and returns `null`, after which the form shows a configuration error. In the current DDEV runtime:

- PHP application target: host `localhost`, database `cansat`, user `root`, blank password.
- Actual DDEV database service: host `db`, database `db`, user `db`, password `db`.
- Actual table state: `applications` does not exist.

The happy path, success redirect, and database row contents are therefore unverified and currently unusable.

### High: malformed request shapes cause a server crash

The state-loading loop assumes every nominally scalar `$_POST` value is a string and calls `trim()` directly. An attacker can submit `first_name[]=Alice` (or use another scalar field) and trigger an uncaught PHP 8.4 `TypeError`. Nested arrays can similarly attack array-valued fields through `array_map('trim', ...)`.

Impact: easy denial-of-service noise, HTTP 500 responses, stack traces when error display is enabled, and bypass of the intended friendly validation experience.

### High: length validation is incomplete

Client-side `maxlength` is useful for normal users but is not a security boundary. Crafted POST requests can exceed it. The backend currently checks only:

- Motivation: 800 characters.
- Problem solving and time commitment: 600 characters.
- Phone: 6–25 allowed characters through its format regex.

It does not enforce the remaining UI or database limits. This creates inconsistent behavior and makes database exceptions the accidental validator. The public endpoint also lacks an explicit request-size strategy at application level.

### High: no anti-automation protection

No CSRF token, rate limiting, honeypot, CAPTCHA/challenge, duplicate-submission control, or submission cooldown is present in the application. For a public recruitment endpoint, automated spam can fill the database once connectivity is fixed. CSRF alone is not a complete spam control; rate limiting or another server-side anti-automation measure is needed.

### Medium: generic persistence errors hide operational causes

All `PDOException`s become “La candidature n'a pas pu être enregistrée.” This is acceptable for users, but there is no visible server-side logging or request identifier. Schema drift, oversized fields, connection failures, and SQL mistakes will be difficult to diagnose in production.

### Medium: database configuration and secrets are deployment-fragile

Credentials are committed as constants and the application expects a privileged `root` user with a blank password. Production should load environment-specific values and use a least-privilege database account. Connection configuration should be validated during deployment, not discovered by applicants.

### Medium: no explicit submission abuse or duplicate strategy

The same applicant can submit repeatedly. Duplicate skills and availability values sent in crafted arrays are also retained rather than normalized. This is not data corruption in the SQL sense, but it lowers recruitment-data quality.

### Medium: privacy operations are underspecified

The form collects identifying and contact information and presents a consent checkbox, but no linked privacy notice, retention period, responsible contact, deletion process, or purpose details were found on the form. Legal requirements depend on deployment context, but these operational details should be decided before collecting real applications.

### Low: response hardening is absent

The observed form response included only basic server/content headers. No Content Security Policy, clickjacking protection (`frame-ancestors` or `X-Frame-Options`), `Referrer-Policy`, or permissions policy was visible. Some of these may be supplied later by a production reverse proxy, but they are not present in the tested product.

### Low: accessibility and UX follow-up

- Server errors appear in an `aria-live` region, which is positive.
- Labels wrap their controls, so associations are generally sound.
- After a server rejection, focus is not explicitly moved to the error summary and fields do not receive per-field error associations.
- The consent checkbox is not repopulated after an unrelated validation error, forcing the applicant to check it again. This may be intentional, but it should be a conscious UX choice.
- Backend messages use informal French (“tu”), consistent with the current form, while the README’s pre-deployment note asks for “Vous”; the product voice decision is unresolved.

## Release recommendation

Do not open the form for real applications yet. A reasonable release gate is:

1. Configure database credentials per environment and initialize/migrate the schema.
2. Prove one complete submission: POST, insert, redirect, and exact stored values for every form field.
3. Reject incorrect input shapes without fatal errors.
4. Mirror all relevant HTML/database length limits in server validation.
5. Add logging for persistence failures without exposing internals to applicants.
6. Add practical anti-spam/rate-limiting protection and decide the duplicate-submission policy.
7. Confirm privacy notice, retention, and contact process.
8. Add an integration test using a disposable database and include the new English/communication fields.
9. Confirm security headers at the actual production edge.

After items 1–4, the form should be functionally testable. After the full release gate, it would be reasonable to reassess it in the **8/10 launch-ready** range, subject to a successful browser/device pass and production-like end-to-end test.

## Positive observations

- Prepared statements are used correctly for the intended insert.
- Select, radio, and checkbox choices are checked against server-side allow-lists.
- Required-field, age, contact dependency, role duplication, consent, and availability rules are enforced server-side.
- Dynamic “other class” and preferred-contact fields correctly toggle `required` and `disabled` in the browser.
- User-controlled values and error strings are escaped before HTML output.
- UTF-8 is used in the page and intended database connection.
- Recent frontend additions are represented consistently in state, INSERT bindings, schema, and regression assertions.
- The current test suite is small but fast and passed under the project’s PHP 8.4 runtime.
