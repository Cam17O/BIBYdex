<?php

session_start();

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['identifiant'])) {
    // Rediriger vers la page de login
    header("Location: login.php");
    exit;
}

// Vérifier si l'ID de l'utilisateur est passé en paramètre dans l'URL
if (!isset($_GET['id'])) {
    echo "ID de l'utilisateur non spécifié.";
    exit;
}

$id_utilisateur = $_GET['id'];

// Connexion à la base de données
$servername = 'database';
$dbname = 'BIBYdex';
$username = 'paugetc';
$password = 'Capa1677';

try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (Exception $e) {
    die('Erreur : ' . $e->getMessage());
}

// Récupérer les photos de l'utilisateur
$stmt = $pdo->prepare("SELECT photo_data FROM Photo WHERE id_utilisateur = :id_utilisateur");
$stmt->execute(['id_utilisateur' => $id_utilisateur]);
$photos = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Galerie de l'utilisateur</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
</head>

<body>

    <?php require("assets/css/menu.php"); ?>

    <div class="container mt-5">
        <h2>Galerie de l'utilisateur</h2>
        <div class="row">
            <?php foreach ($photos as $photo): ?>
                <div class="col-md-3 mb-4">
                    <img src="data:image/jpeg;base64,<?php echo base64_encode($photo['photo_data']); ?>" alt="Photo" class="img-fluid">
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>

</body>

</html>