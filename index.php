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
                présenter un mini-satellite dans le cadre du concours CanSat.
            </p>
            <p>Les candidatures sont ouvertes jusqu'au 15 septembre 2026.</p>
            <a role="button" href="index.php#postuler">Postuler</a>
        </div>
        <img src="assets/img/cansat_hero.png" alt="Prototype CanSat">
    </section>


    <section id="a-propos">
        <h2>Qu'est-ce qu'un CanSat ?</h2>
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
            <article>
                <h3>Informations supplémentaires</h3>
                <ul>
                    <li><a href="https://en.wikipedia.org/wiki/CanSat">Wikipédia - CanSat</a></li>
                    <li>
                        <a href="https://www.esa.int/Education/CanSat/CanSat_2025-2026_Challenge_your_students_to_build_a_can-sized_satellite">ESA - CanSat</a>
                    </li>
                    <li>
                        <a href="https://recherche.wallonie.be/Cansat-Belgium">Wallonie Recherche - CanSat Belgium</a>
                    </li>
                </ul>
            </article>
        </div>
    </section>

    <section id="roles">
        <h2>Rôles</h2>
        <p>
            Nous cherchons des élèves motivés par la technique, la création et le
            travail d'équipe. Aucun rôle n'est figé : l'important est d'être motivé,
            d'apprendre et de contribuer régulièrement.
        </p>

        <div class="card-grid">
            <article><h3>Programmation</h3>
                <p>Code embarqué, lecture des capteurs, stockage des données, télémétrie et sécurité du système.</p>
            </article>
            <article><h3>Électronique</h3>
                <p>Câblage, alimentation, prototypage et intégration matérielle.</p></article>
            <article><h3>Mécanique</h3>
                <p>Structure 3D du CanSat, fixation des composants, parachute et stabilité.</p></article>
            <article><h3>Données</h3>
                <p>Graphiques, analyse scientifique et visualisation des résultats.</p></article>
            <article><h3>Tests et validation</h3>
                <p>Protocoles de test, essais de descente, portée radio, autonomie et fiabilité avant le lancement.</p>
            </article>
            <article><h3>Communication</h3>
                <p>Présentation, identité visuelle, documentation et réseaux sociaux.</p></article>
            <article><h3>Gestion de projet</h3>
                <p>Planning, coordination, suivi des tâches et préparation des livrables pour le concours.</p></article>
        </div>
    </section>

    <section id="activites">
        <h2>Activités et compétences</h2>
        <ul class="activity-list">
            <li>Réunions régulières chaque semaine</li>
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

    <section id="missions">
        <h2>Mission précédente du CanSat 2025-2026</h2>
        <p>
            Nous avons construit un CanSat fonctionnel avec une analyse complète des données de
            l'<span class="info-popover" tabindex="0">IMU<span class="info-popover-content" role="tooltip">Une IMU
                    (unité de mesure inertielle) mesure l'accélération, la rotation et l'orientation du
                    CanSat pendant le vol.</span></span> et un site web permettant de les visualiser. L'équipe a
            participé à la finale régionale belge et a obtenu la 2e place.
        </p>
    </section>
    <section id="gallery">
        <h2>Galerie</h2>

        <div class="gallery-layout">
            <figure class="mission-video">
                <div class="launch-gif-preview" tabindex="0" data-gif-src="assets/media/videos/launch.gif">
                    <img class="launch-poster" src="assets/media/videos/launch_poster.jpg" alt="Lancement du CanSat">
                    <img class="launch-gif" alt="" aria-hidden="true">
                </div>
                <figcaption>Lancement de la mission précédente</figcaption>
            </figure>

            <div class="media-grid">
                <img src="assets/media/photos/cansat_photo.jpg" alt="Prototype CanSat">
                <img src="assets/media/photos/preparation_military_base.jpg" alt="Préparation sur la base">
                <img src="assets/media/photos/starter_kit.jpg" alt="Kit de départ CanSat">
                <img src="assets/media/photos/start_photo.jpg" alt="Photo pendant le trajet vers Namur">
            </div>
        </div>
    </section>

    <script>
        document.querySelectorAll('.launch-gif-preview').forEach((preview) => {
            const gif = preview.querySelector('.launch-gif');
            const play = () => {
                gif.src = preview.dataset.gifSrc;
            };
            const stop = () => {
                gif.removeAttribute('src');
            };

            preview.addEventListener('mouseenter', play);
            preview.addEventListener('focus', play);
            preview.addEventListener('mouseleave', stop);
            preview.addEventListener('blur', stop);
        });
    </script>

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
        <details>
            <summary>Qui peut postuler ?</summary>
            <p>
                Les élèves à partir de la 4e secondaire, ayant 16 ans ou plus, peuvent postuler. Aucune expérience
                technique n'est obligatoire : la motivation et l'envie d'apprendre comptent le plus.
            </p>
        </details>
        <details>
            <summary>Qu'est-ce qu'on attend de moi ?</summary>
            <p>
                Nous attendons une participation régulière, curieuse et responsable. Il faudra essayer, proposer des
                idées, chercher des solutions et participer activement au travail de l'équipe.
            </p>
        </details>

        <small>Si tu as encore des questions, contacte-nous sans hésitation avec les informations en bas de
            page.</small>

    </section>

    <section class="final-cta" id="postuler">
        <h2>Prêt à rejoindre l'équipe ?</h2>
        <p>
            Les candidatures ferment dans <?= $daysRemaining ?> jour<?= $daysRemaining !== 1 ? 's' : '' ?>.
        </p>
        <a role="button" href="form.php">Postuler maintenant</a>
    </section>
</main>

<?php include_once 'includes/footer.php'; ?>
