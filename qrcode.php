<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR Code</title>
    <link rel="stylesheet" href="styles/variables.css">
    <link rel="stylesheet" href="styles/base.css">
    <link rel="stylesheet" href="styles/components.css">
    <link rel="stylesheet" href="styles/pages/qrcode.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <script src="https://code.iconify.design/iconify-icon/1.0.7/iconify-icon.min.js"></script>
</head>
<body>
    <header>
        <div class="navbar">
            <span class="navbar__brand">GEST<span>CLUB</span></span>
            <a href="evenements.html" class="btn--ghost">Retour au événements</a>
        </div>
    </header>
    <main>
        <div class="confirmation__header">
            <span class="badge--success">
                <i class="bx bxs-check-circle"></i>Réservation validée
            </span>
            <h1 class="confirmation__header-title">Votre billet est<br><span class="confirmation__header-title--accent">prêt!</span></h1>
            <p class="confirmation__header-subtitle">
                Présentez ce QR code à l'entrée du stade le jour de l'événement         
            </p>
        </div>

        <div class="ticket">
            <div class="ticket__qr">
                <img src="assets/images/qrcode.png" alt="QR Code de réservation">
                <p class="ticket__qr-number">N° RES-2025-00847</p>
                <p class="ticket__qr-label">DÉTAILS DE LA RÉSERVATION</p>
            </div>

            <div class="ticket__details">
                <div class="ticket__detail-row">
                    <div class="ticket__detail-row-label">
                        <iconify-icon icon="noto-v1:stadium"></iconify-icon>
                        <p class="ticket__detail-label">Événement</p>
                    </div>
                    <p class="ticket__detail-value">FC Metz - PSG</p>
                </div>
            

            
                <div class="ticket__detail-row">
                    <div class="ticket__detail-row-label">
                        <iconify-icon icon="flat-color-icons:calendar"></iconify-icon>
                        <p class="ticket__detail-label">Date</p>
                    </div>
                    <p class="ticket__detail-value">Sam. 14 juin 2025 · 20h45</p>
                </div>
            

                <div class="ticket__detail-row">
                    <div class="ticket__detail-row-label">
                        <iconify-icon icon="fluent-color:location-ripple-24"></iconify-icon>
                        <p class="ticket__detail-label">Tribune - Niveau</p>
                    </div>
                    <p class="ticket__detail-value">Tribune Nord · Niveau 2</p>
                </div>
           

           
                <div class="ticket__detail-row">
                    <div class="ticket__detail-row-label">
                        <iconify-icon icon="noto-v1:seat"></iconify-icon>
                        <p class="ticket__detail-label">Places réservées</p>
                    </div>
                    <span class="badge--seat">N°12-N°13</span>
                </div>

            </div>
            <div class="ticket__detail-footer">
                <button class="btn--primary">Télécharger mon billet</button>
                <a href="evenements.html" class="btn--ghost">Retour aux événements</a>
                <span>Ce QR code est unique et personnel. Il sera scanné une seule fois à l'entrée par l'agent d'accueil.</span>
            </div>
        </div>
    </main>
</body>
</html>