<?php

define('DB_HOST', getenv('DB_HOST') ?: 'db');
define('DB_NAME', getenv('DB_NAME') ?: 'db');
define('DB_USER', getenv('DB_USER') ?: 'db');
define('DB_PASS', getenv('DB_PASS') ?: 'db');

$autoInit = getenv('DB_AUTO_INIT');
define('DB_AUTO_INIT', $autoInit === false || filter_var($autoInit, FILTER_VALIDATE_BOOLEAN));
