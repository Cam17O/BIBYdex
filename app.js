const express = require('express');
const mysql = require('mysql');
const bodyParser = require('body-parser');

// Créer une application Express
const app = express();
const port = 3000;

// Configuration de la connexion à la base de données
const connection = mysql.createConnection({
    host: 'database',
    user: 'paugetc',
    password: 'Capa1677',
    database: 'BIBYdex'
});

// Connecter la base de données
connection.connect(err => {
    if (err) throw err;
    console.log('Connecté à la base de données MySQL');
});

// Utiliser bodyParser pour analyser le corps des requêtes HTTP
app.use(bodyParser.json());
app.use(bodyParser.urlencoded({ extended: true }));

// Définir les endpoints pour l'API
//------------------------------utilisateur------------------------------//
// Récupérer tous les utilisateurs
app.get('/utilisateurs', (req, res) => {
    connection.query('SELECT * FROM Utilisateur', (err, result) => {
        if (err) throw err;
        res.json(result);
    });
});

// Récupérer un utilisateur par son ID
app.get('/utilisateur/:id', (req, res) => {
    const userId = req.params.id;
    connection.query('SELECT * FROM Utilisateur WHERE id_utilisateur = ?', userId, (err, result) => {
        if (err) throw err;
        res.json(result[0]);
    });
});

// Ajouter un nouvel utilisateur
app.post('/utilisateur', (req, res) => {
    const newUser = req.body;
    connection.query('INSERT INTO Utilisateur SET ?', newUser, (err, result) => {
        if (err) throw err;
        res.send('Utilisateur ajouté avec succès');
    });
});

// Mettre à jour un utilisateur par son ID
app.put('/utilisateur/:id', (req, res) => {
    const userId = req.params.id;
    const updatedUser = req.body;
    connection.query('UPDATE Utilisateur SET ? WHERE id_utilisateur = ?', [updatedUser, userId], (err, result) => {
        if (err) throw err;
        res.send('Utilisateur mis à jour avec succès');
    });
});

// Supprimer un utilisateur par son ID
app.delete('/utilisateur/:id', (req, res) => {
    const userId = req.params.id;
    connection.query('DELETE FROM Utilisateur WHERE id_utilisateur = ?', userId, (err, result) => {
        if (err) throw err;
        res.send('Utilisateur supprimé avec succès');
    });
});
//------------------------------galerie------------------------------//
// Récupérer toutes les galeries
app.get('/galeries', (req, res) => {
    connection.query('SELECT * FROM Galerie', (err, result) => {
        if (err) throw err;
        res.json(result);
    });
});

// Récupérer une galerie par son ID
app.get('/galerie/:id', (req, res) => {
    const galleryId = req.params.id;
    connection.query('SELECT * FROM Galerie WHERE id_galerie = ?', galleryId, (err, result) => {
        if (err) throw err;
        res.json(result[0]);
    });
});

// Ajouter une nouvelle galerie
app.post('/galerie', (req, res) => {
    const newGallery = req.body;
    connection.query('INSERT INTO Galerie SET ?', newGallery, (err, result) => {
        if (err) throw err;
        res.send('Galerie ajoutée avec succès');
    });
});

// Mettre à jour une galerie par son ID
app.put('/galerie/:id', (req, res) => {
    const galleryId = req.params.id;
    const updatedGallery = req.body;
    connection.query('UPDATE Galerie SET ? WHERE id_galerie = ?', [updatedGallery, galleryId], (err, result) => {
        if (err) throw err;
        res.send('Galerie mise à jour avec succès');
    });
});

// Supprimer une galerie par son ID
app.delete('/galerie/:id', (req, res) => {
    const galleryId = req.params.id;
    connection.query('DELETE FROM Galerie WHERE id_galerie = ?', galleryId, (err, result) => {
        if (err) throw err;
        res.send('Galerie supprimée avec succès');
    });
});

//------------------------------photo------------------------------//
// Récupérer toutes les photos
app.get('/photos', (req, res) => {
    connection.query('SELECT * FROM Photo', (err, result) => {
        if (err) throw err;
        res.json(result);
    });
});

// Récupérer une photo par son ID
app.get('/photo/:id', (req, res) => {
    const photoId = req.params.id;
    connection.query('SELECT * FROM Photo WHERE id_photo = ?', photoId, (err, result) => {
        if (err) throw err;
        res.json(result[0]);
    });
});

// Ajouter une nouvelle photo
app.post('/photo', (req, res) => {
    const newPhoto = req.body;
    connection.query('INSERT INTO Photo SET ?', newPhoto, (err, result) => {
        if (err) throw err;
        res.send('Photo ajoutée avec succès');
    });
});

// Mettre à jour une photo par son ID
app.put('/photo/:id', (req, res) => {
    const photoId = req.params.id;
    const updatedPhoto = req.body;
    connection.query('UPDATE Photo SET ? WHERE id_photo = ?', [updatedPhoto, photoId], (err, result) => {
        if (err) throw err;
        res.send('Photo mise à jour avec succès');
    });
});

// Supprimer une photo par son ID
app.delete('/photo/:id', (req, res) => {
    const photoId = req.params.id;
    connection.query('DELETE FROM Photo WHERE id_photo = ?', photoId, (err, result) => {
        if (err) throw err;
        res.send('Photo supprimée avec succès');
    });
});

//------------------------------amis------------------------------//
// Récupérer tous les amis d'un utilisateur
app.get('/amis/:id_utilisateur', (req, res) => {
    const userId = req.params.id_utilisateur;
    connection.query('SELECT * FROM Amis WHERE id_utilisateur = ?', userId, (err, result) => {
        if (err) throw err;
        res.json(result);
    });
});

// Ajouter une nouvelle relation d'amitié
app.post('/amis', (req, res) => {
    const newFriendship = req.body;
    connection.query('INSERT INTO Amis SET ?', newFriendship, (err, result) => {
        if (err) throw err;
        res.send('Relation d\'amitié ajoutée avec succès');
    });
});

// Supprimer une relation d'amitié entre deux utilisateurs
app.delete('/amis/:id_utilisateur/:id_ami', (req, res) => {
    const userId = req.params.id_utilisateur;
    const friendId = req.params.id_ami;
    connection.query('DELETE FROM Amis WHERE id_utilisateur = ? AND id_ami = ?', [userId, friendId], (err, result) => {
        if (err) throw err;
        res.send('Relation d\'amitié supprimée avec succès');
    });
});


// Démarrer le serveur
app.listen(port, () => {
    console.log(`Serveur en cours d'exécution sur le port ${port}`);
});