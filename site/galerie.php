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
$stmt = $pdo->prepare("SELECT id_utilisateur FROM Utilisateur WHERE email = :email");
$stmt->execute(['email' => $identifiant]);
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
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .gallery {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            margin-top: 20px;
        }
        .photo {
            flex: 1 0 21%;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            overflow: hidden;
        }
        .photo img {
            width: 100%;
            height: auto;
            display: block;
        }
    </style>
</head>

<body>

    <?php require("assets/css/menu.php"); ?>

    <div class="container mt-5">
        <h2>Bienvenue, <?php echo htmlspecialchars($_SESSION['identifiant']); ?>!</h2>
        
        <h3 class="mt-4">Votre Galerie</h3>
        <div class="gallery">
            <?php foreach ($photos as $photo): ?>
                <div class="photo">
                    <img src="data:image/jpeg;base64,<?php echo base64_encode($photo['photo_data']); ?>" alt="Photo" class="img-fluid">
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>