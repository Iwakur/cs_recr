<?php
require_once __DIR__ . '/includes/db.php';

$errors = [];
$values = [
    'first_name' => '',
    'last_name' => '',
    'email' => '',
    'class' => '',
    'age' => '',
    'preferred_role' => '',
    'second_choice' => '',
    'motivation' => '',
    'programming_experience' => '',
    'electronics_experience' => '',
    'cad_experience' => '',
    'other_projects' => '',
    'availability' => '',
];

function field(string $name): string
{
    global $values;
    return htmlspecialchars($values[$name] ?? '', ENT_QUOTES, 'UTF-8');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($values as $key => $_) {
        $values[$key] = trim($_POST[$key] ?? '');
    }

    $required = [
        'first_name' => 'Le prénom est obligatoire.',
        'last_name' => 'Le nom est obligatoire.',
        'email' => "L'adresse e-mail est obligatoire.",
        'class' => 'La classe est obligatoire.',
        'age' => "L'âge est obligatoire.",
        'preferred_role' => 'Choisis un rôle préféré.',
        'motivation' => 'Explique brièvement ta motivation.',
        'availability' => 'Indique ta disponibilité.',
    ];

    foreach ($required as $field => $message) {
        if ($values[$field] === '') {
            $errors[] = $message;
        }
    }

    if ($values['email'] !== '' && !filter_var($values['email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = "L'adresse e-mail n'est pas valide.";
    }

    if ($values['age'] !== '') {
        $age = filter_var($values['age'], FILTER_VALIDATE_INT);
        if ($age === false || $age < 10 || $age > 25) {
            $errors[] = "L'âge doit être un nombre réaliste.";
        }
    }

    if (!isset($_POST['consent'])) {
        $errors[] = 'Tu dois accepter que tes informations soient utilisées pour le recrutement.';
    }

    if ($errors === []) {
        $pdo = db();

        if ($pdo === null) {
            $errors[] = "La base de données n'est pas encore configurée.";
        } else {
            try {
                $statement = $pdo->prepare(
                    'INSERT INTO applications (
                        first_name, last_name, email, class, age, preferred_role, second_choice,
                        motivation, programming_experience, electronics_experience, cad_experience,
                        other_projects, availability, consent
                    ) VALUES (
                        :first_name, :last_name, :email, :class, :age, :preferred_role, :second_choice,
                        :motivation, :programming_experience, :electronics_experience, :cad_experience,
                        :other_projects, :availability, :consent
                    )'
                );

                $statement->execute([
                    'first_name' => $values['first_name'],
                    'last_name' => $values['last_name'],
                    'email' => $values['email'],
                    'class' => $values['class'],
                    'age' => (int) $values['age'],
                    'preferred_role' => $values['preferred_role'],
                    'second_choice' => $values['second_choice'],
                    'motivation' => $values['motivation'],
                    'programming_experience' => $values['programming_experience'],
                    'electronics_experience' => $values['electronics_experience'],
                    'cad_experience' => $values['cad_experience'],
                    'other_projects' => $values['other_projects'],
                    'availability' => $values['availability'],
                    'consent' => 1,
                ]);

                header('Location: /success.php');
                exit;
            } catch (PDOException) {
                $errors[] = "La candidature n'a pas pu être enregistrée.";
            }
        }
    }
}

include_once __DIR__ . '/includes/header.php';
?>

<main class="container form-page">
    <p><a href="/index.php">&larr; Retour à l'accueil</a></p>

    <section>
        <h1>Postuler à l'équipe CanSat</h1>
        <p>
            Présente-toi rapidement, indique ce qui t'intéresse et explique pourquoi
            tu veux participer au projet.
        </p>
    </section>

    <?php if ($errors !== []): ?>
        <article class="form-errors" aria-live="polite">
            <h2>À corriger</h2>
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></li>
                <?php endforeach; ?>
            </ul>
        </article>
    <?php endif; ?>

    <form method="post" action="/form.php">
        <fieldset>
            <legend>Informations personnelles</legend>

            <label>
                Prénom
                <input type="text" name="first_name" value="<?= field('first_name') ?>" required>
            </label>

            <label>
                Nom
                <input type="text" name="last_name" value="<?= field('last_name') ?>" required>
            </label>

            <label>
                E-mail
                <input type="email" name="email" value="<?= field('email') ?>" required>
            </label>

            <div class="form-row">
                <label>
                    Classe
                    <input type="text" name="class" value="<?= field('class') ?>" required>
                </label>

                <label>
                    Âge
                    <input type="number" name="age" min="10" max="25" value="<?= field('age') ?>" required>
                </label>
            </div>
        </fieldset>

        <fieldset>
            <legend>Intérêts</legend>

            <label>
                Rôle préféré
                <select name="preferred_role" required>
                    <option value="">Choisir...</option>
                    <?php foreach (['Programmation', 'Électronique', 'Mécanique / CAD', 'Données', 'Communication', 'Gestion de projet'] as $role): ?>
                        <option value="<?= $role ?>" <?= field('preferred_role') === $role ? 'selected' : '' ?>><?= $role ?></option>
                    <?php endforeach; ?>
                </select>
            </label>

            <label>
                Deuxième choix
                <select name="second_choice">
                    <option value="">Aucun pour l'instant</option>
                    <?php foreach (['Programmation', 'Électronique', 'Mécanique / CAD', 'Données', 'Communication', 'Gestion de projet'] as $role): ?>
                        <option value="<?= $role ?>" <?= field('second_choice') === $role ? 'selected' : '' ?>><?= $role ?></option>
                    <?php endforeach; ?>
                </select>
            </label>
        </fieldset>

        <label>
            Pourquoi veux-tu rejoindre l'équipe ?
            <textarea name="motivation" rows="5" required><?= field('motivation') ?></textarea>
        </label>

        <fieldset>
            <legend>Expérience</legend>

            <label>
                Programmation
                <textarea name="programming_experience" rows="3"><?= field('programming_experience') ?></textarea>
            </label>

            <label>
                Électronique
                <textarea name="electronics_experience" rows="3"><?= field('electronics_experience') ?></textarea>
            </label>

            <label>
                CAD / 3D
                <textarea name="cad_experience" rows="3"><?= field('cad_experience') ?></textarea>
            </label>

            <label>
                Autres projets
                <textarea name="other_projects" rows="3"><?= field('other_projects') ?></textarea>
            </label>
        </fieldset>

        <label>
            Peux-tu participer à des réunions après les cours ?
            <textarea name="availability" rows="3" required><?= field('availability') ?></textarea>
        </label>

        <label>
            <input type="checkbox" name="consent" value="1" required>
            J'accepte que mes informations soient utilisées pour le recrutement de l'équipe CanSat.
        </label>

        <button type="submit">Envoyer la candidature</button>
    </form>

    <section class="privacy-note">
        <h2>Confidentialité</h2>
        <p>
            Les informations envoyées servent uniquement à organiser le recrutement
            de l'équipe CanSat et à te recontacter.
        </p>
    </section>
</main>

<?php include_once __DIR__ . '/includes/footer.php'; ?>
