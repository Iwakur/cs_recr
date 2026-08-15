<?php

require_once __DIR__ . '/includes/session.php';

startAppSession();
$status = $_SESSION['submission_result'] ?? null;
unset($_SESSION['submission_result']);
session_write_close();

$result = match ($status) {
    'success' => [
        'title' => 'Candidature envoyée',
        'message' => 'Votre candidature a bien été enregistrée. Nous vous contacterons après la date limite.',
    ],
    'error' => [
        'title' => "Échec de l'envoi",
        'message' => "Votre candidature n'a pas pu être enregistrée. Veuillez réessayer dans quelques instants.",
    ],
    default => [
        'title' => 'Aucune candidature récente',
        'message' => "Cette page affiche le résultat après l'envoi du formulaire.",
    ],
};

include_once __DIR__ . '/includes/header.php';
?>

<main class="container form-page">
    <section class="final-cta">
        <h1><?= htmlspecialchars($result['title'], ENT_QUOTES, 'UTF-8') ?></h1>
        <p><?= htmlspecialchars($result['message'], ENT_QUOTES, 'UTF-8') ?></p>
        <?php if ($status === 'error'): ?>
            <a role="button" href="form.php">Réessayer</a>
        <?php else: ?>
            <a role="button" href="index.php">Retour à l'accueil</a>
        <?php endif; ?>
    </section>
</main>

<?php include_once __DIR__ . '/includes/footer.php'; ?>
