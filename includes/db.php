<?php

require_once __DIR__ . '/config.php';

function db(): ?PDO
{
    static $pdo = null;

    if ($pdo instanceof PDO) {
        return $pdo;
    }

    try {
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ];

        if (DB_AUTO_INIT) {
            if (!preg_match('/^[a-zA-Z0-9_]+$/', DB_NAME)) {
                throw new RuntimeException('Invalid database name.');
            }

            $serverDsn = 'mysql:host=' . DB_HOST . ';charset=utf8mb4';
            $serverPdo = new PDO($serverDsn, DB_USER, DB_PASS, $options);
            $serverPdo->exec(
                'CREATE DATABASE IF NOT EXISTS `' . DB_NAME . '` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci'
            );
        }

        $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';
        $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);

        if (DB_AUTO_INIT) {
            $schema = file_get_contents(__DIR__ . '/../sql/schema.sql');

            if ($schema === false) {
                throw new RuntimeException('Database schema could not be read.');
            }

            $pdo->exec($schema);
        }

        return $pdo;
    } catch (PDOException | RuntimeException) {
        return null;
    }
}
