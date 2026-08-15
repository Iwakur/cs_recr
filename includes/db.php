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

            $columns = $pdo->query('SHOW COLUMNS FROM applications')->fetchAll(PDO::FETCH_COLUMN);
            if (in_array('preferred_contact', $columns, true)) {
                if (!in_array('contact', $columns, true)) {
                    $pdo->exec('ALTER TABLE applications ADD COLUMN contact VARCHAR(275) NULL AFTER last_name');
                }
                $pdo->exec(
                    "UPDATE applications SET contact = COALESCE(contact, CONCAT(
                        preferred_contact,
                        ': ',
                        CASE preferred_contact
                            WHEN 'email' THEN COALESCE(email, '')
                            WHEN 'telegram' THEN COALESCE(telegram, '')
                            WHEN 'discord' THEN COALESCE(discord, '')
                            WHEN 'instagram' THEN COALESCE(instagram, '')
                            WHEN 'phone' THEN COALESCE(phone, '')
                            ELSE ''
                        END
                    ))"
                );
                $pdo->exec('ALTER TABLE applications MODIFY contact VARCHAR(275) NOT NULL');

                foreach (['email', 'phone', 'telegram', 'discord', 'instagram', 'preferred_contact'] as $legacyColumn) {
                    if (in_array($legacyColumn, $columns, true)) {
                        $pdo->exec("ALTER TABLE applications DROP COLUMN `{$legacyColumn}`");
                    }
                }
            }
        }

        return $pdo;
    } catch (PDOException | RuntimeException) {
        return null;
    }
}
