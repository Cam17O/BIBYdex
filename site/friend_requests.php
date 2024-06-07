<?php

session_start();

if (!isset($_SESSION['identifiant'])) {
    header("Location: login.php");
    exit;
}

require("connexionDB.php");

$connectedUserName = $_SESSION['identifiant'];

// Récupérer l'id de l'utilisateur connecté en utilisant son nom
$stmt = $pdo->prepare("SELECT id_utilisateur FROM Utilisateur WHERE Name = :name");
$stmt->execute(['name' => $connectedUserName]);
$connectedUser = $stmt->fetch(PDO::FETCH_ASSOC);
$connectedUserId = $connectedUser['id_utilisateur'];

// Récupérer les demandes d'amis en attente pour l'utilisateur connecté
$stmt = $pdo->prepare("
    SELECT u.id_utilisateur, u.email, u.Name 
    FROM Utilisateur u
    JOIN Est_amisc_de a ON a.id_utilisateur = u.id_utilisateur
    WHERE a.id_utilisateur_1 = :connectedUserId AND a.status = 'pending'
");
$stmt->execute(['connectedUserId' => $connectedUserId]);

$friendRequests = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Demandes d'amis</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
</head>
<body>

    <?php require("assets/css/menu.php"); ?>

    <div class="container">
        <h1>Demandes d'amis</h1>
        <table class="table">
            <thead class="thead-dark">
                <tr>
                    <th scope="col">id</th>
                    <th scope="col">Email</th>
                    <th scope="col">Nom</th>
                    <th scope="col">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($friendRequests as $request): ?>
                <tr>
                    <th scope='row'><?= $request['id_utilisateur'] ?></th>
                    <td><?= $request['email'] ?></td>
                    <td><?= $request['Name'] ?></td>
                    <td>
                        <a href='accept_friend_request.php?id=<?= $request['id_utilisateur'] ?>' class='btn btn-primary'>Accepter</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>

</body>
</html>
