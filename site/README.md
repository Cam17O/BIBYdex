mld 
```SQL
CREATE TABLE Utilisateur (
    id_utilisateur INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(50),
    Name VARCHAR(50),
    password VARCHAR(50)
);

CREATE TABLE Galerie (
    id_galerie INT AUTO_INCREMENT PRIMARY KEY,
    id_utilisateur INT,
    FOREIGN KEY (id_utilisateur) REFERENCES Utilisateur(id_utilisateur)
);

CREATE TABLE Photo (
    id_photo INT AUTO_INCREMENT PRIMARY KEY,
    id_utilisateur INT,
    id_galerie INT,
    FOREIGN KEY (id_utilisateur) REFERENCES Utilisateur(id_utilisateur),
    FOREIGN KEY (id_galerie) REFERENCES Galerie(id_galerie)
);

CREATE TABLE Amis (
    id_utilisateur INT,
    id_ami INT,
    FOREIGN KEY (id_utilisateur) REFERENCES Utilisateur(id_utilisateur),
    FOREIGN KEY (id_ami) REFERENCES Utilisateur(id_utilisateur)
);
```