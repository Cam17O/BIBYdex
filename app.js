const express = require('express');
const multer = require('multer');
const mysql = require('mysql2');
const bodyParser = require('body-parser');
const bcrypt = require('bcrypt');
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

// Route pour vérifier le nom d'utilisateur et le mot de passe
app.post('/login', (req, res) => {
    const { Name, password } = req.body;

    if (!Name || !password) {
        return res.status(400).send('Name and password are required');
    }

    // Hachage du mot de passe
    bcrypt.hash(password, 10, async (err, hashedPassword) => {
        if (err) {
            console.error('Error hashing password:', err);
            return res.status(500).send('Failed to hash password');
        }

        // Affiche le mot de passe envoyé par l'utilisateur après cryptage
        console.log('Password sent by user (after hashing):', hashedPassword);

        const query = 'SELECT id_utilisateur, password FROM Utilisateur WHERE Name = ?';
        db.query(query, [Name], async (err, results) => {
            if (err) {
                console.error('Error querying the database:', err);
                return res.status(500).send('Failed to query the database');
            }

            if (results.length > 0) {
                const user = results[0];
                console.log('Retrieved user from database:', user);

                // Comparaison asynchrone des mots de passe hachés
                bcrypt.compare(password, user.password, (err, passwordMatch) => {
                    if (err) {
                        console.error('Error comparing passwords:', err);
                        return res.status(500).send('Failed to compare passwords');
                    }

                    if (passwordMatch) {
                        console.log(`User ${Name} logged in successfully`);
                        res.status(200).json({ id_utilisateur: user.id_utilisateur });
                    } else {
                        console.log(`Incorrect password for user ${Name}`);
                        res.status(401).send('Incorrect Name or password');
                    }
                });
            } else {
                console.log(`User ${Name} not found`);
                res.status(401).send('Incorrect Name or password');
            }
        });
    });
});

app.listen(port, () => {
    console.log(`Server is running on port ${port}`);
});