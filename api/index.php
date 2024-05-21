<?php
require 'vendor/autoload.php';

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

$app = AppFactory::create();

// Middleware pour parser le corps des requêtes en JSON
$app->addBodyParsingMiddleware();

$container = $app->getContainer();

$container['db'] = function() {
    $db = new mysqli('database', 'paugetc', 'Capa1677', 'BIBYdex');
    if ($db->connect_error) {
        die('Connection failed: ' . $db->connect_error);
    }
    return $db;
};

// Route pour télécharger une photo
$app->post('/upload', function (Request $request, Response $response, $args) {
    $data = $request->getParsedBody();
    $uploadedFiles = $request->getUploadedFiles();
    $photo = $uploadedFiles['photo'] ?? null;

    $id_utilisateur = $data['id_utilisateur'] ?? null;
    $id_galerie = $data['id_galerie'] ?? null;

    if (!$id_utilisateur || !$id_galerie || !$photo) {
        return $response->withStatus(400)->write('Missing required fields');
    }

    $photo_data = file_get_contents($photo->getFilePath());

    $db = $this->get('db');

    $stmt = $db->prepare('INSERT INTO Photo (id_utilisateur, id_galerie, photo_data) VALUES (?, ?, ?)');
    $stmt->bind_param('iis', $id_utilisateur, $id_galerie, $photo_data);

    if ($stmt->execute()) {
        return $response->withStatus(200)->write('Photo uploaded successfully');
    } else {
        return $response->withStatus(500)->write('Failed to upload photo');
    }
});

$app->run();


// Route pour vérifier le nom d'utilisateur et le mot de passe
$app->post('/login', function (Request $request, Response $response, $args) {
    $data = $request->getParsedBody();
    $name = $data['Name'] ?? null;
    $password = $data['password'] ?? null;

    if (!$name || !$password) {
        return $response->withStatus(400)->write('Name and password are required');
    }

    $db = $this->get('db');

    $stmt = $db->prepare('SELECT id_utilisateur, password FROM Utilisateur WHERE Name = ?');
    $stmt->bind_param('s', $name);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $storedPassword = $row['password'];
        
        if (password_verify($password, $storedPassword)) {
            return $response->withJson(['id_utilisateur' => $row['id_utilisateur']], 200);
        } else {
            return $response->withStatus(401)->write('Incorrect Name or password');
        }
    } else {
        return $response->withStatus(401)->write('Incorrect Name or password');
    }
});




$app->run();