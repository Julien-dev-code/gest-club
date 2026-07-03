<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: connexion.php');
    exit;
}

require_once __DIR__ .'/includes/db.php';
require_once __DIR__ .'/includes/helpers.php';



$sql = "SELECT 
    evenement.id,
    evenement.nom AS nom_evenement,
    evenement.statut,
    evenement.date_debut,
    evenement.date_fin,
    type_evenement.nom AS nom_type,
    (SELECT COUNT(*)
     FROM reservation_place
     JOIN reservation ON reservation_place.id_reservation = reservation.id
     WHERE reservation.id_evenement = evenement.id
    ) AS places_prises
FROM evenement
JOIN type_evenement ON evenement.id_type_evenement = type_evenement.id
ORDER BY evenement.date_debut ASC";

try {
    $requete = $pdo->prepare($sql);
    $requete->execute();
    $evenements = $requete->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $evenements = [];
    
}


$formatteur = new IntlDateFormatter(
    'fr_FR',
    IntlDateFormatter::NONE,
    IntlDateFormatter::NONE,
    null,
    null,
    "EEE d MMM y — HH'h'mm"
);

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Événements</title>
    <link rel="stylesheet" href="styles/variables.css">
    <link rel="stylesheet" href="styles/base.css">
    <link rel="stylesheet" href="styles/components.css">
    <link rel="stylesheet" href="styles/pages/evenements.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <script src="https://code.iconify.design/iconify-icon/1.0.7/iconify-icon.min.js"></script>
    <script src="js/main.js"defer></script>
</head>
<body>
    

        <?php require_once 'includes/header.php'; ?>


    <main>
        <div class="main__container"> 
            <div class="section__header">
                <p class="section__eyebrow">CALENDRIER</p>
                <h2 class="section__title">Événements à venir</h2>
                <p class="section__subtitle">Réservez vos places avant qu'il ne soit trop tard</p>
            </div>
        
            <div class="events">
                <?php foreach ($evenements as $evenement) : 
                    
                    $aujourdhui = new DateTime();
                    $date_evenement = new DateTime($evenement['date_debut']);
                    $difference = $aujourdhui->diff($date_evenement);

                    $statut_affiche = calculer_statut_affiche($evenement);

                ?>

                <div class="card">
                    <?php if ($statut_affiche === 'a_venir') : ?>
                        <span class="badge--upcoming">
                            <i class="bx bxs-circle"></i>A venir
                        </span>
                    <?php elseif ($statut_affiche === 'en_cours') : ?>
                        <span class="badge--active">
                            <i class="bx bxs-circle"></i>En cours
                        </span>
                    <?php elseif ($statut_affiche === 'complet') : ?>
                        <span class="badge--full">
                            <i class="bx bxs-circle"></i>Complet
                        </span>
                    <?php elseif ($statut_affiche === 'termine') : ?>
                        <span class="badge--finished">
                            <i class="bx bxs-circle"></i>Terminé
                        </span>
                    <?php elseif ($statut_affiche === 'annule') : ?>
                        <span class="badge--full">
                            <i class="bx bxs-circle"></i>Annulé
                        </span>
                    <?php endif; ?>

                    <h3 class="card__title"><?= htmlspecialchars($evenement['nom_evenement'])?> </h3>
                    <p class="card__date"><?= ucfirst($formatteur->format(new DateTime($evenement['date_debut']))) ?></p>
                        
                    <div class="card__stats">
                        <div class="card__stat">
                            <p class="card__stat-label">Places dispo</p>
                            <span class="card__stat-value"><?= CAPACITE_STADE - $evenement['places_prises']?></span>
                        </div>
                        
                        <div class="card__stat">
                            <p class="card__stat-label">Capacité</p>
                            <span class="card__stat-value card__stat-value--dark"><?= CAPACITE_STADE ?> </span>
                        </div>
                
                        <div class="card__stat">
                            <p class="card__stat-label">Compte à rebours</p>
                            <span class="card__stat-value">J-<?= ($difference->days) ?></span>
                        </div>
                    </div>
                    
                    <?php if ($statut_affiche === 'a_venir') : ?>
                        <a href="reservation.php" class="btn--primary">Réserver ma place</a>

                    <?php elseif ($statut_affiche === 'en_cours') : ?>
                        <a href="reservation.php" class="btn--primary">Réserver ma place</a>

                    <?php elseif ($statut_affiche === 'complet') : ?>
                        <button class="btn--danger btn--disabled" disabled>Complet</button>

                    <?php elseif ($statut_affiche === 'termine') : ?>
                        <button class="btn--disabled" disabled>Terminé</button>
                        
                    <?php elseif ($statut_affiche === 'annule') : ?>
                        <button class="btn--disabled btn--danger btn--full" disabled>Annulé</button>
                    <?php endif; ?>
                </div> 

                <?php endforeach; ?>

            </div>
        </div>
    </main>

    <footer class="footer">
        <span class="footer__brand">GEST<span>CLUB</span></span>
        <p class="footer__copy">GestClub 2026 - Tout droit réservés</p>
    </footer>

</body>
</html>