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

// Mettre à jour le statut de la demande d'amis à "accepted"
$stmt = $pdo->prepare("UPDATE Est_amisc_de SET status = 'accepted' WHERE id_utilisateur = :friendId AND id_utilisateur_1 = :connectedUserId");
$stmt->execute(['friendId' => $friendId, 'connectedUserId' => $connectedUserId]);

// Rediriger avec un message de succès
header("Location: friend_requests.php?message=request_accepted");
exit;
?>