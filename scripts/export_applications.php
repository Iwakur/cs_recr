<?php

if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

require dirname(__DIR__) . '/includes/db.php';

$pdo = db();
if (!$pdo instanceof PDO) {
    fwrite(STDERR, "Database unavailable.\n");
    exit(1);
}

$columns = $pdo->query('SHOW COLUMNS FROM form')->fetchAll(PDO::FETCH_COLUMN);
if ($columns === []) {
    fwrite(STDERR, "The form table has no columns.\n");
    exit(1);
}

$quotedColumns = array_map(
    static fn (string $column): string => '`' . str_replace('`', '``', $column) . '`',
    $columns
);
$statement = $pdo->query(
    'SELECT ' . implode(', ', $quotedColumns) . ' FROM form ORDER BY created_at, id'
);

$output = fopen('php://output', 'wb');
if ($output === false) {
    fwrite(STDERR, "Could not open standard output.\n");
    exit(1);
}

fwrite($output, "\xEF\xBB\xBF");
fputcsv($output, $columns, ';', '"', '', "\r\n");

while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
    fputcsv($output, $row, ';', '"', '', "\r\n");
}
