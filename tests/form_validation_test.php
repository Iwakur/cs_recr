<?php

$root = dirname(__DIR__);
$runner = __DIR__ . '/form_case_runner.php';
$form = file_get_contents($root . '/form.php');

$failures = [];

function fail(string $message): void
{
    global $failures;
    $failures[] = $message;
}

function assertContains(string $haystack, string $needle, string $label): void
{
    if (!str_contains($haystack, $needle)) {
        fail($label . ' was not found.');
    }
}

function assertNotContains(string $haystack, string $needle, string $label): void
{
    if (str_contains($haystack, $needle)) {
        fail($label . ' should not be present.');
    }
}

function formErrors(array $override): array
{
    global $runner;

    $basePost = [
        'first_name' => 'Alice',
        'last_name' => 'Dupont',
        'preferred_contact' => 'discord',
        'discord' => 'alice.cansat',
        'class' => '4TTR info',
        'age' => '16',
        'gender' => 'Fille',
        'preferred_role' => 'Programmation',
        'second_choice' => 'Électronique',
        'motivation' => 'Je veux apprendre et contribuer au projet.',
        'availability' => ['Lundi après les cours'],
        'time_commitment' => 'Une réunion par semaine et du travail à la maison.',
        'consent' => '1',
    ];

    $payload = base64_encode(json_encode(array_replace($basePost, $override), JSON_UNESCAPED_UNICODE));
    $command = 'php ' . escapeshellarg($runner) . ' ' . escapeshellarg($payload);
    $output = shell_exec($command);

    if ($output === null) {
        fail('Could not run form case runner.');
        return [];
    }

    $errors = json_decode($output, true);

    if (!is_array($errors)) {
        fail('Runner did not return JSON errors. Output: ' . $output);
        return [];
    }

    return $errors;
}

function assertError(array $override, string $expectedError, string $label): void
{
    $errors = formErrors($override);

    if (!in_array($expectedError, $errors, true)) {
        fail($label . ' did not produce expected error: ' . $expectedError);
    }
}

assertContains($form, 'name="science_experience"', 'Science experience textarea');
assertContains($form, 'Disponibilités pour les réunions', 'Availability legend accent');
assertContains($form, 'otherClassInput.disabled = !isOther;', 'Disabled hidden class input');
assertContains($form, 'input.disabled = !isSelected;', 'Disabled hidden contact inputs');
assertContains($form, 'setCustomValidity', 'Duplicate role browser validation');
assertContains($form, 'maxlength="800"', 'Motivation maxlength');
assertNotContains($form, '???', 'Placeholder question');
assertNotContains($form, 'Scientific', 'Mixed English role label');
assertNotContains($form, 'reunions', 'Unaccented reunions');

assertError(
    ['second_choice' => 'Programmation'],
    'Le deuxième choix doit être différent du rôle préféré.',
    'Duplicate role validation'
);

assertError(
    ['class' => 'Autre', 'other_class' => ''],
    'Indique ta classe si tu choisis "Autre".',
    'Other class validation'
);

assertError(
    ['preferred_contact' => 'telegram', 'discord' => '', 'telegram' => ''],
    'Indique ton contact pour le moyen de communication choisi.',
    'Preferred contact required field validation'
);

assertError(
    ['preferred_contact' => 'phone', 'discord' => '', 'phone' => 'abc'],
    "Le numéro de téléphone n'est pas valide.",
    'Phone format validation'
);

assertError(
    ['motivation' => str_repeat('a', 801)],
    'La motivation doit faire 800 caractères maximum.',
    'Motivation length validation'
);

assertError(
    ['availability' => []],
    'Choisis au moins une disponibilité après les cours.',
    'Availability checkbox validation'
);

assertError(
    ['availability' => 'Samedi toute la journée'],
    "Une disponibilité choisie n'est pas valide.",
    'Scalar availability tampering validation'
);

if ($failures !== []) {
    foreach ($failures as $failure) {
        fwrite(STDERR, "FAIL: {$failure}\n");
    }

    exit(1);
}

echo "All form validation tests passed.\n";
