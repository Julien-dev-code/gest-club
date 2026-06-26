<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: connexion.php');
    exit;
}
$retour_texte = "Retour aux événements";
$retour_url = "evenements.php";

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reservation</title>
    <link rel="stylesheet" href="styles/variables.css">
    <link rel="stylesheet" href="styles/base.css">
    <link rel="stylesheet" href="styles/components.css">
    <link rel="stylesheet" href="styles/pages/reservations.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <script src="https://code.iconify.design/iconify-icon/1.0.7/iconify-icon.min.js"></script>
    <script src="js/modals.js"defer></script>
    <script src="js/pages/reservation.js"defer></script>
</head>
<body>
    
<?php require_once 'includes/header-simple.php'; ?>


    <main>
        <div class="section__header">
            <p class="section__eyebrow">RÉSERVATION</p>
            <h1 class="section__title">Choisissez votre place</h1>
            <p class="section__subtitle">Finale du championnat — Samedi 15 mars 2026 à 20h00</p>
        </div>
        <div class="main__container">
            <div class="alert alert--info">
                <i class='bx bx-info-circle'></i>
                <div class="alert__content">
                    <p>Maximum 2 places par événement</p>
                    <p>Vous n'avez encore aucune réservation pour cette date.</p>
                </div>
            </div>
        </div>

        <form action="" method="post" class="reservation__form">  
            <div class="reservation__step">
                <h2 class="reservation__step-title">
                    <span class="reservation__step-number">1.</span> 
                    Choisissez votre tribune
                </h2>
                <div class="tribune-grid">
                    <button type="button" class="tribune-option tribune-option--selected">
                        <div>
                            <iconify-icon icon="noto-v1:stadium"></iconify-icon>
                            <span class="tribune-option__name">Nord</span>
                        </div>
                        <span class="tribune-option__places">38 places dispo</span>
                    </button>
                    <button type="button" class="tribune-option">
                        <div>
                            <iconify-icon icon="noto-v1:stadium"></iconify-icon>
                            <span class="tribune-option__name">Sud</span>
                        </div>
                        <span class="tribune-option__places">52 places dispo</span>
                    </button>
                    <button type="button" class="tribune-option">
                        <div>
                            <iconify-icon icon="noto-v1:stadium"></iconify-icon>
                            <span class="tribune-option__name">Est</span>
                        </div>
                        <span class="tribune-option__places">24 places dispo</span>
                    </button>
                    <button type="button" class="tribune-option">
                        <div>
                            <iconify-icon icon="noto-v1:stadium"></iconify-icon>
                            <span class="tribune-option__name">Ouest</span>
                        </div>
                        <span class="tribune-option__places">28 places dispo</span>
                    </button>
                </div>
            </div>

            <div class="reservation__step">
                <h2 class="reservation__step-title">
                    <span class="reservation__step-number">2.</span> 
                    Choisissez votre niveau
                </h2>
                <div class="niveau-grid">
                    <button type="button" class="niveau-option niveau-option--selected">Haut</button>
                    <button type="button" class="niveau-option">Milieu</button>
                    <button type="button" class="niveau-option">Bas</button>
                </div>
            </div>

            <div class="reservation__step">
                <h2 class="reservation__step-title">
                    <span class="reservation__step-number">3.</span> 
                    Nombre de places
                </h2>
                <div class="places-counter">
                    <div class="places-counter__controls">
                        <button type="button" class="places-counter__btn--minus">-</button>
                        <span class="places-counter__value">1</span>
                        <button type="button" class="places-counter__btn--plus">+</button>
                    </div>
                    <p class="places-counter__max">maximum : <strong>2 places</strong></p>
                </div>
            </div>
            <div class="form__footer">
                <button type="button" class="btn--primary btn--confirm">Confirmer la réservation</button>
                <span>Un QR code vous sera généré après confirmation</span>
            </div>
        </form>  
    </main>

    <div id="modal-confirm" class="modal-wrapper">
        <div class="modal-wrapper__content">
            <button class="modal-button-close">
                <i class='bx  bx-x'></i>
            </button>
            
            <div class="section__header">
                <p class="section__eyebrow">CONFIRMATION</p>
                <h2 class="section__title">Finale du championnat</h2>
                <p class="section__subtitle">Samedi 15 mars 2026 à 20H00</p>
            </div>
            <div class="ticket__details">
                <p class="ticket__detail-label">Tribune</p>
                <p class="ticket__detail-value ticket__detail-value--accent">Nord</p>
            </div>
            <div class="ticket__details">
                <p class="ticket__detail-label">Niveaux</p>
                <p class="ticket__detail-value  ticket__detail-value--accent">Haut</p>
            </div>
            <div class="ticket__details">
                <p class="ticket__detail-label">Nombre de places</p>
                <p class="ticket__detail-value  ticket__detail-value--primary">1 place</p>
            </div>
            <div class="ticket__details">
                <p class="ticket__detail-label">Numéro de siege</p>
                <p class="ticket__detail-value  ticket__detail-value--muted">Attribution automatique</p>
            </div>
            <div class="ticket__details">
                <p class="ticket__detail-label">Réservé par </p>
                <p class="ticket__detail-value  ticket__detail-value--primary">Julien D.</p>
            </div>
            <div class="ticket__detail-footer">
                <button type="submit" class="btn--primary">Confirmer la réservation</button>
                <span>Un QR code vous sera généré après confirmation</span>
            </div>
        </div>
    </div>
</body>
</html>