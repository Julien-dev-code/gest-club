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

$id_utilisateur = $_SESSION['user_id'];

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


try {
    // === Check #1 : événement existe ===
    $sql = "SELECT id, statut, date_debut 
            FROM evenement 
            WHERE id = :id";

    $requete = $pdo->prepare($sql);
    $requete->execute(['id' => $id_evenement]);
    $evenement = $requete->fetch(PDO::FETCH_ASSOC);

    if (!$evenement) {
        ajouter_flash('error', "Événement introuvable.");
        header('Location: ' . $retour_url);
        exit;
    }
    // === Check #2 : statut annulé ===
    if ($evenement['statut'] === 'annule') {
        ajouter_flash('error', "Événement annulé.");
        header('Location: ' . $retour_url);
        exit;
    }
    // === Check #3 : date passée ===
    $dateEvenement = new DateTime($evenement['date_debut']);
    $maintenant = new DateTime();

    if ($dateEvenement < $maintenant) {
        ajouter_flash('error', "Cet événement a déjà commencé.");
        header('Location: ' . $retour_url);
        exit;
    }

    // === Check #4 : places dispo (COUNT global) ===
    $sql_places = "SELECT COUNT(*) 
                FROM reservation_place rp
                JOIN reservation r ON rp.id_reservation = r.id
                WHERE r.id_evenement = :id";


    $requete_places = $pdo->prepare($sql_places);
    $requete_places->execute(['id' => $id_evenement]);
    $total_places_reservees = (int) $requete_places->fetchColumn();

    if ($total_places_reservees >= CAPACITE_STADE) {
        ajouter_flash('error', "Cet événement est complet.");
        header('Location: ' . $retour_url);
        exit;
    }

    // === Check #5 : quota utilisateur (COUNT + AND user) ===
    $sql_quota = "SELECT COUNT(*) 
                    FROM reservation_place rp
                    JOIN reservation r ON rp.id_reservation = r.id
                    WHERE r.id_evenement = :id_evenement
                    AND r.id_utilisateur = :id_utilisateur";

    $requete_quota = $pdo->prepare($sql_quota);
    $requete_quota->execute([
        'id_evenement' => $id_evenement,
        'id_utilisateur' => $_SESSION['user_id']
    ]);
    $places_deja_reservees = (int) $requete_quota->fetchColumn();

    if ($places_deja_reservees + $nombre_places > 2) {
        ajouter_flash('error', "Vous ne pouvez pas réserver plus de 2 places pour cet événement.");
        header('Location: ' . $retour_url);
        exit;
    }

    // === Check #6 : places dispo dans tribune/niveau choisis ===
    $sql_places_dispo = "SELECT p.id
                        FROM place p
                        JOIN tribune t ON p.id_tribune = t.id
                        JOIN niveau n ON p.id_niveau = n.id
                        WHERE t.nom = :tribune
                        AND n.nom = :niveau
                        AND p.id NOT IN (
                        SELECT rp.id_place
                        FROM reservation_place rp
                        JOIN reservation r ON rp.id_reservation = r.id
                        WHERE r.id_evenement = :id_evenement
                        )
                        LIMIT " . (int)$nombre_places;


    $requete_places_dispo = $pdo->prepare($sql_places_dispo);
    $requete_places_dispo->execute([
        'tribune' => $tribune,
        'niveau' => $niveau,
        'id_evenement' => $id_evenement,
    ]);
    $places_dispos = $requete_places_dispo->fetchAll(PDO::FETCH_COLUMN);

    if (count($places_dispos) < $nombre_places) {
    ajouter_flash('error', "Il ne reste plus assez de places pour cet événement.");
    header('Location: ' . $retour_url);
    exit;
}

    $pdo->beginTransaction();

    $sql_insert_reservation = "INSERT INTO reservation (id_utilisateur, id_evenement) 
                               VALUES (:id_utilisateur, :id_evenement)";

    $requete_insert_reservation = $pdo->prepare($sql_insert_reservation);
    $requete_insert_reservation->execute([
        'id_utilisateur' => $id_utilisateur,
        'id_evenement' => $id_evenement,
    ]);

    $id_reservation = $pdo->lastInsertId();

    $sql_insert_place = "INSERT INTO reservation_place (id_reservation, id_place, qr_code)
                         VALUES (:id_reservation, :id_place, :qr_code)";

    $requete_insert_place = $pdo->prepare($sql_insert_place);

    foreach ($places_dispos as $id_place) {
        $qr_code = bin2hex(random_bytes(16));
        $requete_insert_place->execute([
            'id_reservation' => $id_reservation,
            'id_place' => $id_place,
            'qr_code' => $qr_code,
        ]);
    }

    $pdo->commit();

    header('Location: ../qrcode.php?id=' . $id_reservation);
    exit;


} catch (PDOException $e){
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        ajouter_flash('error', "Une erreur technique est survenue.");
        header('Location: ' . $retour_url);
        exit;
    
}