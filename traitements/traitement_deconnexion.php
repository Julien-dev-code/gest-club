<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] !=='POST') {
    header('Location: ../index.php');
    exit;
}

if (!isset($_SESSION['user_id'])) {
    header('Location: ../index.php');
    exit;
}

session_unset();
session_destroy();

header('Location: ../index.php');
exit;

?>