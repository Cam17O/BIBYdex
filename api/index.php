<?php

require __DIR__ . '/vendor/autoload.php';

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use Slim\Psr7\Factory\ResponseFactory; // Importer ResponseFactory

// Créer l'application Slim
$app = AppFactory::create();

// Middleware pour analyser les corps de requête JSON
$app->addBodyParsingMiddleware();

// Définir les services dans le conteneur
$container = $app->getContainer();

$container['db'] = function($container) {
    $db = new mysqli(
        'database', 
        'paugetc', 
        'Capa1677', 
        'BIBYdex'
    );

    if ($db->connect_error) {
        die('La connexion a échoué : ' . $db->connect_error);
    }
    return $db;
};

// Route to upload a photo
$app->post('/upload', function (Request $request, Response $response, $args) use ($container) {
    $data = $request->getParsedBody();
    $uploadedFiles = $request->getUploadedFiles();
    $photo = $uploadedFiles['photo'] ?? null;

    $id_utilisateur = $data['id_utilisateur'] ?? null;
    $id_galerie = $data['id_galerie'] ?? null;

    if (!$id_utilisateur || !$id_galerie || !$photo) {
        return $response->withStatus(400)->getBody()->write('Missing required fields');
    }

    $photo_data = file_get_contents($photo->getStream()->getMetadata('uri'));

    $db = $container->get('db');

    $stmt = $db->prepare('INSERT INTO Photo (id_utilisateur, id_galerie, photo_data) VALUES (?, ?, ?)');
    $stmt->bind_param('iis', $id_utilisateur, $id_galerie, $photo_data);

    if ($stmt->execute()) {
        return $response->withStatus(200)->getBody()->write('Photo uploaded successfully');
    } else {
        return $response->withStatus(500)->getBody()->write('Failed to upload photo');
    }
});

// Route to check username and password
$app->post('/login', function (Request $request, Response $response, $args) use ($container) {
    $data = $request->getParsedBody();
    $name = $data['Name'] ?? null;
    $password = $data['password'] ?? null;

    if (!$name || !$password) {
        return $response->withStatus(400)->getBody()->write('Name and password are required');
    }

    $db = $container->get('db');

    $stmt = $db->prepare('SELECT id_utilisateur, password FROM Utilisateur WHERE Name = ?');
    $stmt->bind_param('s', $name);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $storedPassword = $row['password'];

        if (password_verify($password, $storedPassword)) {
            // Créer une nouvelle réponse JSON avec ResponseFactory
            $responseFactory = new ResponseFactory();
            $jsonResponse = $responseFactory->createResponse();
            $jsonResponse->getBody()->write(json_encode(['id_utilisateur' => $row['id_utilisateur']]));

            return $jsonResponse->withStatus(200)->withHeader('Content-Type', 'application/json');
        } else {
            return $response->withStatus(401)->getBody()->write('Incorrect Name or password');
        }
    } else {
        return $response->withStatus(401)->getBody()->write('Incorrect Name or password');
    }
});

$app->run();