<?php

require dirname(__DIR__) . '/includes/db.php';

$pdo = db();
if (!$pdo instanceof PDO) {
    fwrite(STDERR, "Database unavailable.\n");
    exit(1);
}

$columns = $pdo->query('SHOW COLUMNS FROM applications')->fetchAll(PDO::FETCH_COLUMN);
$expectedColumns = [
    'id', 'first_name', 'last_name', 'contact', 'class', 'age', 'gender',
    'preferred_role', 'second_choice', 'motivation', 'programming_level',
    'electronics_level', 'cad_level', 'science_level', 'english_listening_level',
    'english_speaking_level', 'known_skills', 'problem_solving', 'role_flexibility',
    'programming_experience', 'electronics_experience', 'cad_experience',
    'science_experience', 'communication_experience', 'other_projects',
    'availability', 'time_commitment', 'consent', 'created_at',
];

if ($columns !== $expectedColumns) {
    fwrite(STDERR, 'Unexpected database columns: ' . implode(', ', $columns) . "\n");
    exit(1);
}

echo "Database schema validation passed.\n";
