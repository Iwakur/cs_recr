<?php

require dirname(__DIR__) . '/includes/db.php';

$pdo = db();
if (!$pdo instanceof PDO) {
    fwrite(STDERR, "Database unavailable.\n");
    exit(1);
}

$columns = $pdo->query('SHOW COLUMNS FROM applications')->fetchAll(PDO::FETCH_COLUMN);
$requiredColumns = ['contact'];
$legacyColumns = ['email', 'phone', 'telegram', 'discord', 'instagram', 'preferred_contact'];

foreach ($requiredColumns as $column) {
    if (!in_array($column, $columns, true)) {
        fwrite(STDERR, "Missing required column: {$column}\n");
        exit(1);
    }
}

foreach ($legacyColumns as $column) {
    if (in_array($column, $columns, true)) {
        fwrite(STDERR, "Legacy contact column still exists: {$column}\n");
        exit(1);
    }
}

echo "Database schema validation passed.\n";
