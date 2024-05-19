const express = require('express');
const bodyParser = require('body-parser');
const mysql = require('mysql2');

// Initialize Express app
const app = express();
app.use(bodyParser.json());

// Create MySQL connection
const connection = mysql.createConnection({
    host: 'database',
    user: 'paugetc',
    password: 'Capa1677',
    database: 'BIBYdex'
});

connection.connect(err => {
    if (err) throw err;
    console.log('Connected to the database');
});

// Routes to get all and one item from each table
app.get('/utilisateurs', (req, res) => {
    connection.query('SELECT * FROM Utilisateur', (err, result) => {
        if (err) throw err;
        res.json(result);
    });
});

app.get('/utilisateur/:id', (req, res) => {
    const { id } = req.params;
    connection.query('SELECT * FROM Utilisateur WHERE id_utilisateur = ?', [id], (err, result) => {
        if (err) throw err;
        res.json(result[0]);
    });
});

app.get('/galeries', (req, res) => {
    connection.query('SELECT * FROM Galerie', (err, result) => {
        if (err) throw err;
        res.json(result);
    });
});

app.get('/galerie/:id', (req, res) => {
    const { id } = req.params;
    connection.query('SELECT * FROM Galerie WHERE id_galerie = ?', [id], (err, result) => {
        if (err) throw err;
        res.json(result[0]);
    });
});

app.get('/photos', (req, res) => {
    connection.query('SELECT * FROM Photo', (err, result) => {
        if (err) throw err;
        res.json(result);
    });
});

app.get('/photo/:id', (req, res) => {
    const { id } = req.params;
    connection.query('SELECT * FROM Photo WHERE id_photo = ?', [id], (err, result) => {
        if (err) throw err;
        res.json(result[0]);
    });
});

app.get('/amis', (req, res) => {
    connection.query('SELECT * FROM Amis', (err, result) => {
        if (err) throw err;
        res.json(result);
    });
});

app.get('/amis/:id', (req, res) => {
    const { id } = req.params;
    connection.query('SELECT * FROM Amis WHERE id_utilisateur = ?', [id], (err, result) => {
        if (err) throw err;
        res.json(result);
    });
});

// Routes to add an item to each table
app.post('/utilisateur', (req, res) => {
    const { email, Name, password } = req.body;
    connection.query('INSERT INTO Utilisateur (email, Name, password) VALUES (?, ?, ?)', [email, Name, password], (err, result) => {
        if (err) throw err;
        res.json({ id_utilisateur: result.insertId, email, Name, password });
    });
});

app.post('/galerie', (req, res) => {
    const { id_utilisateur } = req.body;
    connection.query('INSERT INTO Galerie (id_utilisateur) VALUES (?)', [id_utilisateur], (err, result) => {
        if (err) throw err;
        res.json({ id_galerie: result.insertId, id_utilisateur });
    });
});

app.post('/photo', (req, res) => {
    const { id_utilisateur, id_galerie, photo_data } = req.body;
    connection.query('INSERT INTO Photo (id_utilisateur, id_galerie, photo_data) VALUES (?, ?, ?)', [id_utilisateur, id_galerie, photo_data], (err, result) => {
        if (err) throw err;
        res.json({ id_photo: result.insertId, id_utilisateur, id_galerie, photo_data });
    });
});

app.post('/amis', (req, res) => {
    const { id_utilisateur, id_ami } = req.body;
    connection.query('INSERT INTO Amis (id_utilisateur, id_ami) VALUES (?, ?)', [id_utilisateur, id_ami], (err, result) => {
        if (err) throw err;
        res.json({ id_utilisateur, id_ami });
    });
});

// Routes to delete an item from each table
app.delete('/utilisateur/:id', (req, res) => {
    const { id } = req.params;
    connection.query('DELETE FROM Utilisateur WHERE id_utilisateur = ?', [id], (err, result) => {
        if (err) throw err;
        res.sendStatus(204);
    });
});

app.delete('/galerie/:id', (req, res) => {
    const { id } = req.params;
    connection.query('DELETE FROM Galerie WHERE id_galerie = ?', [id], (err, result) => {
        if (err) throw err;
        res.sendStatus(204);
    });
});

app.delete('/photo/:id', (req, res) => {
    const { id } = req.params;
    connection.query('DELETE FROM Photo WHERE id_photo = ?', [id], (err, result) => {
        if (err) throw err;
        res.sendStatus(204);
    });
});

app.delete('/amis/:id_utilisateur/:id_ami', (req, res) => {
    const { id_utilisateur, id_ami } = req.params;
    connection.query('DELETE FROM Amis WHERE id_utilisateur = ? AND id_ami = ?', [id_utilisateur, id_ami], (err, result) => {
        if (err) throw err;
        res.sendStatus(204);
    });
});

// Start server
app.listen(3000, () => {
    console.log('Server is running on port 3000');
});