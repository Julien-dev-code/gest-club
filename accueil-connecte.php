<?php

require_once __DIR__ . '/includes/db.php';
require_once __DIR__ .'/includes/helpers.php';

session_start();


if (!isset($_SESSION['user_id'])) {
    header('Location: connexion.php');
    exit;
}

$sql_reservations = "SELECT
    reservation.id,
    evenement.id AS id_evenement,
    evenement.nom AS nom_evenement,
    evenement.statut,
    evenement.date_debut,
    evenement.date_fin,
    tribune.nom AS nom_tribune,
    niveau.nom AS nom_niveau,
    GROUP_CONCAT(place.numero ORDER BY place.numero SEPARATOR ', ') AS numeros_places
FROM reservation
JOIN evenement          ON reservation.id_evenement = evenement.id
JOIN reservation_place  ON reservation_place.id_reservation = reservation.id
JOIN place              ON reservation_place.id_place = place.id
JOIN tribune            ON place.id_tribune = tribune.id
JOIN niveau             ON place.id_niveau = niveau.id
WHERE reservation.id_utilisateur = :id_utilisateur
GROUP BY reservation.id,
         evenement.id,
         evenement.nom,
         evenement.statut,
         evenement.date_debut,
         evenement.date_fin,
         tribune.nom,
         niveau.nom
ORDER BY evenement.date_debut ASC";

try {
    $requete_reservations = $pdo->prepare($sql_reservations);
    $requete_reservations->execute(['id_utilisateur' => $_SESSION['user_id']]);
    $mes_reservations = $requete_reservations->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log($e->getMessage());
    $mes_reservations = [];
}

$reservations_par_evenement = [];
foreach ($mes_reservations as $reservation) {
    $reservations_par_evenement[$reservation['id_evenement']] = $reservation['id'];
}

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
    <title>Accueil - GestClub</title>
    <link rel="stylesheet" href="styles/variables.css">
    <link rel="stylesheet" href="styles/base.css">
    <link rel="stylesheet" href="styles/components.css">
    <link rel="stylesheet" href="styles/pages/accueil.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <script src="https://code.iconify.design/iconify-icon/1.0.7/iconify-icon.min.js"></script>
    <script src="js/main.js"defer></script>
    <script src="js/modals.js" defer></script>
</head>
<body>

    <?php require_once 'includes/header.php'; ?>

    <main>

        <div class="hero">
            <img class="hero__image" src="assets/images/gabriele-fenili-7MF6_YwHJA8-unsplash.jpg" alt="">
              
            <div class="hero__content">
                <h1 class="hero__title">Bonjour <span class="hero__title--prenom"><?= htmlspecialchars($_SESSION['prenom'])  ?></span>,<br><span class="hero__title--accent">Bienvenue dans l'arène.</span></h1>
                <p class="hero__subtitle">Réservez vos places, retrouvez vos amis dans les tribunes et vivez chaque match comme jamais...</p>
                <a href="evenements.php" class="btn--primary">Voir les événements</a>
            </div>
        </div>

        <div class="main__container"> 

             <?php afficher_flashes(); ?>

            <div class="section__header">
                <p class="section__eyebrow">MES BILLETS</p>
                <h2 class="section__title">Mes réservations</h2>
                <p class="section__subtitle">
                    <?= count($mes_reservations) ?>
                    billet<?= count($mes_reservations) > 1 ? 's' : '' ?> actif<?= count($mes_reservations) > 1 ? 's' : '' ?>
                </p>
            </div>

            <?php if (empty($mes_reservations)) : ?>

                <div class="alert alert--info">
                    <i class="bx bxs-info-circle"></i>
                    Vous n'avez encore aucune réservation. Choisissez un événement ci-dessous pour réserver votre place.
                </div>

            <?php else : ?>

                <div class="reservations">
                    <?php foreach ($mes_reservations as $reservation) :

                        $evenement_du_billet = $reservation;
                        $evenement_du_billet['places_prises'] = 0;
                        $statut_billet = calculer_statut_affiche($evenement_du_billet);

                        $aujourdhui = new DateTime();
                        $date_evenement = new DateTime($reservation['date_debut']);
                        $difference = $aujourdhui->diff($date_evenement);
                    ?>

                    <div class="card">
                        <?php if ($statut_billet === 'annule') : ?>
                            <span class="badge--full">
                                <i class="bx bxs-circle"></i>Annulé
                            </span>
                        <?php elseif ($statut_billet === 'termine') : ?>
                            <span class="badge--finished">
                                <i class="bx bxs-circle"></i>Terminé
                            </span>
                        <?php elseif ($statut_billet === 'en_cours') : ?>
                            <span class="badge--active">
                                <i class="bx bxs-circle"></i>En cours
                            </span>
                        <?php else : ?>
                            <span class="badge--upcoming">
                                <i class="bx bxs-circle"></i>A venir
                            </span>
                        <?php endif; ?>

                        <h3 class="card__title"><?= htmlspecialchars($reservation['nom_evenement']) ?></h3>
                        <p class="card__date"><?= ucfirst($formatteur->format(new DateTime($reservation['date_debut']))) ?></p>

                        <div class="card__stats--reservation">
                            <div class="card__stat">
                                <p class="card__stat-label">Tribune</p>
                                <span class="card__stat-value"><?= ucfirst(htmlspecialchars($reservation['nom_tribune'])) ?></span>
                            </div>

                            <div class="card__stat">
                                <p class="card__stat-label">Niveau</p>
                                <span class="card__stat-value card__stat-value--dark"><?= ucfirst(htmlspecialchars($reservation['nom_niveau'])) ?></span>
                            </div>

                            <div class="card__stat">
                                <p class="card__stat-label">Place<?= str_contains($reservation['numeros_places'], ',') ? 's' : '' ?></p>
                                <span class="card__stat-value card__stat-value--dark">N°<?= htmlspecialchars($reservation['numeros_places']) ?></span>
                            </div>

                            <?php if ($statut_billet === 'a_venir') : ?>
                            <div class="card__stat">
                                <p class="card__stat-label">Compte à rebours</p>
                                <span class="card__stat-value">J-<?= $difference->days ?></span>
                            </div>
                            <?php endif; ?>
                        </div>

                        <div class="card__actions">
                            <a href="qrcode.php?id=<?= $reservation['id'] ?>" class="btn--success btn--full">Voir mon billet</a>

                            <?php if ($statut_billet === 'a_venir') : ?>
                                <button type="button"
                                        class="btn--danger btn--delete"
                                        aria-label="Annuler cette réservation">
                                    <i class='bx bx-trash'></i>
                                </button>
                            <?php else : ?>
                                <button type="button"
                                        class="btn--danger btn--disabled"
                                        disabled
                                        aria-label="Annulation impossible">
                                    <i class='bx bx-trash'></i>
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>

                    <?php if ($statut_billet === 'a_venir') : ?>
                    <div class="modal-wrapper">
                        <div class="modal-wrapper__content">
                            <button class="modal-button-close" type="button" aria-label="Fermer">
                                <i class='bx bx-x'></i>
                            </button>

                            <div class="confirmation__header" tabindex="-1">
                                <span class="badge--error">
                                    <i class="bx bxs-error-circle"></i>Action irréversible
                                </span>
                                <h2 class="confirmation__header-title">Annuler la réservation ?</h2>
                                <p class="confirmation__header-subtitle">
                                    Vos places seront remises à disposition et vos billets définitivement supprimés.
                                </p>
                            </div>

                            <div class="suppression__recap">
                                <p class="suppression__recap-title"><?= htmlspecialchars($reservation['nom_evenement']) ?></p>
                                <p class="suppression__recap-date"><?= ucfirst($formatteur->format(new DateTime($reservation['date_debut']))) ?></p>

                                <div class="suppression__recap-stats">
                                    <div class="card__stat">
                                        <p class="card__stat-label">Tribune</p>
                                        <span class="card__stat-value"><?= ucfirst(htmlspecialchars($reservation['nom_tribune'])) ?></span>
                                    </div>
                                    <div class="card__stat">
                                        <p class="card__stat-label">Niveau</p>
                                        <span class="card__stat-value card__stat-value--dark"><?= ucfirst(htmlspecialchars($reservation['nom_niveau'])) ?></span>
                                    </div>
                                    <div class="card__stat">
                                        <p class="card__stat-label">Places</p>
                                        <span class="card__stat-value card__stat-value--dark">N°<?= htmlspecialchars($reservation['numeros_places']) ?></span>
                                    </div>
                                </div>
                            </div>

                            <form action="traitements/traitement_suppression_reservation.php" method="POST" class="suppression__form">
                                <input type="hidden" name="id_reservation" value="<?= $reservation['id'] ?>">
                                <button type="submit" class="btn--danger btn--full">Oui, annuler ma réservation</button>
                                <button type="button" class="btn--ghost btn--annuler">Non, conserver mon billet</button>
                            </form>
                        </div>
                    </div>
                    <?php endif; ?>

                    <?php endforeach; ?>
                </div>

            <?php endif; ?>

            <div class="section__header">
                <p class="section__eyebrow">CALENDRIER</p>
                <h2 class="section__title">Événements à venir</h2>
                <p class="section__subtitle">Réservez vos places avant qu'il ne soit trop tard</p>
            </div>
        
            <div class="events">
                <?php foreach ($evenements as $evenement) :

                    $statut_affiche = calculer_statut_affiche($evenement);

                    $aujourdhui = new DateTime();
                    $date_evenement = new DateTime($evenement['date_debut']);
                    $difference = $aujourdhui->diff($date_evenement);

                    $id_reservation_existante = $reservations_par_evenement[$evenement['id']] ?? null;
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

                    <?php if ($id_reservation_existante !== null) : ?>
                        <a href="qrcode.php?id=<?= $id_reservation_existante ?>" class="btn--success btn--full">Voir mon billet</a>

                    <?php elseif ($statut_affiche === 'complet') : ?>
                        <button class="btn--danger btn--disabled" disabled>Complet</button>

                    <?php else : ?>
                        <a href="reservation.php?id=<?= $evenement['id'] ?>" class="btn--primary">Réserver ma place</a>
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