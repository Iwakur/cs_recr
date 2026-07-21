<?php

$payload = json_decode(base64_decode($argv[1] ?? ''), true);

if (!is_array($payload)) {
    fwrite(STDERR, "Invalid payload\n");
    exit(2);
}

$_SERVER['REQUEST_METHOD'] = 'POST';
$_POST = $payload;

ob_start();
require __DIR__ . '/../form.php';
ob_end_clean();

echo json_encode($errors ?? [], JSON_UNESCAPED_UNICODE);
