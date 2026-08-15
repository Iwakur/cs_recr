# Exporting CanSat applications

Use this procedure after applications close to create both:

- an SQL backup for complete recovery;
- a UTF-8, semicolon-separated CSV file for Excel.

The CSV exporter is command-line only. It is not a website page and returns HTTP 404 if a web server tries to execute it.

Store exports outside the public website directory. Application exports contain personal information and should not be committed to Git, emailed without encryption, or left world-readable on the VPS.

## Local export with DDEV

Run these commands from the project root.

Choose a private directory outside the repository:

```sh
export CANSAT_EXPORT_DIR="$HOME/cansat-private-exports/$(date +%Y-%m-%d)"
mkdir -p "$CANSAT_EXPORT_DIR"
chmod 700 "$CANSAT_EXPORT_DIR"
```

Create the recoverable SQL backup:

```sh
ddev export-db --database=cansat --gzip=false --file="$CANSAT_EXPORT_DIR/applications.sql"
```

Create the Excel-compatible CSV:

```sh
ddev exec php scripts/export_applications.php > "$CANSAT_EXPORT_DIR/applications.csv"
```

Restrict access and inspect the files:

```sh
chmod 600 "$CANSAT_EXPORT_DIR/applications.sql" "$CANSAT_EXPORT_DIR/applications.csv"
ls -lh "$CANSAT_EXPORT_DIR"
ddev exec mysql -N -udb -pdb cansat -e "SELECT COUNT(*) FROM form"
```

Open `applications.csv` in Excel. It uses UTF-8 with a BOM and semicolons, which preserves French accents and normally matches Belgian/French Excel settings. Quoted multiline answers are valid CSV and should remain inside their cells.

## VPS export with Docker Compose

Here, “Docker Compose” means the deployment started with `docker compose up -d`. Composer, the PHP dependency manager, is not involved in exporting the database.

Run the commands from the VPS directory containing `compose.yaml`.

First identify the service names:

```sh
docker compose config --services
docker compose ps
```

The examples below assume:

- the PHP application service is named `web`;
- the database service is named `db`;
- the project is located at `/var/www/html` inside the web container;
- MariaDB uses the standard `MARIADB_*` environment variables.
- the database is named `cansat` and its application table is named `form`.

Replace those values if your `compose.yaml` uses different names or paths.

In the production `compose.yaml`, keep the database and application environments aligned:

```yaml
services:
  web:
    environment:
      DB_HOST: db
      DB_NAME: cansat
      DB_USER: your_database_user
      DB_PASS: your_private_password
  db:
    environment:
      MARIADB_DATABASE: cansat
      MARIADB_USER: your_database_user
      MARIADB_PASSWORD: your_private_password
```

Use secrets or a private `.env` file for the real password; do not commit it.

Create a private host directory outside the deployed website:

```sh
export CANSAT_EXPORT_DIR="$HOME/cansat-private-exports/$(date +%Y-%m-%d)"
mkdir -p "$CANSAT_EXPORT_DIR"
chmod 700 "$CANSAT_EXPORT_DIR"
```

Create the SQL backup from a MariaDB service:

```sh
docker compose exec -T db sh -lc \
  'mariadb-dump -u"$MARIADB_USER" -p"$MARIADB_PASSWORD" "$MARIADB_DATABASE"' \
  > "$CANSAT_EXPORT_DIR/applications.sql"
```

If the database image uses MySQL-style variables instead, use:

```sh
docker compose exec -T db sh -lc \
  'mysqldump -u"$MYSQL_USER" -p"$MYSQL_PASSWORD" "$MYSQL_DATABASE"' \
  > "$CANSAT_EXPORT_DIR/applications.sql"
```

Create the Excel-compatible CSV through the application container:

```sh
docker compose exec -T web \
  php /var/www/html/scripts/export_applications.php \
  > "$CANSAT_EXPORT_DIR/applications.csv"
```

Restrict and inspect the files:

```sh
chmod 600 "$CANSAT_EXPORT_DIR/applications.sql" "$CANSAT_EXPORT_DIR/applications.csv"
ls -lh "$CANSAT_EXPORT_DIR"
```

Check the application count through the PHP service without printing any personal data:

```sh
docker compose exec -T web php -r \
  'require "/var/www/html/includes/db.php"; echo db()->query("SELECT COUNT(*) FROM form")->fetchColumn(), PHP_EOL;'
```

The count should equal the number of data rows visible in Excel. Multiline CSV cells can span physical text lines, so `wc -l applications.csv` is not a reliable record count.

## Copy exports from the VPS

Run this on your own computer, replacing the VPS account and hostname:

```sh
scp vps-user@example.com:"~/cansat-private-exports/2026-09-16/applications.sql" .
scp vps-user@example.com:"~/cansat-private-exports/2026-09-16/applications.csv" .
```

After confirming both local files open correctly, remove the VPS copies when they are no longer needed. Keep at least one protected SQL backup according to your retention policy.

## Verification checklist

- The SQL file is non-empty and begins with dump comments or SQL statements.
- The CSV opens in Excel with one column per database field.
- Names and French accents display correctly.
- Long answers remain in a single cell.
- The database count matches the number of applications reviewed.
- Both files are stored privately and are not tracked by Git.
- The original database is not dropped or truncated during export.

Export commands are read-only. Do not run `DROP`, `TRUNCATE`, or `DELETE` as part of the real export procedure.
