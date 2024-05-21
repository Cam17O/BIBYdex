<?php

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $p_email = $_POST["mail"];
    $p_Name = $_POST["pseudo"];
    $p_password = $_POST["password"];

    // Hash the password using PHP's password_hash() function
    $hashed_password = password_hash($p_password, PASSWORD_BCRYPT);

    require("connexionDB.php");

    // Check if the username already exists
    $stmt_check = $pdo->prepare("SELECT * FROM `Utilisateur` WHERE `Name` = :Name");
    $stmt_check->execute(array(':Name' => $p_Name));
    $row = $stmt_check->fetch(PDO::FETCH_ASSOC);

    if (!$row) {
        // The username does not exist, create the account
        $stmt_insert = $pdo->prepare("INSERT INTO `Utilisateur`(`email`, `Name`, `password`) VALUES (:email, :Name, :password)");
        $stmt_insert->execute(array(':email' => $p_email, ':Name' => $p_Name, ':password' => $hashed_password));

        header("location: ../../login.php");
    } else {
        // The username already exists, redirect the user to an error page or handle the conflict
        header("location: ../../CreateAccount.php");
    }
} else {
    header("location: ../../index.php");
    die();
}