mld :

```SQL

CREATE TABLE Utilisateur (
    id_utilisateur INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) UNIQUE,
    Name VARCHAR(50),
    password VARCHAR(255) -- Assuming hashed passwords
);

CREATE TABLE Galerie (
    id_galerie INT AUTO_INCREMENT PRIMARY KEY,
    id_utilisateur INT,
    FOREIGN KEY (id_utilisateur) REFERENCES Utilisateur(id_utilisateur) ON DELETE CASCADE
);

CREATE TABLE Photo (
    id_photo INT AUTO_INCREMENT PRIMARY KEY,
    id_utilisateur INT,
    id_galerie INT,
    photo_data LONGBLOB,
    FOREIGN KEY (id_utilisateur) REFERENCES Utilisateur(id_utilisateur) ON DELETE CASCADE,
    FOREIGN KEY (id_galerie) REFERENCES Galerie(id_galerie) ON DELETE CASCADE
);

CREATE TABLE Amis (
    id_utilisateur INT,
    id_ami INT,
    PRIMARY KEY (id_utilisateur, id_ami),
    FOREIGN KEY (id_utilisateur) REFERENCES Utilisateur(id_utilisateur) ON DELETE CASCADE,
    FOREIGN KEY (id_ami) REFERENCES Utilisateur(id_utilisateur) ON DELETE CASCADE
);

```