<?php

require __DIR__ . '/vendor/autoload.php';

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use Slim\Psr7\Factory\ResponseFactory;
use DI\Container;
use Slim\Middleware\BodyParsingMiddleware;

// Create a new container
$container = new Container();

// Set up database connection using PDO
$container->set('db', function () {
    $db = new PDO('mysql:host=database;dbname=BIBYdex;charset=utf8', 'paugetc', 'Capa1677');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    return $db;
});

// Create App with the container
AppFactory::setContainer($container);
$app = AppFactory::create();

// Middleware for parsing JSON request bodies
$app->add(new BodyParsingMiddleware());

// Route to upload a photo
$app->post('/upload', function (Request $request, Response $response) {
    $data = $request->getParsedBody();
    $uploadedFiles = $request->getUploadedFiles();
    $photo = $uploadedFiles['photo'] ?? null;

    $id_utilisateur = $data['id_utilisateur'] ?? null;
    $id_galerie = $data['id_galerie'] ?? null;

    if (!$id_utilisateur || !$id_galerie || !$photo) {
        $response->getBody()->write('Missing required fields');
        return $response->withStatus(400);
    }

    $photo_data = file_get_contents($photo->getStream()->getMetadata('uri'));

    $db = $this->get('db');

    $stmt = $db->prepare('INSERT INTO Photo (id_utilisateur, id_galerie, photo_data) VALUES (?, ?, ?)');
    $stmt->bindParam(1, $id_utilisateur, PDO::PARAM_INT);
    $stmt->bindParam(2, $id_galerie, PDO::PARAM_INT);
    $stmt->bindParam(3, $photo_data, PDO::PARAM_LOB);

    if ($stmt->execute()) {
        $response->getBody()->write('Photo uploaded successfully');
        return $response->withStatus(200);
    } else {
        $response->getBody()->write('Failed to upload photo');
        return $response->withStatus(500);
    }
});

// Route to check username and password
$app->post('/login', function (Request $request, Response $response) {
    $data = $request->getParsedBody();
    $name = $data['Name'] ?? null;
    $password = $data['password'] ?? null;

    if (!$name || !$password) {
        $response->getBody()->write('Name and password are required');
        return $response->withStatus(400);
    }

    $db = $this->get('db');

    $stmt = $db->prepare('SELECT id_utilisateur, password FROM Utilisateur WHERE Name = ?');
    $stmt->bindParam(1, $name, PDO::PARAM_STR);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result) {
        $storedPassword = $result['password'];

        if (password_verify($password, $storedPassword)) {
            $responseFactory = new ResponseFactory();
            $jsonResponse = $responseFactory->createResponse();
            $jsonResponse->getBody()->write(json_encode(['id_utilisateur' => $result['id_utilisateur']]));
            return $jsonResponse->withStatus(200)->withHeader('Content-Type', 'application/json');
        } else {
            $response->getBody()->write('Incorrect Name or password');
            return $response->withStatus(401);
        }
    } else {
        $response->getBody()->write('Incorrect Name or password');
        return $response->withStatus(401);
    }
});

// Route pour obtenir toutes les photos d'un utilisateur
$app->get('/utilisateurs/{idUtilisateur}/photos', function (Request $request, Response $response, $args) {
    $idUtilisateur = $args['idUtilisateur'];

    // Récupérer la connexion à la base de données depuis le conteneur
    $db = $this->get('db');

    // Requête SQL pour sélectionner toutes les photos d'un utilisateur
    $sql = 'SELECT * FROM Photo WHERE id_utilisateur = :id_utilisateur';
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':id_utilisateur', $idUtilisateur);
    $stmt->execute();
    $photos = $stmt->fetchAll();

    // Vérifier si des photos ont été trouvées
    if ($photos) {
        // Convertir le résultat en JSON et l'envoyer en réponse
        $response->getBody()->write(json_encode($photos));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    } else {
        // Aucune photo trouvée pour l'utilisateur
        $response->getBody()->write(json_encode(['message' => 'Aucune photo trouvée pour cet utilisateur']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
    }
});

$app->run();