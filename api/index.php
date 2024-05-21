<?php
require __DIR__ . '/vendor/autoload.php';

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

$app = AppFactory::create();

// Middleware for parsing JSON request bodies
$app->addBodyParsingMiddleware();

$container = $app->getContainer();

$container['db'] = function($container) {
    $db = new mysqli(
        $container->get('MYSQL_HOST'), 
        $container->get('MYSQL_USER'), 
        $container->get('MYSQL_PASSWORD'), 
        $container->get('MYSQL_DATABASE')
    );
    if ($db->connect_error) {
        die('Connection failed: ' . $db->connect_error);
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
        return $response->withStatus(400)->write('Missing required fields');
    }

    $photo_data = file_get_contents($photo->getFilePath());

    $db = $container->get('db');

    $stmt = $db->prepare('INSERT INTO Photo (id_utilisateur, id_galerie, photo_data) VALUES (?, ?, ?)');
    $stmt->bind_param('iis', $id_utilisateur, $id_galerie, $photo_data);

    if ($stmt->execute()) {
        return $response->withStatus(200)->write('Photo uploaded successfully');
    } else {
        return $response->withStatus(500)->write('Failed to upload photo');
    }
});

// Route to check username and password
$app->post('/login', function (Request $request, Response $response, $args) use ($container) {
    $data = $request->getParsedBody();
    $name = $data['Name'] ?? null;
    $password = $data['password'] ?? null;

    if (!$name || !$password) {
        return $response->withStatus(400)->write('Name and password are required');
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
            return $response->withJson(['id_utilisateur' => $row['id_utilisateur']], 200);
        } else {
            return $response->withStatus(401)->write('Incorrect Name or password');
        }
    } else {
        return $response->withStatus(401)->write('Incorrect Name or password');
    }
});

$app->run();