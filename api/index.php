<?php

// Fonction pour nettoyer les caractères non-UTF-8 d'une chaîne de caractères
function cleanString($str)
{
    return preg_replace(
        '/[\x00-\x08\x10\x0B\x0C\x0E-\x19\x7F]' .
            '|[\x00-\x7F][\x80-\xBF]+' .
            '|([\xC0\xC1]|[\xF0-\xFF])[\x80-\xBF]*' .
            '|[\xC2-\xDF]((?![\x80-\xBF])|[\x80-\xBF]{2,})' .
            '|[\xE0-\xEF](([\x80-\xBF](?![\x80-\xBF]))|(?![\x80-\xBF]{2})|[\x80-\xBF]{3,})/S',
        '',
        $str
    );
}

// Charger les dépendances via Composer
require __DIR__ . '/vendor/autoload.php';

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use Slim\Psr7\Factory\ResponseFactory;
use DI\Container;
use Slim\Middleware\BodyParsingMiddleware;

// Créer un nouveau conteneur
$container = new Container();

// Configurer la connexion à la base de données en utilisant PDO
$container->set('db', function () {
    $db = new PDO('mysql:host=database;dbname=BIBYdex;charset=utf8', 'paugetc', 'Capa1677');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    return $db;
});

// Créer l'application avec le conteneur
AppFactory::setContainer($container);
$app = AppFactory::create();

// Middleware pour analyser les corps de requêtes JSON
$app->add(new BodyParsingMiddleware());

// Route pour uploader une photo
$app->post('/upload', function (Request $request, Response $response) {
    // Récupérer les données du corps de la requête
    $data = $request->getParsedBody();
    // Récupérer les fichiers uploadés
    $uploadedFiles = $request->getUploadedFiles();
    $photo = $uploadedFiles['photo'] ?? null;

    // Récupérer les paramètres de la requête
    $id_utilisateur = $data['id_utilisateur'] ?? null;
    $id_galerie = $data['id_galerie'] ?? null;

    // Vérifier la présence des champs requis
    if (!$id_utilisateur || !$id_galerie || !$photo) {
        $response->getBody()->write('Champs requis manquants');
        return $response->withStatus(400);
    }

    // Lire le contenu de la photo
    $photo_data = file_get_contents($photo->getStream()->getMetadata('uri'));

    // Récupérer la connexion à la base de données
    $db = $this->get('db');

    // Préparer et exécuter la requête d'insertion
    $stmt = $db->prepare('INSERT INTO Photo (id_utilisateur, id_galerie, photo_data) VALUES (?, ?, ?)');
    $stmt->bindParam(1, $id_utilisateur, PDO::PARAM_INT);
    $stmt->bindParam(2, $id_galerie, PDO::PARAM_INT);
    $stmt->bindParam(3, $photo_data, PDO::PARAM_LOB);

    // Vérifier si l'insertion a réussi
    if ($stmt->execute()) {
        $response->getBody()->write('Photo téléchargée avec succès');
        return $response->withStatus(200);
    } else {
        $response->getBody()->write('Échec du téléchargement de la photo');
        return $response->withStatus(500);
    }
});

// Route pour vérifier le nom d'utilisateur et le mot de passe
$app->post('/login', function (Request $request, Response $response) {
    // Récupérer les données du corps de la requête
    $data = $request->getParsedBody();
    $name = $data['Name'] ?? null;
    $password = $data['password'] ?? null;

    // Vérifier la présence des champs requis
    if (!$name || !$password) {
        $response->getBody()->write('Le nom et le mot de passe sont requis');
        return $response->withStatus(400);
    }

    // Récupérer la connexion à la base de données
    $db = $this->get('db');

    // Préparer et exécuter la requête de sélection
    $stmt = $db->prepare('SELECT id_utilisateur, password FROM Utilisateur WHERE Name = ?');
    $stmt->bindParam(1, $name, PDO::PARAM_STR);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    // Vérifier si l'utilisateur existe et si le mot de passe est correct
    if ($result) {
        $storedPassword = $result['password'];

        // Vérifier le mot de passe haché
        if (password_verify($password, $storedPassword)) {
            $responseFactory = new ResponseFactory();
            $jsonResponse = $responseFactory->createResponse();
            $jsonResponse->getBody()->write(json_encode(['id_utilisateur' => $result['id_utilisateur']]));
            return $jsonResponse->withStatus(200)->withHeader('Content-Type', 'application/json');
        } else {
            $response->getBody()->write('Nom ou mot de passe incorrect');
            return $response->withStatus(401);
        }
    } else {
        $response->getBody()->write('Nom ou mot de passe incorrect');
        return $response->withStatus(401);
    }
});

// Route pour récupérer les photos d'un utilisateur spécifique
$app->get('/utilisateurs/{idUtilisateur}/photos', function (Request $request, Response $response, $args) {
    $idUtilisateur = $args['idUtilisateur'];

    // Récupérer la connexion à la base de données
    $db = $this->get('db');

    // Requête SQL pour sélectionner les photos d'un utilisateur
    $sql = 'SELECT photo_data FROM Photo WHERE id_utilisateur = :id_utilisateur';

    $stmt = $db->prepare($sql);
    $stmt->bindParam(':id_utilisateur', $idUtilisateur, PDO::PARAM_INT);
    $stmt->execute();

    // Récupérer les résultats
    $photos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Vérifier si des photos ont été trouvées
    if ($photos) {
        // Encoder les images en Base64
        foreach ($photos as &$photo) {
            $photo['photo_data'] = base64_encode($photo['photo_data']);
        }

        // Configurer la réponse en JSON avec le bon encodage
        $response = $response->withHeader('Content-Type', 'application/json');
        $response->getBody()->write(json_encode($photos));
    } else {
        $response = $response->withStatus(404);
        $response->getBody()->write('Aucune photo trouvée pour l\'utilisateur ' . $idUtilisateur);
    }

    return $response;
});

// Exécuter l'application
$app->run();