set -e

if [ -f .env ]; then
    set -- $(grep -E '^(DB_HOST|DB_PORT|DB_USERNAME|DB_PASSWORD)=' .env)
    eval "$@"
fi

DB_HOST="${DB_HOST:-127.0.0.1}"
DB_PORT="${DB_PORT:-3306}"
DB_USERNAME="${DB_USERNAME:-root}"
DB_PASSWORD="${DB_PASSWORD:-}"
DB_DATABASE="${DB_DATABASE:-distretto10}"
DB_TEST_DATABASE="${DB_DATABASE}_test"

MYSQL="mysql -h $DB_HOST -P $DB_PORT -u $DB_USERNAME"
[ -n "$DB_PASSWORD" ] && MYSQL="$MYSQL -p$DB_PASSWORD"

$MYSQL -e "create database if not exists \`$DB_DATABASE\` character set utf8mb4 collate utf8mb4_unicode_ci;"
php artisan migrate --no-interaction
