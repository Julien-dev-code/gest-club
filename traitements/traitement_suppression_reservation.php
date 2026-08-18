<?php

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/helpers.php';

session_start();


if (!isset($_SESSION['user_id'])) {
    header('Location: ../connexion.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../accueil-connecte.php');
    exit;
}

$retour_url = "../accueil-connecte.php";

$id_reservation = filter_input(INPUT_POST, 'id_reservation', FILTER_VALIDATE_INT, [
    'options' => ['min_range' => 1]
]);

if ($id_reservation === false || $id_reservation === null) {
    ajouter_flash('error', "Réservation introuvable.");
    header('Location: ' . $retour_url);
    exit;
}

try {
    
    $sql_verification = "SELECT evenement.date_debut,
                                evenement.statut
                         FROM reservation
                         INNER JOIN evenement ON reservation.id_evenement = evenement.id
                         WHERE reservation.id = :id_reservation
                           AND reservation.id_utilisateur = :id_utilisateur";

    $requete_verification = $pdo->prepare($sql_verification);
    $requete_verification->execute([
        'id_reservation' => $id_reservation,
        'id_utilisateur' => $_SESSION['user_id']
    ]);

    $reservation = $requete_verification->fetch(PDO::FETCH_ASSOC);

    if (!$reservation) {
        ajouter_flash('error', "Réservation introuvable.");
        header('Location: ' . $retour_url);
        exit;
    }

    
    if (new DateTime($reservation['date_debut']) <= new DateTime()) {
        ajouter_flash('error', "Cet événement a déjà commencé, la réservation ne peut plus être annulée.");
        header('Location: ' . $retour_url);
        exit;
    }

    
    $sql_suppression = "DELETE FROM reservation
                        WHERE id = :id_reservation
                          AND id_utilisateur = :id_utilisateur";

    $requete_suppression = $pdo->prepare($sql_suppression);
    $requete_suppression->execute([
        'id_reservation' => $id_reservation,
        'id_utilisateur' => $_SESSION['user_id']
    ]);

    ajouter_flash('success', "Votre réservation a bien été annulée.");
    header('Location: ' . $retour_url);
    exit;

} catch (PDOException $e) {
    error_log($e->getMessage());
    ajouter_flash('error', "Une erreur technique est survenue.");
    header('Location: ' . $retour_url);
    exit;
}