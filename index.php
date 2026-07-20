<?php require 'includes/header.php'; ?>
<?php
$deadline = new DateTimeImmutable('2026-09-15');
$today = new DateTimeImmutable('today');
$daysRemaining = max(0, (int)$today->diff($deadline)->format('%r%a'));
?>
<main class="container">
    <section class="hero" id="accueil">
        <div>
            <p class="eyebrow">CanSat 2026-2027</p>
            <h1>Design. Build. Launch.</h1>
            <p>
                Rejoins l'équipe de l'école pour concevoir, construire, tester et
                présenter un mini-satellite pendant le concours CanSat.
            </p>
            <p>Les candidatures sont ouvertes jusqu'au 15 septembre.</p>
            <a role="button" href="index.php#postuler">Postuler</a>
        </div>
        <img src="assets/img/cansat_hero.png" alt="Prototype CanSat">
    </section>


    <section id="a-propos">
        <h2>Qu'est-ce que CanSat ?</h2>
        <p>
            Un CanSat est un système embarqué de la taille d'une canette. Il doit
            mesurer des données, les transmettre au sol et remplir une mission choisie
            par l'équipe.
        </p>
        <div class="card-grid">
            <article>
                <h3>Concours ESA</h3>
                <p>Un vrai projet d'ingénierie avec des contraintes, une mission et une présentation finale.</p>
            </article>
            <article>
                <h3>Mini-satellite</h3>
                <p>L'équipe assemble l'électronique, la structure, le logiciel et la télémétrie.</p>
            </article>
            <article>
                <h3>Lancement et analyse</h3>
                <p>Les données collectées servent à vérifier la mission et défendre les choix techniques.</p>
            </article>
        </div>
    </section>

    <section id="missions">
        <h2>Mission précédente</h2>
        <p>
            L'équipe précédente a construit un CanSat fonctionnel, participé à la
            finale régionale belge et obtenu la 2e place.
        </p>
    </section>

    <section id="roles">
        <h2>Rôles</h2>
        <p>
            Nous cherchons des élèves motivés par la technique, la création et le
            travail d'équipe. Aucun rôle n'est fermé : l'important est d'être motivé,
            d'apprendre et de contribuer régulièrement.
        </p>

        <div class="card-grid">
            <article><h3>Programmation</h3>
                <p>Capteurs, stockage des données et sécurité du système.</p></article>
            <article><h3>Électronique</h3>
                <p>Câblage, alimentation, prototypage et intégration matérielle.</p></article>
            <article><h3>Mécanique</h3>
                <p>Structure 3D, fixation des composants, parachute et stabilité.</p></article>
            <article><h3>Données</h3>
                <p>Graphiques, analyse scientifique et visualisation des résultats.</p></article>
            <article><h3>Communication</h3>
                <p>Présentation, identité visuelle, documentation et réseaux sociaux.</p></article>
            <article><h3>Gestion de projet</h3>
                <p>Planning, coordination, suivi des tests et préparation des livrables.</p></article>
        </div>
    </section>

    <section id="activites">
        <h2>Activités et compétences</h2>
        <ul class="activity-list">
            <li>Réunions régulières après les cours</li>
            <li>Programmation et tests des capteurs</li>
            <li>Prototypage électronique</li>
            <li>Modélisation et impression 3D</li>
            <li>Tests de stabilité, de transmission et de descente</li>
            <li>Présentations techniques et documentation</li>
        </ul>
    </section>

    <section id="calendrier">
        <h2>Calendrier</h2>
        <ol class="timeline">
            <li><strong>Septembre</strong><span>Candidatures et formation de l'équipe.</span></li>
            <li><strong>Octobre</strong><span>Choix de mission, premiers schémas et répartition des rôles.</span></li>
            <li><strong>Hiver</strong><span>Prototype, programmation, intégration et premiers tests.</span></li>
            <li>
                <strong>Printemps</strong><span>Version finale, validation, présentation et préparation du concours.</span>
            </li>
            <li><strong>Compétition</strong><span>Lancement, analyse des données et défense du projet.</span></li>
        </ol>
    </section>

    <section id="gallery">
        <h2>Galerie</h2>

        <div class="media-grid">
            <img src="assets/media/photos/cansat_photo.jpg" alt="Prototype CanSat">
            <img src="assets/media/photos/preparation_military_base.jpg" alt="Préparation sur la base">
            <img src="assets/media/photos/starter_kit.jpg" alt="Kit de départ CanSat">
        </div>

        <figure class="mission-video">
            <video controls preload="metadata">
                <source src="assets/media/videos/launch_video.mp4" type="video/mp4">
                Votre navigateur ne peut pas lire cette vidéo.
            </video>
            <figcaption>Lancement de la mission précédente</figcaption>
        </figure>
    </section>

    <section id="faq">
        <h2>FAQ</h2>
        <details>
            <summary>Faut-il déjà savoir programmer ou faire de l'électronique ?</summary>
            <p>
                Non. Des bases sont utiles, mais la motivation et la régularité sont
                plus importantes. Le projet sert aussi à apprendre.
            </p>
        </details>

        <details>
            <summary>Combien de temps faut-il prévoir ?</summary>
            <p>
                Il faudra travailler régulièrement, parfois en dehors des heures de
                cours, surtout pendant les phases de test.
            </p>
        </details>

        <details>
            <summary>Est-ce que je dois choisir un seul rôle ?</summary>
            <p>
                Non. Les rôles aident à organiser l'équipe, mais chaque élève peut
                découvrir plusieurs parties du projet.
            </p>
        </details>

        <small>Si tu as encore des questions, contacte-nous sans hesitation avec les informations en bas de page.</small>

    </section>

    <section class="final-cta" id="postuler">
        <h2>Prêt à rejoindre l'équipe ?</h2>
        <p>
            Les candidatures ferment dans <?= $daysRemaining ?> jour<?= $daysRemaining > 1 ? 's' : '' ?>.
        </p>
        <a role="button" href="form.php">Postuler maintenant</a>
    </section>
</main>

<?php include_once 'includes/footer.php'; ?>
