<?php

$status = $argv[1] ?? '';

ob_start();
require_once __DIR__ . '/../includes/session.php';

startAppSession();
if ($status !== '') {
    $_SESSION['submission_result'] = $status;
}
session_write_close();

require __DIR__ . '/../result.php';
echo ob_get_clean();
