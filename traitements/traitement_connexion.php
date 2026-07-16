<?php

require_once __DIR__ .'/../includes/db.php';
require_once __DIR__ .'/../includes/helpers.php';

session_start();

if ($_SERVER['REQUEST_METHOD'] !=='POST') {
    header('Location: ../connexion.php');
    exit;
}

$email = trim($_POST['email'] ?? '');
$mot_de_passe = $_POST['mot_de_passe'] ?? '';


if ($email === '' || $mot_de_passe === '') {
    ajouter_flash('error', 'Veuillez remplir tous les champs.');
    $_SESSION['anciennes_valeurs'] = ['email' => $email];
    header('Location: ../connexion.php');
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    ajouter_flash('error', 'Format d\'email invalide.');
    $_SESSION['anciennes_valeurs'] = ['email' => $email];
    header('Location: ../connexion.php');
    exit;
}

$sql = "SELECT id, nom, prenom, mot_de_passe, actif, `role`
        FROM utilisateur
        WHERE email = :email";

try {
    $requete = $pdo->prepare($sql);
    $requete->execute(['email' => $email]);
    $user = $requete->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($mot_de_passe, $user['mot_de_passe'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['prenom'] = $user['prenom'];
        $_SESSION['nom'] = $user['nom'];
        $_SESSION['role'] = $user['role'];

        header('Location: ../accueil-connecte.php');
        exit;
    } else {
        ajouter_flash('error', 'Email ou mot de passe incorrect.');
        $_SESSION['anciennes_valeurs'] = ['email' => $email];

        header('Location: ../connexion.php');
        exit;
    }
} catch (PDOException $e) {
    ajouter_flash('error', 'Une erreur est survenue, veuillez réessayer.');
    header('Location: ../connexion.php');
    exit;
}

