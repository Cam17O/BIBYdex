<?php

session_start();

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $p_pseudo = $_POST["pseudo"];
    $p_password = $_POST["password"];

    // connexion à la base de données
    require("connexionDB.php");

    $stmt = $pdo->prepare("SELECT * FROM `Utilisateur` WHERE `Name` = :pseudo");
    $stmt->execute(array(':pseudo' => $p_pseudo));

    // Fetch all rows as an associative array
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($p_password, $user['password'])) {

        $_SESSION['identifiant'] = $p_pseudo;
        header("location: ../../galerie.php");
        exit;
    }
     else {
        header("location: ../../index.php");
        die();
    }
} else {
    header("location: ../../index.php");
    die();
}