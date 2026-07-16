<?php

// je definie la constante avec la capacite du stade
// elle ne bouge pas
const CAPACITE_STADE = 1200;


function calculer_statut_affiche(array $evenement): string {
    if ($evenement['statut'] === 'annule'){
        return 'annule';
    } 
    
    if (new DateTime($evenement['date_fin']) < new DateTime()) {
        return 'termine';
    }

    if ($evenement['places_prises'] === CAPACITE_STADE) {
        return 'complet';
    }

    if (new DateTime($evenement['date_debut']) <= new DateTime()
        && new DateTime($evenement['date_fin']) > new DateTime()) {
        return 'en_cours';
    }

    return 'a_venir';
    
}


function ajouter_flash(string $type, string $message): void {
    if ($type !== 'error' && $type !== 'success') {
        throw new InvalidArgumentException("Type de flash invalide : $type");
    }

    $_SESSION['flash_' . $type][] = $message;
}


function afficher_flashes(): void {
    if (!empty($_SESSION['flash_error'])) {
        foreach($_SESSION['flash_error'] as $message) {
            echo '<div class="alert alert--error">' . '<i class="bx bxs-x-circle"></i>' . htmlspecialchars($message, ENT_QUOTES, 'UTF-8') . '</div>';
        }
        unset($_SESSION['flash_error']);
    }

    if (!empty($_SESSION['flash_success'])) {
        foreach($_SESSION['flash_success'] as $message)  {
            echo '<div class="alert alert--success">' . '<i class="bx bxs-check-circle"></i>' . htmlspecialchars($message, ENT_QUOTES, 'UTF-8') . '</div>';
        }
        unset($_SESSION['flash_success']);
    }
}