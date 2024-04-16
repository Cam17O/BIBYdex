<?php

    $email = $_POST["email"];
    $Name = $_POST["Name"];
    $password = $_POST["password"];


    header("Content-Type:application/json");
    //ceci signifie que le format du corps de la requête est JSON


    require("assets/db/connexionDB.php");

    $stmt = $pdo->prepare("INSERT INTO `Utilisateur`(`id_utilisateur`, `email`, `Name`, `password`) VALUES ('email=:email','Name=:Name','password=:password')");
    $stmt->execute(array(':email' => $email, ':Name' => $Name, ':password' => $password));
    $count = $stmt->rowCount();

    