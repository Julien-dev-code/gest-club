<?php
session_start();

if (isset($_SESSION['user_id'])) {
    header('Location: accueil-connecte.php');
    exit;
}
$retour_texte = "Retour à l'accueil";
$retour_url = "index.php";

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion</title>
    <link rel="stylesheet" href="styles/variables.css">
    <link rel="stylesheet" href="styles/base.css">
    <link rel="stylesheet" href="styles/components.css">
    <link rel="stylesheet" href="styles/pages/auth.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <script src="js/pages/connexion.js"defer></script>
</head>
<body>
    
<?php require_once 'includes/header-simple.php'; ?>

    <main>
        <div class="hero">
            <img class="hero__image" src="assets/images/gabriele-fenili-7MF6_YwHJA8-unsplash.jpg" alt="">
            <h1 class="hero__title">L'arène<br><span class="hero__title--accent">vous attend.</span></h1>
            <p class="hero__subtitle">Réservez vos places, retrouvez vos amis dans les tribunes et vivez chaque match comme jamais...</p>
        </div>
        <div class="form__group">
            <div class="form__header">
                <p class="form__eyebrow">Connexion</p>
                <h2 class="form__title">Bon retour</h2>
                <p class="form__subtitle">Pas encore de compte ? <a href="inscription.php">Créer un compte</a></p>
            </div>
            <form action="traitements/traitement_connexion.php" method="POST" id="connexion-form" novalidate>

            <?php if (isset($_SESSION['erreur_connexion'])) : ?>
                <p class="form__error"><?= htmlspecialchars($_SESSION['erreur_connexion']) ?></p>
                <?php unset($_SESSION['erreur_connexion']); ?>
            <?php endif; ?>

            

                <div class="form-group">
                    <label class="form__label" for="email">Adresse email</label>
                    <input class="form__input" type="email" id="email" name="email" value="<?= htmlspecialchars($_SESSION['anciennes_valeurs']['email'] ?? '')?>" placeholder="Exemple@email.com" >
                </div>

                <div class="form-group">
                    <label class="form__label" for="mot_de_passe">Mot de passe</label>
                    <input class="form__input" type="password" id="mot_de_passe" name="mot_de_passe"  placeholder="••••••••">
                </div>


                <?php unset($_SESSION['anciennes_valeurs']); ?>

                <div class="form__footer">
                    <a href="#">Mot de passe oublié ?</a>
                    <button class="btn--primary" type="submit" >Se Connecter</button>
                    <p>Pas encore de inscrit? <a href="inscription.php">Créer mon compte gratuitement</a></p>
                </div>
            </form>
        </div>
    </main>

</body>
</html>