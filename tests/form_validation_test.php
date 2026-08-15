<?php

$root = dirname(__DIR__);
$runner = __DIR__ . '/form_case_runner.php';
$form = file_get_contents($root . '/form.php');
$rules = require $root . '/includes/form_rules.php';

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
        'programming_level' => 'Bases',
        'electronics_level' => 'Bases',
        'cad_level' => 'Aucune expérience',
        'science_level' => 'Intermédiaire',
        'english_listening_level' => 'Intermédiaire',
        'english_speaking_level' => 'Élémentaire',
        'role_flexibility' => 'Oui',
        'availability' => ['Lundi'],
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
assertContains($form, 'name="communication_experience"', 'Communication experience textarea');
assertContains($form, 'name="english_listening_level"', 'English oral comprehension level');
assertContains($form, 'name="english_speaking_level"', 'English oral speaking level');
assertContains($form, 'Disponibilités pour les réunions', 'Availability legend accent');
assertContains($form, 'otherClassInput.disabled = !isOther;', 'Disabled hidden class input');
assertContains($form, 'input.disabled = !isSelected;', 'Disabled hidden contact inputs');
assertContains($form, 'setCustomValidity', 'Duplicate role browser validation');
assertContains($form, 'data-character-counter', 'Character counters');
assertContains($form, 'validateAvailability', 'Availability browser validation');
assertContains($form, '$formRules[\'lengths\'][\'motivation\']', 'Shared motivation maxlength');
assertContains($form, "Les champs marqués d'un * sont obligatoires.", 'Required field legend');
assertContains($form, 'Prénom *', 'Required first name marker');
assertContains($form, 'Classe précise *', 'Conditionally required class marker');
assertContains($form, 'Deuxième choix', 'Optional second role label');
assertNotContains($form, 'Deuxième choix *', 'Optional second role marker');
assertContains($form, 'Niveau en programmation *', 'Required programming level marker');
assertContains($form, 'Compréhension orale en anglais *', 'Required English level marker');
assertContains($form, 'Flexibilité de rôle *', 'Required role flexibility marker');
if (($rules['lengths']['motivation'] ?? null) !== 800) {
    fail('Motivation rule should be 800 characters.');
}
if (($rules['request_max_bytes'] ?? null) !== 262144) {
    fail('POST body limit should be 262144 bytes.');
}
assertNotContains($form, '???', 'Placeholder question');
assertNotContains($form, 'Scientific', 'Mixed English role label');
assertNotContains($form, 'reunions', 'Unaccented reunions');

assertError(
    ['second_choice' => 'Programmation'],
    'Le deuxième choix doit être différent du rôle préféré.',
    'Duplicate role validation'
);

assertError(
    ['first_name' => str_repeat('a', 101)],
    'Le prénom doit faire 100 caractères maximum.',
    'First name length validation'
);

assertError(
    ['programming_experience' => str_repeat('a', 1501)],
    "L'expérience en programmation doit faire 1500 caractères maximum.",
    'Experience length validation'
);

assertError(
    ['age' => '16.5'],
    "L'âge doit être un nombre entier entre 16 et 25 ans.",
    'Non-integer age validation'
);

assertError(
    ['first_name' => ['Alice']],
    'Certaines données envoyées ont un format invalide.',
    'Scalar field tampering validation'
);

assertError(
    ['known_skills' => [['Python']]],
    'Certaines données envoyées ont un format invalide.',
    'Nested multi-value field tampering validation'
);

assertError(
    ['availability' => array_fill(0, 20, 'Lundi')],
    'Certaines données envoyées ont un format invalide.',
    'Oversized multi-value field validation'
);

assertError(
    ['known_skills' => ['Python', 'Python']],
    'Une compétence ne peut être choisie qu’une seule fois.',
    'Duplicate skill validation'
);

assertError(
    ['consent' => 'yes'],
    'Vous devez accepter que vos informations soient utilisées pour le recrutement.',
    'Exact consent validation'
);

assertError(
    ['consent' => ['1']],
    'Vous devez accepter que vos informations soient utilisées pour le recrutement.',
    'Consent array tampering validation'
);

assertError(
    ['class' => 'Autre', 'other_class' => ''],
    'Indiquez votre classe si vous choisissez "Autre".',
    'Other class validation'
);

assertError(
    ['preferred_contact' => 'telegram', 'discord' => '', 'telegram' => ''],
    'Indiquez votre contact pour le moyen de communication choisi.',
    'Preferred contact required field validation'
);

assertNotContains($form, "'phone' => 'Téléphone'", 'Phone contact option');
assertNotContains($form, 'name="phone"', 'Phone contact input');
assertContains($form, "'contact' => \$values['preferred_contact'] . ': ' . \$values[\$values['preferred_contact']]", 'Prefixed contact storage');
assertContains($form, "redirectToResult('success')", 'Successful submission redirect');
assertContains($form, "redirectToResult('error')", 'Persistence error redirect');

assertError(
    ['preferred_contact' => 'phone', 'discord' => '', 'phone' => '+32 470 12 34 56'],
    "Le moyen de contact choisi n'est pas valide.",
    'Removed phone option backend validation'
);

assertError(
    ['english_listening_level' => 'Expert'],
    "Un niveau d'anglais choisi n'est pas valide.",
    'English oral level validation'
);

assertError(
    ['programming_level' => ''],
    'Choisissez votre niveau en programmation.',
    'Required experience level validation'
);

assertError(
    ['english_speaking_level' => ''],
    "Choisissez votre niveau d'expression orale en anglais.",
    'Required English level validation'
);

assertError(
    ['role_flexibility' => ''],
    'Indiquez votre flexibilité de rôle.',
    'Required role flexibility validation'
);

assertError(
    ['motivation' => str_repeat('a', 801)],
    'La motivation doit faire 800 caractères maximum.',
    'Motivation length validation'
);

assertError(
    ['availability' => []],
    'Choisissez au moins une disponibilité après les cours.',
    'Availability checkbox validation'
);

assertError(
    ['availability' => 'Samedi toute la journée'],
    'Certaines données envoyées ont un format invalide.',
    'Scalar availability tampering validation'
);

if ($failures !== []) {
    foreach ($failures as $failure) {
        fwrite(STDERR, "FAIL: {$failure}\n");
    }

    exit(1);
}

echo "All form validation tests passed.\n";
