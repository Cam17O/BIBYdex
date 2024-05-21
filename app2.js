const express = require('express');
const multer = require('multer');
const mysql = require('mysql2');
const bodyParser = require('body-parser');
const app = express();
const port = 3000;

// Configurer multer pour le stockage des fichiers
const storage = multer.memoryStorage();
const upload = multer({ storage: storage });

// Configurer la connexion à la base de données
const db = mysql.createConnection({
    host: 'database',
    user: 'paugetc',
    password: 'Capa1677',
    database: 'BIBYdex'
});

db.connect((err) => {
    if (err) {
        console.error('Error connecting to the database:', err);
        process.exit(1);
    }
    console.log('Connected to MySQL database');
});

// Middleware
app.use(bodyParser.json());
app.use(bodyParser.urlencoded({ extended: true }));

// Route pour télécharger une photo
app.post('/upload', upload.single('photo'), (req, res) => {
    const { id_utilisateur, id_galerie } = req.body;
    const photo_data = req.file.buffer;

    if (!id_utilisateur || !id_galerie || !photo_data) {
        return res.status(400).send('Missing required fields');
    }

    const query = 'INSERT INTO Photo (id_utilisateur, id_galerie, photo_data) VALUES (?, ?, ?)';
    db.query(query, [id_utilisateur, id_galerie, photo_data], (err, results) => {
        if (err) {
            console.error(err);
            return res.status(500).send('Failed to upload photo');
        }
        res.status(200).send('Photo uploaded successfully');
    });
});

// Route pour vérifier le pseudo et le mot de passe
app.post('/login', (req, res) => {
    const { name, password } = req.body;

    // Retrieve the user from the database
    const query = 'SELECT id_utilisateur, password FROM Utilisateur WHERE Name = ?';
    db.query(query, [name], (err, results) => {
        if (err) {
            console.error('Error fetching user:', err);
            return res.status(500).send('Server error');
        }

        if (results.length === 0) {
            return res.status(401).send('Incorrect pseudo or password');
        }

        const user = results[0];

        // Verify the password
        bcrypt.compare(password, user.password, (err, result) => {
            if (err) {
                console.error('Error verifying password:', err);
                return res.status(500).send('Server error');
            }

            if (result) {
                // Password is correct, send user ID
                res.status(200).json({ id_utilisateur: user.id_utilisateur });
            } else {
                // Password is incorrect
                res.status(401).send('Incorrect pseudo or password');
            }
        });
    });
});

app.listen(port, () => {
    console.log(`Server is running on port ${port}`);
});