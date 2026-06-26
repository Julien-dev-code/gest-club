<header>
    <nav class="navbar" id="nav">
        <div class="navbar__top">
            <a href="<?= isset($_SESSION['user_id']) ? 'accueil-connecte.php' : 'index.php' ?>" class="navbar__brand">
                GEST<span>CLUB</span>
            </a>
            <button id="burger-button-display">
                <i class='bx bx-menu'></i>
                <i class='bx bx-x'></i>
            </button>
        </div>
        
        <div class="navbar__right">
            <?php if (isset($_SESSION['user_id'])) : ?>
                
                <ul class="navbar__links">
                    <li><a href="evenements.php">Événements</a></li>
                </ul>
                
                <form class="navbar__logout" action="traitements/traitement_deconnexion.php" method="POST">
                    <button class="navbar__logout-btn" type="submit">Se déconnecter</button>
                </form>
                
                <div class="navbar__user">
                    <span class="navbar__username">
                        <?= htmlspecialchars(ucfirst(strtolower($_SESSION['prenom']))) . ' ' . htmlspecialchars(strtoupper(substr($_SESSION['nom'], 0, 1))) . '.' ?>
                    </span>
                    <span class="navbar__avatar">
                        <?= htmlspecialchars(strtoupper(substr($_SESSION['prenom'], 0, 1))) . htmlspecialchars(strtoupper(substr($_SESSION['nom'], 0, 1))) ?>
                    </span>
                </div>
                
            <?php else : ?>
                
                <ul class="navbar__links" id="nav-ul">
                    <li><a href="#fonctionnalites">Fonctionnalités</a></li>
                    <li><a href="evenements.php">Événements</a></li>
                    <li><a href="connexion.php" class="navbar__cta btn--primary">Me connecter</a></li>
                </ul>
                
            <?php endif; ?>
        </div>
    </nav>
</header>