<?php

session_start();

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['identifiant'])) {
    // Rediriger vers la page de login
    header("Location: login.php");
    exit;
}

// Si l'utilisateur est connecté, afficher la page sécurisée
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Galerie</title>
</head>

<body>

    <?php require("assets/css/menu.php"); ?>n  

    <h2>Bienvenue, <?php echo $_SESSION['identifiant']; ?>!</h2>
    <p>Ceci est une page sécurisée.</p>

</body>

</html>