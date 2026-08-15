#!/bin/sh
set -eu

project_dir=$(CDPATH= cd -- "$(dirname -- "$0")/.." && pwd)
env_file=${CANSAT_ENV_FILE:-"$project_dir/.env.docker"}
compose_file="$project_dir/compose.production.yaml"
schema_file="$project_dir/sql/schema.sql"

if [ ! -f "$env_file" ]; then
    echo "Missing $env_file. Copy .env.docker.example and set its passwords." >&2
    exit 1
fi

cd "$project_dir"
docker compose --env-file "$env_file" -f "$compose_file" up -d db

echo "Waiting for MariaDB..."
until docker compose --env-file "$env_file" -f "$compose_file" exec -T db \
    healthcheck.sh --connect --innodb_initialized >/dev/null 2>&1; do
    sleep 2
done

docker compose --env-file "$env_file" -f "$compose_file" exec -T db sh -c \
    'exec mariadb -uroot -p"$MARIADB_ROOT_PASSWORD" "$MARIADB_DATABASE"' < "$schema_file"

docker compose --env-file "$env_file" -f "$compose_file" exec -T db sh -c \
    'exec mariadb -N -u"$MARIADB_USER" -p"$MARIADB_PASSWORD" "$MARIADB_DATABASE" -e "SELECT COUNT(*) FROM form"'

echo "Database initialization complete. The number above is the current application count."
