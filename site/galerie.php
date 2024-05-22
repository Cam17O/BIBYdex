<?php

session_start();

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['identifiant'])) {
    // Rediriger vers la page de login
    header("Location: login.php");
    exit;
}

// Connexion à la base de données
$servername = 'database';
$dbname = 'BIBYdex';
$username = 'paugetc';
$password = 'Capa1677';

// On établit la connexion
try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (Exception $e) {
    die('Erreur : ' . $e->getMessage());
}

// Récupérer l'ID utilisateur
$identifiant = $_SESSION['identifiant'];
$stmt = $pdo->prepare("SELECT id_utilisateur FROM Utilisateur WHERE Name = :name");
$stmt->execute(['name' => $identifiant]);
$user = $stmt->fetch();

$id_utilisateur = $user['id_utilisateur'];

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
    <title>Galerie</title>
</head>

<body>

    <?php require("assets/css/menu.php"); ?>

    <h2>Bienvenue, <?php echo htmlspecialchars($_SESSION['identifiant']); ?>!</h2>
    
    <h3>Votre Galerie</h3>
    <div class="gallery">
        <?php foreach ($photos as $photo): ?>
            <div class="photo">
                <img src="data:image/jpeg;base64,<?php echo base64_encode($photo['photo_data']); ?>" alt="Photo">
            </div>
        <?php endforeach; ?>
    </div>

</body>

</html>
