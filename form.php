<?php
require_once __DIR__ . '/includes/db.php';

$errors = [];
$classOptions = ['4TTR info', '5TTR info', '6TTR info', 'Autre'];
$roleOptions = [
        'Programmation',
        'Électronique',
        'Mécanique / CAD',
        'Données / sciences',
        'Tests et validation',
        'Communication',
];
$contactOptions = [
        'email' => 'E-mail',
        'phone' => 'Téléphone',
        'telegram' => 'Telegram',
        'discord' => 'Discord',
        'instagram' => 'Instagram',
];
$genderOptions = ['Fille', 'Garçon', 'Autre / préfère ne pas répondre'];
$experienceLevels = ['Aucune expérience', 'Bases', 'Intermédiaire', 'Avancé'];
$availabilityOptions = [
        'Lundi après les cours',
        'Mardi après les cours',
        'Mercredi après-midi',
        'Jeudi après les cours',
        'Vendredi après les cours',
        'Certains week-ends',
];
$skillOptions = [
        'Arduino / ESP32',
        'Capteurs',
        'C / C++',
        'Python',
        'HTML / CSS',
        'Soudure',
        'Circuits électroniques',
        'Modélisation 3D',
        'Impression 3D',
        'Analyse de données',
        'Présentation orale',
        'Design / réseaux sociaux',
];
$values = [
        'first_name' => '',
        'last_name' => '',
        'email' => '',
        'phone' => '',
        'telegram' => '',
        'discord' => '',
        'instagram' => '',
        'preferred_contact' => '',
        'class' => '',
        'other_class' => '',
        'age' => '',
        'gender' => '',
        'preferred_role' => '',
        'second_choice' => '',
        'motivation' => '',
        'programming_level' => '',
        'electronics_level' => '',
        'cad_level' => '',
        'science_level' => '',
        'known_skills' => [],
        'problem_solving' => '',
        'role_flexibility' => '',
        'programming_experience' => '',
        'electronics_experience' => '',
        'cad_experience' => '',
        'science_experience' => '',
        'other_projects' => '',
        'availability' => [],
        'time_commitment' => '',
];

function field(string $name): string
{
    global $values;
    return htmlspecialchars($values[$name] ?? '', ENT_QUOTES, 'UTF-8');
}

function selected(string $name, string $value): string
{
    global $values;
    return ($values[$name] ?? '') === $value ? ' selected' : '';
}

function checked(string $name, string $value): string
{
    global $values;
    $currentValue = $values[$name] ?? '';

    if (is_array($currentValue)) {
        return in_array($value, $currentValue, true) ? ' checked' : '';
    }

    return $currentValue === $value ? ' checked' : '';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($values as $key => $_) {
        if (is_array($_)) {
            $submittedValues = $_POST[$key] ?? [];
            $values[$key] = array_values(array_filter(array_map('trim', is_array($submittedValues) ? $submittedValues : [$submittedValues])));
        } else {
            $values[$key] = trim($_POST[$key] ?? '');
        }
    }

    $required = [
            'first_name' => 'Le prénom est obligatoire.',
            'last_name' => 'Le nom est obligatoire.',
            'preferred_contact' => 'Choisis le moyen de contact que tu préfères.',
            'class' => 'La classe est obligatoire.',
            'age' => "L'âge est obligatoire.",
            'gender' => 'Choisis une option pour le genre.',
            'preferred_role' => 'Choisis un rôle préféré.',
            'motivation' => 'Explique brièvement ta motivation.',
            'time_commitment' => 'Indique combien de temps tu peux consacrer au projet.',
    ];

    foreach ($required as $field => $message) {
        if ($values[$field] === '') {
            $errors[] = $message;
        }
    }

    if ($values['email'] !== '' && !filter_var($values['email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = "L'adresse e-mail n'est pas valide.";
    }

    if ($values['phone'] !== '' && !preg_match('/^[0-9 +().-]{6,25}$/', $values['phone'])) {
        $errors[] = "Le numéro de téléphone n'est pas valide.";
    }

    if ($values['preferred_contact'] !== '' && !array_key_exists($values['preferred_contact'], $contactOptions)) {
        $errors[] = 'Le moyen de contact choisi n\'est pas valide.';
    }

    if (
            $values['preferred_contact'] !== ''
            && array_key_exists($values['preferred_contact'], $contactOptions)
            && $values[$values['preferred_contact']] === ''
    ) {
        $errors[] = 'Indique ton contact pour le moyen de communication choisi.';
    }

    if ($values['class'] !== '' && !in_array($values['class'], $classOptions, true)) {
        $errors[] = "La classe choisie n'est pas valide.";
    }

    if ($values['class'] === 'Autre' && $values['other_class'] === '') {
        $errors[] = 'Indique ta classe si tu choisis "Autre".';
    }

    if ($values['gender'] !== '' && !in_array($values['gender'], $genderOptions, true)) {
        $errors[] = "L'option de genre choisie n'est pas valide.";
    }

    if ($values['preferred_role'] !== '' && !in_array($values['preferred_role'], $roleOptions, true)) {
        $errors[] = "Le rôle préféré choisi n'est pas valide.";
    }

    if ($values['second_choice'] !== '' && !in_array($values['second_choice'], $roleOptions, true)) {
        $errors[] = "Le deuxième choix de rôle n'est pas valide.";
    }

    if (
            $values['preferred_role'] !== ''
            && $values['second_choice'] !== ''
            && $values['preferred_role'] === $values['second_choice']
    ) {
        $errors[] = 'Le deuxième choix doit être différent du rôle préféré.';
    }

    if (mb_strlen($values['motivation']) > 800) {
        $errors[] = 'La motivation doit faire 800 caractères maximum.';
    }

    foreach (['programming_level', 'electronics_level', 'cad_level', 'science_level'] as $levelField) {
        if ($values[$levelField] !== '' && !in_array($values[$levelField], $experienceLevels, true)) {
            $errors[] = "Un niveau d'expérience choisi n'est pas valide.";
        }
    }

    foreach ($values['known_skills'] as $skill) {
        if (!in_array($skill, $skillOptions, true)) {
            $errors[] = "Une compétence choisie n'est pas valide.";
            break;
        }
    }

    if ($values['role_flexibility'] !== '' && !in_array($values['role_flexibility'], ['Oui', 'Peut-être', 'Non'], true)) {
        $errors[] = "La réponse sur la flexibilité de rôle n'est pas valide.";
    }

    foreach (['problem_solving', 'time_commitment'] as $shortAnswerField) {
        if (mb_strlen($values[$shortAnswerField]) > 600) {
            $errors[] = 'Les réponses courtes doivent faire 600 caractères maximum.';
            break;
        }
    }

    if ($values['availability'] === []) {
        $errors[] = 'Choisis au moins une disponibilité après les cours.';
    }

    foreach ($values['availability'] as $availability) {
        if (!in_array($availability, $availabilityOptions, true)) {
            $errors[] = "Une disponibilité choisie n'est pas valide.";
            break;
        }
    }

    if ($values['age'] !== '') {
        $age = filter_var($values['age'], FILTER_VALIDATE_INT);
        if ($age === false || $age < 16 || $age > 25) {
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
                        first_name, last_name, email, phone, telegram, discord, instagram, preferred_contact,
                        class, age, gender, preferred_role, second_choice,
                        motivation, programming_level, electronics_level, cad_level, science_level, known_skills,
                        problem_solving, role_flexibility,
                        programming_experience, electronics_experience, cad_experience,
                        science_experience, other_projects, availability, time_commitment, consent
                    ) VALUES (
                        :first_name, :last_name, :email, :phone, :telegram, :discord, :instagram, :preferred_contact,
                        :class, :age, :gender, :preferred_role, :second_choice,
                        :motivation, :programming_level, :electronics_level, :cad_level, :science_level, :known_skills,
                        :problem_solving, :role_flexibility,
                        :programming_experience, :electronics_experience, :cad_experience,
                        :science_experience, :other_projects, :availability, :time_commitment, :consent
                    )'
                );

                $statement->execute([
                        'first_name' => $values['first_name'],
                        'last_name' => $values['last_name'],
                        'email' => $values['email'],
                        'phone' => $values['phone'],
                        'telegram' => $values['telegram'],
                        'discord' => $values['discord'],
                        'instagram' => $values['instagram'],
                        'preferred_contact' => $values['preferred_contact'],
                        'class' => $values['class'] === 'Autre' ? $values['other_class'] : $values['class'],
                        'age' => (int)$values['age'],
                        'gender' => $values['gender'],
                        'preferred_role' => $values['preferred_role'],
                        'second_choice' => $values['second_choice'],
                        'motivation' => $values['motivation'],
                        'programming_level' => $values['programming_level'],
                        'electronics_level' => $values['electronics_level'],
                        'cad_level' => $values['cad_level'],
                        'science_level' => $values['science_level'],
                        'known_skills' => implode(', ', $values['known_skills']),
                        'problem_solving' => $values['problem_solving'],
                        'role_flexibility' => $values['role_flexibility'],
                        'programming_experience' => $values['programming_experience'],
                        'electronics_experience' => $values['electronics_experience'],
                        'cad_experience' => $values['cad_experience'],
                        'science_experience' => $values['science_experience'],
                        'other_projects' => $values['other_projects'],
                        'availability' => implode(', ', $values['availability']),
                        'time_commitment' => $values['time_commitment'],
                        'consent' => 1,
                ]);

                header('Location: success.php');
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
    <section>
        <h1>Postuler à l'équipe CanSat</h1>
        <p>
            Présente-toi, indique ce qui t'intéresse et explique pourquoi
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

    <form method="post" action="form.php">
        <hr>

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
            <div class="form-row">
                <label>
                    Classe
                    <select name="class" id="class-select" required>
                        <option value="">Choisir...</option>
                        <?php foreach ($classOptions as $classOption): ?>
                            <option value="<?= htmlspecialchars($classOption, ENT_QUOTES, 'UTF-8') ?>"<?= selected('class', $classOption) ?>><?= htmlspecialchars($classOption, ENT_QUOTES, 'UTF-8') ?></option>
                        <?php endforeach; ?>
                    </select>
                </label>

                <label>
                    Âge
                    <input type="number" name="age" min="16" max="25" value="<?= field('age') ?>" required>
                </label>

                <label>
                    Genre
                    <select name="gender" required>
                        <option value="">Choisir...</option>
                        <?php foreach ($genderOptions as $genderOption): ?>
                            <option value="<?= htmlspecialchars($genderOption, ENT_QUOTES, 'UTF-8') ?>"<?= selected('gender', $genderOption) ?>><?= htmlspecialchars($genderOption, ENT_QUOTES, 'UTF-8') ?></option>
                        <?php endforeach; ?>
                    </select>
                </label>

            </div>

            <label class="conditional-field" id="other-class-field">
                Classe précise
                <input type="text" name="other_class" id="other-class-input" value="<?= field('other_class') ?>"
                       placeholder="ex. 5e sciences appliquées" maxlength="50">
            </label>
            <div class="communication-fields">
                <label>
                    Moyen de contact préféré
                    <select name="preferred_contact" id="preferred-contact" required>
                        <option value="">Choisir...</option>
                        <?php foreach ($contactOptions as $value => $label): ?>
                            <option value="<?= htmlspecialchars($value, ENT_QUOTES, 'UTF-8') ?>"<?= selected('preferred_contact', $value) ?>><?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?></option>
                        <?php endforeach; ?>
                    </select>
                </label>

                <label class="conditional-field contact-method-field" data-contact-method="email">
                    E-mail
                    <input type="email" name="email" value="<?= field('email') ?>"
                           placeholder="E-mail : prenom.nom@ecole.be" maxlength="255">
                </label>

                <label class="conditional-field contact-method-field" data-contact-method="phone">
                    Téléphone
                    <input type="tel" name="phone" value="<?= field('phone') ?>"
                           placeholder="Téléphone : +32 470 12 34 56" maxlength="25" pattern="[0-9 +().-]{6,25}">
                </label>

                <label class="conditional-field contact-method-field" data-contact-method="telegram">
                    Telegram
                    <input type="text" name="telegram" value="<?= field('telegram') ?>"
                           placeholder="Telegram : @prenom_cansat" maxlength="100">
                </label>

                <label class="conditional-field contact-method-field" data-contact-method="discord">
                    Discord
                    <input type="text" name="discord" value="<?= field('discord') ?>"
                           placeholder="Discord : prenom.cansat" maxlength="100">
                </label>

                <label class="conditional-field contact-method-field" data-contact-method="instagram">
                    Instagram
                    <input type="text" name="instagram" value="<?= field('instagram') ?>"
                           placeholder="Instagram : @prenom_projet" maxlength="100">
                </label>
            </div>

        </fieldset>
        <hr>

        <fieldset>
            <legend>Intérêts</legend>

            <label>
                Rôle préféré
                <select name="preferred_role" id="preferred-role" required>
                    <option value="">Choisir...</option>
                    <?php foreach ($roleOptions as $role): ?>
                        <option value="<?= htmlspecialchars($role, ENT_QUOTES, 'UTF-8') ?>"<?= selected('preferred_role', $role) ?>><?= htmlspecialchars($role, ENT_QUOTES, 'UTF-8') ?></option>
                    <?php endforeach; ?>
                </select>
            </label>

            <label>
                Deuxième choix
                <select name="second_choice" id="second-choice">
                    <option value="">Aucun pour l'instant</option>
                    <?php foreach ($roleOptions as $role): ?>
                        <option value="<?= htmlspecialchars($role, ENT_QUOTES, 'UTF-8') ?>"<?= selected('second_choice', $role) ?>><?= htmlspecialchars($role, ENT_QUOTES, 'UTF-8') ?></option>
                    <?php endforeach; ?>
                </select>
            </label>
        </fieldset>

        <label>
            Pourquoi veux-tu rejoindre l'équipe ?
            <textarea name="motivation" rows="5" maxlength="800" required><?= field('motivation') ?></textarea>
        </label>
        <hr>

        <fieldset>
            <legend>Expérience</legend>

            <div class="experience-level-row">
                <label>
                    Niveau en programmation
                    <select name="programming_level">
                        <option value="">Choisir...</option>
                        <?php foreach ($experienceLevels as $level): ?>
                            <option value="<?= htmlspecialchars($level, ENT_QUOTES, 'UTF-8') ?>"<?= selected('programming_level', $level) ?>><?= htmlspecialchars($level, ENT_QUOTES, 'UTF-8') ?></option>
                        <?php endforeach; ?>
                    </select>
                </label>

                <label>
                    Niveau en électronique
                    <select name="electronics_level">
                        <option value="">Choisir...</option>
                        <?php foreach ($experienceLevels as $level): ?>
                            <option value="<?= htmlspecialchars($level, ENT_QUOTES, 'UTF-8') ?>"<?= selected('electronics_level', $level) ?>><?= htmlspecialchars($level, ENT_QUOTES, 'UTF-8') ?></option>
                        <?php endforeach; ?>
                    </select>
                </label>

                <label>
                    Niveau en CAD / 3D
                    <select name="cad_level">
                        <option value="">Choisir...</option>
                        <?php foreach ($experienceLevels as $level): ?>
                            <option value="<?= htmlspecialchars($level, ENT_QUOTES, 'UTF-8') ?>"<?= selected('cad_level', $level) ?>><?= htmlspecialchars($level, ENT_QUOTES, 'UTF-8') ?></option>
                        <?php endforeach; ?>
                    </select>
                </label>

                <label>
                    Niveau en sciences
                    <select name="science_level">
                        <option value="">Choisir...</option>
                        <?php foreach ($experienceLevels as $level): ?>
                            <option value="<?= htmlspecialchars($level, ENT_QUOTES, 'UTF-8') ?>"<?= selected('science_level', $level) ?>><?= htmlspecialchars($level, ENT_QUOTES, 'UTF-8') ?></option>
                        <?php endforeach; ?>
                    </select>
                </label>
            </div>

            <fieldset>
                <legend>Compétences déjà vues</legend>
                <div class="checkbox-grid">
                    <?php foreach ($skillOptions as $skill): ?>
                        <label>
                            <input type="checkbox" name="known_skills[]" value="<?= htmlspecialchars($skill, ENT_QUOTES, 'UTF-8') ?>"<?= checked('known_skills', $skill) ?>>
                            <?= htmlspecialchars($skill, ENT_QUOTES, 'UTF-8') ?>
                        </label>
                    <?php endforeach; ?>
                </div>
            </fieldset>

            <label>
                Si le CanSat ne transmet plus de données pendant un test, que vérifierais-tu en premier ? Décris ton raisonnement.
                <textarea name="problem_solving" rows="3" maxlength="600"><?= field('problem_solving') ?></textarea>
            </label>

            <fieldset>
                <legend>Flexibilité de rôle</legend>
                <label>
                    <input type="radio" name="role_flexibility" value="Oui"<?= checked('role_flexibility', 'Oui') ?>>
                    Oui, je peux aider dans un autre rôle si l'équipe en a besoin.
                </label>
                <label>
                    <input type="radio" name="role_flexibility" value="Peut-être"<?= checked('role_flexibility', 'Peut-être') ?>>
                    Peut-être, selon les besoins et ce que je peux apprendre.
                </label>
                <label>
                    <input type="radio" name="role_flexibility" value="Non"<?= checked('role_flexibility', 'Non') ?>>
                    Non, je préfère rester dans mon rôle principal.
                </label>
            </fieldset>

            <label>
                Programmation
                <textarea name="programming_experience" rows="3"
                          placeholder="Décris le langage, le type de projet, le matériel utilisé ou ce que ton programme devait faire."><?= field('programming_experience') ?></textarea>
            </label>

            <label>
                Électronique
                <textarea name="electronics_experience" rows="3"
                          placeholder="Décris les capteurs, circuits, câblages, soudures ou tests que tu as déjà faits."><?= field('electronics_experience') ?></textarea>
            </label>

            <label>
                CAD / 3D
                <textarea name="cad_experience" rows="3"
                          placeholder="Décris les pièces modélisées, logiciels utilisés, impressions 3D ou contraintes mécaniques."><?= field('cad_experience') ?></textarea>
            </label>

            <label>
                Sciences
                <textarea name="science_experience" rows="3"
                          placeholder="Décris ton expérience en physique, mesures, graphiques, analyse de données ou rédaction scientifique."><?= field('science_experience') ?></textarea>
            </label>

            <label>
                Projets réalisés
                <textarea name="other_projects" rows="3"
                          placeholder="Décris un ou deux projets dont tu es fier, même s'ils ne sont pas liés au CanSat."><?= field('other_projects') ?></textarea>
            </label>
        </fieldset>
        <hr>

        <fieldset>
            <legend>Disponibilités pour les réunions</legend>
            <div class="checkbox-grid">
                <?php foreach ($availabilityOptions as $availabilityOption): ?>
                    <label>
                        <input type="checkbox" name="availability[]" value="<?= htmlspecialchars($availabilityOption, ENT_QUOTES, 'UTF-8') ?>"<?= checked('availability', $availabilityOption) ?>>
                        <?= htmlspecialchars($availabilityOption, ENT_QUOTES, 'UTF-8') ?>
                    </label>
                <?php endforeach; ?>
            </div>
        </fieldset>

        <label>
            Combien de temps peux-tu consacrer au projet chaque semaine ?
            <textarea name="time_commitment" rows="3" maxlength="600" required
                      placeholder="Exemple : 1 réunion par semaine + 1 ou 2 heures à la maison quand il y a des tests."><?= field('time_commitment') ?></textarea>
        </label>

        <label>
            <input type="checkbox" name="consent" value="1" required>
            J'accepte que mes informations soient utilisées pour le recrutement de l'équipe CanSat.
        </label>

        <button type="submit">Envoyer la candidature</button>
    </form>


</main>

<script>
    const classSelect = document.querySelector('#class-select');
    const otherClassField = document.querySelector('#other-class-field');
    const otherClassInput = document.querySelector('#other-class-input');
    const preferredContactSelect = document.querySelector('#preferred-contact');
    const contactMethodFields = document.querySelectorAll('.contact-method-field');
    const preferredRoleSelect = document.querySelector('#preferred-role');
    const secondChoiceSelect = document.querySelector('#second-choice');

    function updateOtherClassField() {
        const isOther = classSelect.value === 'Autre';
        otherClassField.classList.toggle('is-visible', isOther);
        otherClassInput.required = isOther;
        otherClassInput.disabled = !isOther;

        if (!isOther) {
            otherClassInput.value = '';
        }
    }

    classSelect.addEventListener('change', updateOtherClassField);
    updateOtherClassField();

    function updateContactMethodField() {
        contactMethodFields.forEach((field) => {
            const input = field.querySelector('input');
            const isSelected = field.dataset.contactMethod === preferredContactSelect.value;

            field.classList.toggle('is-visible', isSelected);
            input.required = isSelected;
            input.disabled = !isSelected;

            if (!isSelected) {
                input.value = '';
            }
        });
    }

    preferredContactSelect.addEventListener('change', updateContactMethodField);
    updateContactMethodField();

    function validateRoleChoices() {
        const hasDuplicateRoles = preferredRoleSelect.value !== '' && preferredRoleSelect.value === secondChoiceSelect.value;
        secondChoiceSelect.setCustomValidity(hasDuplicateRoles ? 'Le deuxième choix doit être différent du rôle préféré.' : '');
    }

    preferredRoleSelect.addEventListener('change', validateRoleChoices);
    secondChoiceSelect.addEventListener('change', validateRoleChoices);
    validateRoleChoices();
</script>

<?php include_once __DIR__ . '/includes/footer.php'; ?>
