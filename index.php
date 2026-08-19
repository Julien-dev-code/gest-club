<?php

require_once __DIR__ . '/includes/db.php';
require_once __DIR__ .'/includes/helpers.php';

session_start();

$sql_evenements = "SELECT
    evenement.id,
    evenement.nom AS nom_evenement,
    evenement.statut,
    evenement.date_debut,
    evenement.date_fin,
    (SELECT COUNT(*)
     FROM reservation_place
     JOIN reservation ON reservation_place.id_reservation = reservation.id
     WHERE reservation.id_evenement = evenement.id
    ) AS places_prises
FROM evenement
WHERE evenement.statut <> 'annule'
  AND evenement.date_fin > NOW()
ORDER BY evenement.date_debut ASC
LIMIT 3";

try {
    $requete_evenements = $pdo->prepare($sql_evenements);
    $requete_evenements->execute();
    $evenements = $requete_evenements->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log($e->getMessage());
    $evenements = [];
}

$formatteur = new IntlDateFormatter(
    'fr_FR',
    IntlDateFormatter::NONE,
    IntlDateFormatter::NONE,
    null,
    null,
    "EEEE d MMMM y — HH'h'mm"
);

?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accueil - GEST CLUB</title>
    <link rel="stylesheet" href="styles/variables.css">
    <link rel="stylesheet" href="styles/base.css">
    <link rel="stylesheet" href="styles/components.css">
    <link rel="stylesheet" href="styles/pages/accueil.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <script src="https://code.iconify.design/iconify-icon/1.0.7/iconify-icon.min.js"></script>
    <script src="js/main.js"defer></script>
</head>
<body>
  
        <?php require_once 'includes/header.php'; ?>
    
  <main>
        
        <div class="hero">
          <img class="hero__image" src="assets/images/gabriele-fenili-7MF6_YwHJA8-unsplash.jpg" alt="">
            
            <div class="hero__content">
              <h1 class="hero__title">L'arène<br><span class="hero__title--accent">vous attend.</span></h1>
              <p class="hero__subtitle">Réservez vos places, retrouvez vos amis dans les tribunes et vivez chaque match comme jamais...</p>
              <a href="inscription.php" class="btn--primary">Créer mon compte</a>
            </div>
        </div>

        <div class="main__container"> 
          <div class="section__header">
              <p class="section__eyebrow">Calendrier</p>
              <h2 class="section__title">Événements à venir</h2>
              <p class="section__subtitle">Réservez vos places avant qu'il ne soit trop tard</p>
          </div>

          <div class="events">
            <?php foreach ($evenements as $evenement) :

              $statut_affiche = calculer_statut_affiche($evenement);

              $aujourdhui = new DateTime();
              $date_evenement = new DateTime($evenement['date_debut']);
              $difference = $aujourdhui->diff($date_evenement);
            ?>

            <div class="card">
              <?php if ($statut_affiche === 'en_cours') : ?>
                  <span class="badge--active">
                      <i class="bx bxs-circle"></i>En cours
                  </span>
              <?php elseif ($statut_affiche === 'complet') : ?>
                  <span class="badge--full">
                      <i class="bx bxs-circle"></i>Complet
                  </span>
              <?php else : ?>
                  <span class="badge--upcoming">
                      <i class="bx bxs-circle"></i>A venir
                  </span>
              <?php endif; ?>

              <h3 class="card__title"><?= htmlspecialchars($evenement['nom_evenement']) ?></h3>
              <p class="card__date"><?= ucfirst($formatteur->format(new DateTime($evenement['date_debut']))) ?></p>

              <div class="card__stats">
                <div class="card__stat">
                  <p class="card__stat-label">Places dispo</p>
                  <span class="card__stat-value"><?= CAPACITE_STADE - $evenement['places_prises'] ?></span>
                </div>

                <div class="card__stat">
                  <p class="card__stat-label">Capacité</p>
                  <span class="card__stat-value card__stat-value--dark"><?= CAPACITE_STADE ?></span>
                </div>

                <?php if ($statut_affiche === 'a_venir' || $statut_affiche === 'complet') : ?>
                <div class="card__stat">
                  <p class="card__stat-label">Compte à rebours</p>
                  <span class="card__stat-value">J-<?= $difference->days ?></span>
                </div>
                <?php endif; ?>
            </div>

            <?php if ($statut_affiche === 'complet') : ?>
                <button class="btn--danger btn--disabled" disabled>Complet</button>
            <?php else : ?>
                <a href="connexion.php" class="btn--primary">Réserver ma place</a>
            <?php endif; ?>
        </div>

        <?php endforeach; ?>
      </div>

            <div class="section__header">
                <p class="section__eyebrow">FONCTIONNALITÉS</p>
                <h2 class="section__title">Tout ce dont vous avez besoin </h2>
                <p class="section__subtitle">Une plateforme complète pour vivre le sport autrement</p>
            </div>

            <div class="features">
              <div class="feature">
                <div class="feature__icon">
                  <iconify-icon icon="emojione:admission-tickets"></iconify-icon>
                </div>
                <div class="feature__content">
                  <h3 class="feature__title">Réservation de places</h3>
                  <p class="feature__description">Choisissez votre tribune, votre niveau et votre siège numéroté. Jusqu'à 2 places par événement.</p>
                </div>
              </div>

              <div class="feature">
                <div class="feature__icon">
                  <iconify-icon icon="emojione:mobile-phone"></iconify-icon>
                </div>
                <div class="feature__content">
                  <h3 class="feature__title">QR Code d'entrée</h3>
                  <p class="feature__description">Votre billet numérique scannable directement à l'entrée du stade. Rapide et sans contact.</p>
                </div>
              </div>

              <div class="feature">
                <div class="feature__icon">
                  <iconify-icon icon="fluent-color:people-community-28"></iconify-icon>
                </div>
                <div class="feature__content">
                  <h3 class="feature__title">Amis & Communauté</h3>
                  <p class="feature__description">Retrouvez vos amis dans le stade et suivez leurs réservations selon leur profil de confidentialité.</p>
                </div>
              </div>

              <div class="feature">
                <div class="feature__icon">
                  <iconify-icon icon="fluent-emoji-flat:star"></iconify-icon>
                </div>
                <div class="feature__content">
                  <h3 class="feature__title">Votes & Notations</h3>
                  <p class="feature__description">Votez pour le meilleur athlète pendant les événements et notez les matchs.</p>
                </div>
              </div>

              <div class="feature">
                <div class="feature__icon">
                  <iconify-icon icon="twemoji:bar-chart"></iconify-icon>
                </div>
                <div class="feature__content">
                  <h3 class="feature__title">Dashboard en temps réel</h3>
                  <p class="feature__description">Suivez les présences, les places disponibles et les statistiques de chaque événement.</p>
                </div>
              </div>

              <div class="feature">
                <div class="feature__icon">
                  <iconify-icon icon="fxemoji:lock"></iconify-icon>
                </div>
                <div class="feature__content">
                  <h3 class="feature__title">Profil & Confidentialité</h3>
                  <p class="feature__description">Gérez la visibilité de vos réservations : public, privé ou fermé selon vos préférences.</p>
                </div>
              </div>
            </div>
        

          <section class="cta">
            <div class="cta__container">
              <div class="cta__content">
                <p class="cta__eyebrow">REJOIGNEZ-NOUS</p>
                <h2 class="cta__title">Prêt à vivre l'expérience ?</h2>
                <p class="cta__description">Créez votre compte gratuitement et réservez votre première place en moins de 2 minutes.</p>
              </div>
              <div class="cta__btn">
                <a href="inscription.php" class="btn--dark">Créer mon compte</a>
              </div>
            </div>
          </section>
        </div> 
        
    </main>

    <footer class="footer">
      <span class="footer__brand">GEST<span>CLUB</span></span>
      <p class="footer__copy">GestClub 2026 - Tout droit réservés</p>
    </footer>
</body>
</html>

