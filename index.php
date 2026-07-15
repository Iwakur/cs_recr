<?php include_once 'includes/header.php'; ?>
<main>
    <section class="hero" id="accueil">
        <h1>CanSat 2026-2027</h1>
        <p>
            Nous recrutons une équipe de six élèves pour participer au concours CanSat.
            Le projet consiste à concevoir, construire et tester un mini-satellite.
        </p>
        <p>Les candidatures sont ouvertes jusqu'au 15 septembre.</p>  <!--- Implememnt dealine countdown for 15 semptember  -->
        <a class="button" href="#postuler">Postuler</a>
    </section>


    <section id="a-propos">
        <h2>À propos de CanSat</h2>
        <p>
            Un CanSat est un système embarqué de la taille d'une canette. Il doit
            mesurer des données, les transmettre au sol et remplir une mission choisie
            par l'équipe.
        </p>
        <p>
            Notre objectif est de créer un projet fiable, original et bien présenté,
            puis de le défendre pendant le concours. <!-- add links to official resources-->
        </p>
    </section>

    <section id="missions">
        <h2>Mission précédente</h2>
        <p>
            • Built and launched a functioning CanSat
            • Competed in the Belgian regional finals
            • Achieved 2nd place
        </p>
    </section>
    <section id="gallery">
        <h2>Galerie</h2>

        <div class="gallery-group">
            <h3>Photos</h3>
            <div class="media-grid">
                <img src="/assets/media/photos/cansat_photo.jpg" alt="Prototype CanSat">
                <img src="/assets/media/photos/preparation_military_base.jpg" alt="Préparation sur la base">
                <img src="/assets/media/photos/starter_kit.jpg" alt="Kit de départ CanSat">
            </div>
        </div>

        <div class="gallery-group">
            <h3>Vidéos</h3>
            <div class="media-grid video-grid">
                <figure>
                    <video controls preload="metadata">
                        <source src="/assets/media/videos/launch_video.mp4" type="video/mp4">
                        Votre navigateur ne peut pas lire cette vidéo.
                    </video>
                    <figcaption>Lancement</figcaption>
                </figure>
            </div>
        </div>
    </section>

    <section id="roles">
        <h2>Rôles</h2>
        <p>
            Nous cherchons des élèves motivés par la technique, la création et le
            travail d'équipe. Aucun rôle n'est fermé : l'important est d'apprendre
            et de contribuer régulièrement. + etre motive
        </p>

        <ul>
            <li><strong>Programmation embarquée :</strong> capteurs, stockage des données, sécurité du système.</li>
            <li><strong>Station au sol :</strong> réception de la télémétrie, base de données, communication radio.</li>
            <li><strong>Électronique :</strong> câblage, alimentation, prototypage, intégration matérielle.</li>
            <li><strong>Mécanique :</strong> structure 3D, fixation des composants, parachute et stabilité.</li>
            <li><strong>Données :</strong> graphiques, analyse scientifique, visualisation des résultats.</li>
            <li><strong>Communication :</strong> présentation, réseaux sociaux, identité visuelle, documentation.</li>
        </ul>
    </section>

    <section id="calendrier">
        <h2>Calendrier</h2>
        <p>
            Les grandes étapes seront la formation de l'équipe, la conception, le
            prototypage, les tests, la préparation du lancement et la présentation finale.
        </p>
        <p>
            Le projet demandera du travail en dehors des cours, avec des réunions et
            des séances de test à l'école de manière régulière.
        </p>
    </section>

    <section id="faq">
        <h2>FAQ</h2>
        <p>
            Quelques réponses rapides avant de postuler.
        </p>

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
        <p>If u still have questions, hesitate, not sure, or have any other question, please dont hesitate and contact us through contacts below</p>

    </section>

    <section id="postuler">
        <h2>Postuler</h2>
        <p>
            Présente-toi, explique ce que tu veux apprendre et indique le rôle qui
            t'intéresse le plus.
        </p>
        <p> Don't forget to indicate ur cridantials so we cann reach to u later</p>
        <a class="button" href="form.php">Envoyer une candidature</a>
    </section>
</main>

<?php include_once 'includes/footer.php'; ?>
