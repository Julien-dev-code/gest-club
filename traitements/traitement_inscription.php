<?php

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

$errors = [];

if (empty($prenom)) {
    $errors[] = "Le prenom est obligatoire";
}

if (empty($nom)) {
    $errors[] = "Le nom est obligatoire";
}

if (empty($telephone)) {
    $errors[] = "Le telephone est obligatoire";
}elseif (!preg_match('/^0[67](\s?\d{2}){4}$/', $telephone)) {
    $errors[] = "Le telephone n'est pas valide";
}

if (empty($email)) {
    $errors[] = "L'email est obligatoire";
}elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = "L'email n'est pas valide";
}

if (empty($mot_de_passe)) {
    $errors[] = "Le mot de passe est obligatoire";
} else {
    if (strlen($mot_de_passe) < 8) {
        $errors[] = "Le mot de passe doit comporter au moins 8 caractères";
    }
    if (!preg_match('/[A-Z]/', $mot_de_passe)) {
        $errors[] = "Le mot de passe doit comporter au moins 1 majuscule";
    }
    if (!preg_match('/[0-9]/', $mot_de_passe)) {
        $errors[] = "Le mot de passe doit comporter au moins 1 chiffre";
    }
    if (!preg_match('/[#@!?$%&]/', $mot_de_passe)) {
        $errors[] = "Le mot de passe doit comporter au moins 1 caractère special";
    }
}

if (empty($mot_de_passe_confirmation)) {
    $errors[] = "La confirmation de mot de passe est obligatoire";
} elseif ($mot_de_passe !== $mot_de_passe_confirmation) {
    $errors[] = "Les mot de passe ne sont pas identique";
}


// TEMPORAIRE : affichage brut pour test // 

if (!empty($errors)) {
   foreach ($errors as $erreur) {
        echo $erreur . "<br>";
    }
    exit;
}
// TEMPORAIRE : affichage brut pour test // 