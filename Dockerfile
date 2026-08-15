FROM dunglas/frankenphp:1-php8.4-bookworm

RUN cp "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini" \
    && install-php-extensions pdo_mysql

COPY deploy/php-production.ini "$PHP_INI_DIR/conf.d/zz-cansat-production.ini"
COPY deploy/Caddyfile /etc/frankenphp/Caddyfile

WORKDIR /app/public

COPY index.php form.php result.php ./
COPY assets ./assets
COPY includes ./includes
COPY scripts ./scripts

EXPOSE 8080

HEALTHCHECK --interval=30s --timeout=5s --start-period=10s --retries=3 \
    CMD php -r 'exit(@file_get_contents("http://127.0.0.1:8080/") === false ? 1 : 0);'
