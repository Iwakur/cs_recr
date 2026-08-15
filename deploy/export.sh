#!/bin/sh
set -eu

project_dir=$(CDPATH= cd -- "$(dirname -- "$0")/.." && pwd)
env_file=${CANSAT_ENV_FILE:-"$project_dir/.env.docker"}
compose_file="$project_dir/compose.production.yaml"
timestamp=$(date +%Y-%m-%d_%H-%M-%S)
export_dir=${1:-"$project_dir/private-exports/$timestamp"}

if [ ! -f "$env_file" ]; then
    echo "Missing $env_file. Copy .env.docker.example and set its passwords." >&2
    exit 1
fi

umask 077
mkdir -p "$export_dir"
sql_tmp="$export_dir/applications.sql.tmp"
csv_tmp="$export_dir/applications.csv.tmp"
trap 'rm -f "$sql_tmp" "$csv_tmp"' EXIT HUP INT TERM

cd "$project_dir"
docker compose --env-file "$env_file" -f "$compose_file" exec -T db sh -c \
    'exec mariadb-dump --single-transaction --quick -uroot -p"$MARIADB_ROOT_PASSWORD" "$MARIADB_DATABASE"' \
    > "$sql_tmp"

docker compose --env-file "$env_file" -f "$compose_file" exec -T web \
    php /app/public/scripts/export_applications.php > "$csv_tmp"

test -s "$sql_tmp"
test -s "$csv_tmp"
mv "$sql_tmp" "$export_dir/applications.sql"
mv "$csv_tmp" "$export_dir/applications.csv"
trap - EXIT HUP INT TERM

echo "Private exports created in: $export_dir"
ls -lh "$export_dir/applications.sql" "$export_dir/applications.csv"
