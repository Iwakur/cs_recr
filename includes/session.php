<?php

function startAppSession(): void
{
    if (session_status() === PHP_SESSION_ACTIVE) {
        return;
    }

    session_set_cookie_params([
        'httponly' => true,
        'samesite' => 'Lax',
        'secure' => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
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
