<?php

require_once __DIR__ .'/../includes/db.php';
require_once __DIR__ .'/../includes/helpers.php';

session_start();


// Garde 1 : Authentification
if (!isset($_SESSION['user_id'])) {
    header('Location: ../connexion.php');
    exit;
}

// Garde 2 : Méthode HTTP
if ($_SERVER['REQUEST_METHOD'] !=='POST') {
    header('Location: ../evenements.php');
    exit;
}

$retour_url = "../evenements.php";

$tribunes_valides = ['nord', 'sud', 'est', 'ouest'];
$niveaux_valides = ['Haut', 'Milieu', 'Bas'];

$id_evenement = filter_input(INPUT_POST, 'id_evenement', FILTER_VALIDATE_INT, [
        'options' => ['min_range' => 1]
    ]);

   
$nombre_places = filter_input(INPUT_POST, 'nombre_places', FILTER_VALIDATE_INT, [
        'options' => ['min_range' => 1,'max_range' => 2]
    ]);

$tribune = $_POST['tribune'] ?? '';
$niveau = $_POST['niveau'] ?? '';

// === VÉRIFICATIONS ===

 // Si l'ID est invalide ou absent : stockage d'un flash d'erreur et redirect vers la liste
if ($id_evenement === false || $id_evenement === null) {
    ajouter_flash('error', "Événement introuvable.");
    header('Location: ' . $retour_url);
    exit;
}

if ($nombre_places === false || $nombre_places === null) {
    ajouter_flash('error', "Nombre de places invalide.");
    header('Location: ' . $retour_url);
    exit;
}

if (!in_array($tribune, $tribunes_valides, true)) {
    ajouter_flash('error', "Tribune non valide.");
    header('Location: ' . $retour_url);
    exit;
}

if (!in_array($niveau, $niveaux_valides, true)) {
    ajouter_flash('error', "Niveaux non valide.");
    header('Location: ' . $retour_url);
    exit;
}



