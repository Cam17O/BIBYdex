<?php

session_start();

if (!isset($_SESSION['identifiant'])) {
    header("Location: login.php");
    exit;
}

require("assets/db/connexionDB.php");

$connectedUserName = $_SESSION['identifiant'];

// Récupérer l'id de l'utilisateur connecté en utilisant son nom
$stmt = $pdo->prepare("SELECT id_utilisateur FROM Utilisateur WHERE Name = :name");
$stmt->execute(['name' => $connectedUserName]);
$connectedUser = $stmt->fetch(PDO::FETCH_ASSOC);
$connectedUserId = $connectedUser['id_utilisateur'];

$friendId = $_GET['id'];

// Vérifier si une demande d'amis existe déjà
$stmt = $pdo->prepare("SELECT * FROM Est_amisc_de WHERE (id_utilisateur = :user1 AND id_utilisateur_1 = :user2) OR (id_utilisateur = :user2 AND id_utilisateur_1 = :user1)");
$stmt->execute(['user1' => $connectedUserId, 'user2' => $friendId]);
$request = $stmt->fetch();

if ($request) {
    // Demande d'amis existe déjà, rediriger avec un message
    header("Location: allUser.php?message=already_requested");
} else {
    // Créer une nouvelle demande d'amis
    $stmt = $pdo->prepare("INSERT INTO Est_amisc_de (id_utilisateur, id_utilisateur_1, status) VALUES (:user1, :user2, 'pending')");
    $stmt->execute(['user1' => $connectedUserId, 'user2' => $friendId]);

    // Rediriger avec un message de succès
    header("Location: allUser.php?message=request_sent");
}
exit;
?>