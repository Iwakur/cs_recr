# CanSat two-container deployment

The production stack contains exactly two long-running containers:

- `web`: the immutable application image, PHP 8.4, and FrankenPHP on port 8080;
- `db`: MariaDB with its data in the `database_data` named volume.

FrankenPHP deliberately serves plain HTTP inside the machine. The machine's main Caddy instance
terminates HTTPS and reverse-proxies to `127.0.0.1:8080`. MariaDB has no published port.

## 1. Prepare the server

Install Docker Engine with the Compose plugin. Clone this repository into a private deployment
directory, then create the production environment:

```sh
cp .env.docker.example .env.docker
chmod 600 .env.docker
```

Generate two independent passwords, for example with `openssl rand -base64 36`, and replace the
example values. Avoid characters added by hand that have special meaning in Compose interpolation,
especially an unescaped `$`.

The database values have the following relationship:

| Setting | Consumer | Must match |
|---|---|---|
| `DB_PASSWORD` | Compose input | Source value for both services |
| `DB_PASS` | PHP application | `MARIADB_PASSWORD` |
| `MARIADB_PASSWORD` | MariaDB application user | `DB_PASS` |
| `DB_ROOT_PASSWORD` | MariaDB administrator | Nothing else; it must be different |

You only write `DB_PASSWORD` once in `.env.docker`; Compose maps it to both `DB_PASS` and
`MARIADB_PASSWORD`. `DB_NAME` and `DB_USER` are likewise shared by both services. During DDEV
development, the existing defaults remain `db` / `db` for user and password. The production Docker
password does not need to equal the DDEV password because they are separate databases.

Validate the resolved configuration without printing it:

```sh
docker compose --env-file .env.docker -f compose.production.yaml config --quiet
```

## 2. Initialize the database once

The initialization command starts MariaDB, waits for it to become ready, imports `sql/schema.sql`,
and confirms that the application user can read the `form` table:

```sh
./deploy/init-db.sh
```

The schema uses `CREATE TABLE IF NOT EXISTS`, so rerunning the command is non-destructive. It is not
a migration system and will not alter an older table to match future schema changes.

Start or update both services:

```sh
docker compose --env-file .env.docker -f compose.production.yaml up -d --build
docker compose --env-file .env.docker -f compose.production.yaml ps
```

For a registry-built release, set `APP_IMAGE` to the immutable tag published by GitHub Actions and
omit `--build`, for example:

```dotenv
APP_IMAGE=ghcr.io/your-github-owner/your-repository:sha-full-commit-hash
```

## 3. Connect the machine's Caddy and Cloudflare

For a Caddy process installed directly on the host, the site block is approximately:

```caddyfile
recrutement.example.be {
    reverse_proxy 127.0.0.1:8080
}
```

Create the DNS record in Cloudflare and proxy it through Cloudflare. Use **Full (strict)** SSL mode,
not Flexible mode. Allow inbound TCP 80/443 to Caddy; do not expose 8080 or 3306 publicly.

The default `WEB_BIND_ADDRESS=127.0.0.1` works only when the main Caddy runs on the host. If the main
Caddy is itself a container, do not change the binding to `0.0.0.0` as a shortcut. Attach `web` and
that proxy to a deliberately managed shared Docker network, remove the published port, and proxy to
`web:8080` on that network.

`SESSION_COOKIE_SECURE=true` remains correct: the browser talks HTTPS to the outer Caddy even though
the final private hop uses HTTP.

## 4. Export applications

Create both a recoverable SQL dump and an Excel-compatible CSV in a new mode-700 private directory:

```sh
./deploy/export.sh
```

Choose another destination when desired:

```sh
./deploy/export.sh /srv/private-backups/cansat/2026-09-16
```

The command uses temporary files and only publishes them after both exports succeed. The default
`private-exports/` directory and `.env.docker` are ignored by Git. Copy backups off the VPS, encrypt
them, test restoration periodically, and apply a retention policy appropriate for applicants'
personal information.

## 5. Routine operations

```sh
# Status
docker compose --env-file .env.docker -f compose.production.yaml ps

# Logs
docker compose --env-file .env.docker -f compose.production.yaml logs --tail=100 web db

# Pull and deploy a registry image
docker compose --env-file .env.docker -f compose.production.yaml pull web
docker compose --env-file .env.docker -f compose.production.yaml up -d --no-build web

# Count applications without displaying personal data
docker compose --env-file .env.docker -f compose.production.yaml exec -T web php -r \
  'require "/app/public/includes/db.php"; echo db()->query("SELECT COUNT(*) FROM form")->fetchColumn(), PHP_EOL;'
```

Never use `docker compose down -v` on production: `-v` deletes the database volume.

## CI/CD strategy

The checked-in GitHub Actions workflow builds the exact production image, checks every PHP file,
runs the non-database tests, and validates the Compose model. Pull requests stop after verification.
Successful pushes to `main` and tags beginning with `v` also publish the already-tested local image
to GitHub Container Registry. Branch protection should require this workflow before merging.

The workflow publishes these GHCR tags automatically:

- every push to `main`: `ghcr.io/OWNER/REPOSITORY:sha-FULL_COMMIT_SHA` and `:latest`;
- every Git tag such as `v1.0.0`: `:sha-FULL_COMMIT_SHA` and `:v1.0.0`;
- pull requests: no image is pushed.

GitHub creates the container package automatically on the first successful push. The package is
normally private initially. Either make it public in the package settings or authenticate the VPS
with a token that has `read:packages` permission:

```sh
echo "$GHCR_READ_TOKEN" | docker login ghcr.io -u YOUR_GITHUB_USER --password-stdin
```

The CI-to-registry flow is:

1. CI builds and tests the local production image.
2. CI tags and pushes that same image using `GITHUB_TOKEN` with `packages: write` permission.
3. A protected deployment job connects to the VPS through SSH.
4. The job changes `APP_IMAGE` to the commit tag, pulls it, and runs Compose.
5. It checks `docker compose ps` and `https://recrutement.example.be/`.
6. The previous image tag is retained for rollback.

Publishing to GHCR needs no custom GitHub secret: Actions provides `GITHUB_TOKEN`. Keep these GitHub
environment secrets only when an automatic VPS deployment job is later enabled:

- `DEPLOY_HOST` and `DEPLOY_USER`;
- `DEPLOY_SSH_KEY` and a pinned `known_hosts` entry;
- optionally a registry read token if the image is private.

Do not put `DB_PASSWORD` or `DB_ROOT_PASSWORD` in the workflow. They remain solely in the server's
mode-600 `.env.docker`. A deployment command therefore needs no database secret from GitHub.

A safe server-side release sequence is:

```sh
cd /srv/cansat-recruitment
git fetch --prune origin
git checkout --detach COMMIT_SHA
docker compose --env-file .env.docker -f compose.production.yaml pull web
docker compose --env-file .env.docker -f compose.production.yaml up -d --no-build web
docker compose --env-file .env.docker -f compose.production.yaml ps
```

The current workflow intentionally publishes releases but does not SSH into the VPS. For the first
iteration, deploy the chosen immutable tag from the VPS. If automatic VPS deployment is later added,
use a GitHub **environment with required approval** for production. Database schema changes must
include a backup and an explicit migration step before automatic deployment is enabled.

Rollback an application-only release by restoring the previous `APP_IMAGE` tag and running the same
`pull` and `up -d --no-build web` commands. A database migration needs its own tested rollback or
forward-fix plan.
