<?php

require("connexionDB.php");

// Récupérer le nom de l'utilisateur connecté
$connectedUserName = $_SESSION['identifiant'];

// Récupérer l'id de l'utilisateur connecté en utilisant son nom
$stmt = $pdo->prepare("SELECT id_utilisateur FROM Utilisateur WHERE Name = :name");
$stmt->execute(['name' => $connectedUserName]);
$connectedUser = $stmt->fetch(PDO::FETCH_ASSOC);
$connectedUserId = $connectedUser['id_utilisateur'];

// Requête pour récupérer les utilisateurs et les informations d'amitié
$stmt = $pdo->prepare("
    SELECT u.id_utilisateur, u.email, u.Name, 
           CASE 
               WHEN a.status = 'accepted' THEN 1 
               ELSE 0 
           END AS is_friend
    FROM Utilisateur u
    LEFT JOIN Est_amisc_de a ON (
        (a.id_utilisateur = u.id_utilisateur AND a.id_utilisateur_1 = :connectedUserId) OR
        (a.id_utilisateur = :connectedUserId AND a.id_utilisateur_1 = u.id_utilisateur)
    )
    AND a.status = 'accepted'
    WHERE u.id_utilisateur != :connectedUserId
");
$stmt->execute(['connectedUserId' => $connectedUserId]);

// Fetch all rows as an associative array
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($users as $user) {
    echo "<tr>";
    echo "<th scope='row'>" . $user['id_utilisateur'] . "</th>";
    echo "<td>" . $user['Name'] . "</td>";
    echo "<td>" . $user['email'] . "</td>";
    
    // Vérifier si l'utilisateur est ami avec l'utilisateur connecté
    if ($user['is_friend']) {
        echo "<td><a href='galerie2.php?id=" . $user['id_utilisateur'] . "' class='btn btn-primary'>Voir la galerie</a></td>";
    } else {
        echo "<td><a href='send_friend_request.php?id=" . $user['id_utilisateur'] . "' class='btn btn-secondary'>Demande d'amis</a></td>";
    }
    
    echo "</tr>";
}
?>