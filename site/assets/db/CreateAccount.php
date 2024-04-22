<?php

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $p_email = $_POST["mail"];
    $p_Name = $_POST["pseudo"];
    $p_password = $_POST["password"];

    // Hachage du mot de passe
    $hashed_password = password_hash($p_password, PASSWORD_DEFAULT);

    require("connexionDB.php");

    // Vérifier si le pseudo existe déjà
    $stmt_check = $pdo->prepare("SELECT * FROM `Utilisateur` WHERE `Name` = :Name");
    $stmt_check->execute(array(':Name' => $p_Name));
    $row = $stmt_check->fetch(PDO::FETCH_ASSOC);

    if (!$row) {
        // Le pseudo n'existe pas, on peut créer le compte
        $stmt_insert = $pdo->prepare("INSERT INTO `Utilisateur`(`email`, `Name`, `password`) VALUES (:email, :Name, :password)");
        $stmt_insert->execute(array(':email' => $p_email, ':Name' => $p_Name, ':password' => $hashed_password));

        header("location: ../../login.php");
    } else {
        // Le pseudo existe déjà, rediriger l'utilisateur vers une page d'erreur ou de gestion du conflit
        header("location: ../../CreateAccount.php");
    }
} else {
    header("location: ../../index.php");
    die();
}