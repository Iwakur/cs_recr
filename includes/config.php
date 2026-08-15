<?php

define('DB_HOST', getenv('DB_HOST') ?: 'db');
define('DB_NAME', getenv('DB_NAME') ?: 'cansat');
define('DB_USER', getenv('DB_USER') ?: 'db');
define('DB_PASS', getenv('DB_PASS') ?: 'db');

$sessionCookieSecure = getenv('SESSION_COOKIE_SECURE');
define(
    'SESSION_COOKIE_SECURE',
    $sessionCookieSecure === false || filter_var($sessionCookieSecure, FILTER_VALIDATE_BOOLEAN)
);

$autoInit = getenv('DB_AUTO_INIT');
define('DB_AUTO_INIT', $autoInit === false || filter_var($autoInit, FILTER_VALIDATE_BOOLEAN));
