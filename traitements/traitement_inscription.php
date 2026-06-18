<?php

session_start();

require_once __DIR__ .'/../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] !=='POST') {
    header('Location: ../inscription.php');
    exit;
}

$prenom = trim($_POST['prenom'] ?? '');
$nom = trim($_POST['nom'] ?? '');
$telephone = trim($_POST['telephone'] ?? '');
$email = trim($_POST['email'] ?? '');
$mot_de_passe = $_POST['mot_de_passe'] ?? '';
$mot_de_passe_confirmation = $_POST['mot_de_passe_confirmation'] ?? '';

$erreurs = [];

if (empty($prenom)) {
    $erreurs[] = "Le prenom est obligatoire";
}

if (empty($nom)) {
    $erreurs[] = "Le nom est obligatoire";
}

if (empty($telephone)) {
    $erreurs[] = "Le telephone est obligatoire";
}elseif (!preg_match('/^0[67](\s?\d{2}){4}$/', $telephone)) {
    $erreurs[] = "Le telephone n'est pas valide";
}

if (empty($email)) {
    $erreurs[] = "L'email est obligatoire";
}elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $erreurs[] = "L'email n'est pas valide";
}

if (empty($mot_de_passe)) {
    $erreurs[] = "Le mot de passe est obligatoire";
} else {
    if (strlen($mot_de_passe) < 8) {
        $erreurs[] = "Le mot de passe doit comporter au moins 8 caractères";
    }
    if (!preg_match('/[A-Z]/', $mot_de_passe)) {
        $erreurs[] = "Le mot de passe doit comporter au moins 1 majuscule";
    }
    if (!preg_match('/[0-9]/', $mot_de_passe)) {
        $erreurs[] = "Le mot de passe doit comporter au moins 1 chiffre";
    }
    if (!preg_match('/[#@!?$%&]/', $mot_de_passe)) {
        $erreurs[] = "Le mot de passe doit comporter au moins 1 caractère special";
    }
}

if (empty($mot_de_passe_confirmation)) {
    $erreurs[] = "La confirmation de mot de passe est obligatoire";
} elseif ($mot_de_passe !== $mot_de_passe_confirmation) {
    $erreurs[] = "Les mot de passe ne sont pas identique";
}

if (!empty($erreurs)) {
    $_SESSION['erreurs'] = $erreurs;
    
    $anciennes_valeurs = $_POST;
    unset($anciennes_valeurs['mot_de_passe']);
    $_SESSION['anciennes_valeurs'] = $anciennes_valeurs;
    
    header('Location: ../inscription.php');
    exit;
}

$mot_de_passe_hash = password_hash($mot_de_passe, PASSWORD_DEFAULT);

$sql = "INSERT INTO utilisateur (nom, prenom, telephone, email, mot_de_passe)
        VALUES (:nom, :prenom, :telephone, :email, :mot_de_passe)";
try {
    $requete = $pdo->prepare($sql);
    $requete->execute([
        ':nom' => $nom,
        ':prenom' => $prenom,
        ':telephone' => $telephone,
        ':email' => $email,
        ':mot_de_passe' => $mot_de_passe_hash
    ]);

    header('Location: ../connexion.php');
    exit;
} catch (PDOException $e) {
    if($e->errorInfo[1] === 1062) {
        $erreurs[] = "Cet email est déjà utilisé.";
    } else {
        $erreurs[] = "Une erreur technique est survenue.";
    }

    $_SESSION['erreurs'] = $erreurs;

    $anciennes_valeurs = $_POST;
    unset($anciennes_valeurs['mot_de_passe']);
    $_SESSION['anciennes_valeurs'] = $anciennes_valeurs;
    
    header('Location: ../inscription.php');
    exit;
    
}

