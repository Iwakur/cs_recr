<?php

$runner = __DIR__ . '/result_case_runner.php';
$failures = [];

function renderResult(string $status): string
{
    global $runner;

    $html = shell_exec('php ' . escapeshellarg($runner) . ' ' . escapeshellarg($status)) ?? '';

    return html_entity_decode($html, ENT_QUOTES | ENT_HTML5, 'UTF-8');
}

function expectResult(string $status, string $expected, string $label): void
{
    global $failures;

    if (!str_contains(renderResult($status), $expected)) {
        $failures[] = "{$label} did not contain: {$expected}";
    }
}

expectResult('success', 'Candidature envoyée', 'Success result');
expectResult('error', "Échec de l'envoi", 'Error result');
expectResult('', 'Aucune candidature récente', 'Direct-access result');
expectResult('unexpected', 'Aucune candidature récente', 'Unknown-status result');

if ($failures !== []) {
    foreach ($failures as $failure) {
        fwrite(STDERR, "FAIL: {$failure}\n");
    }
    exit(1);
}

echo "All result page tests passed.\n";
