<?php
require 'vendor/autoload.php';

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use Slim\Exception\HttpBadRequestException;
use Slim\Psr7\UploadedFile;

$app = AppFactory::create();

// Middleware for parsing the request body
$app->addBodyParsingMiddleware();

// Dependency injection container
$container = $app->getContainer();

// Database connection
$container['db'] = function() {
    $db = new mysqli('database', 'paugetc', 'Capa1677', 'BIBYdex');
    if ($db->connect_error) {
        die('Connection failed: ' . $db->connect_error);
    }
    return $db;
};

// Route to upload a photo
$app->post('/upload', function (Request $request, Response $response, $args) {
    $data = $request->getParsedBody();
    $uploadedFiles = $request->getUploadedFiles();
    $photo = $uploadedFiles['photo'] ?? null;

    $id_utilisateur = $data['id_utilisateur'] ?? null;
    $id_galerie = $data['id_galerie'] ?? null;

    if (!$id_utilisateur || !$id_galerie || !$photo) {
        $response->getBody()->write(json_encode(['error' => 'Missing required fields']));
        return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
    }

    $photo_data = $photo->getStream()->getContents();

    $db = $this->get('db');

    $stmt = $db->prepare('INSERT INTO Photo (id_utilisateur, id_galerie, photo_data) VALUES (?, ?, ?)');
    $stmt->bind_param('iis', $id_utilisateur, $id_galerie, $photo_data);

    if ($stmt->execute()) {
        $response->getBody()->write(json_encode(['message' => 'Photo uploaded successfully']));
        return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
    } else {
        $response->getBody()->write(json_encode(['error' => 'Failed to upload photo']));
        return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
    }
});

// Route to verify username and password
$app->post('/login', function (Request $request, Response $response, $args) {
    $data = $request->getParsedBody();
    $name = $data['Name'] ?? null;
    $password = $data['password'] ?? null;

    if (!$name || !$password) {
        $response->getBody()->write(json_encode(['error' => 'Name and password are required']));
        return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
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
            $response->getBody()->write(json_encode(['id_utilisateur' => $row['id_utilisateur']]));
            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } else {
            $response->getBody()->write(json_encode(['error' => 'Incorrect Name or password']));
            return $response->withStatus(401)->withHeader('Content-Type', 'application/json');
        }
    } else {
        $response->getBody()->write(json_encode(['error' => 'Incorrect Name or password']));
        return $response->withStatus(401)->withHeader('Content-Type', 'application/json');
    }
});

$app->run();