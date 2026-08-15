<?php

require_once __DIR__ . '/config.php';

function startAppSession(): void
{
    if (session_status() === PHP_SESSION_ACTIVE) {
        return;
    }

    session_set_cookie_params([
        'httponly' => true,
        'samesite' => 'Lax',
        'secure' => SESSION_COOKIE_SECURE,
    ]);
    session_start();
}

function redirectToResult(string $status): never
{
    startAppSession();
    $_SESSION['submission_result'] = $status;
    session_write_close();

    header('Location: result.php');
    exit;
}
