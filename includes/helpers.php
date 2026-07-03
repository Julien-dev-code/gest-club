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