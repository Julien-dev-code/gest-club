<header>
    <div class="navbar">
        <a href="<?= isset($_SESSION['user_id']) ? 'accueil-connecte.php' : 'index.php' ?>" class="navbar__brand">
            GEST<span>CLUB</span>
        </a>
        <a href="<?= $retour_url ?>" class="btn--ghost">
            <?= $retour_texte ?>
        </a>
    </div>
</header>