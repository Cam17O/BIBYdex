mld :

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

utilisateur test :


php mysql

Vérifiez la configuration PHP dans votre conteneur :

    Accédez à votre conteneur PHP en utilisant la commande docker-compose exec webserver bash.

    Exécutez la commande php -i | grep extension_dir pour trouver le répertoire des extensions PHP.

    Assurez-vous que le répertoire des extensions contient le fichier pdo_mysql.so. Si ce fichier est manquant, cela signifie que l'extension pdo_mysql n'est pas installée ou est mal installée dans votre conteneur PHP.

Installer l'extension pdo_mysql :

Si l'extension pdo_mysql est manquante, vous pouvez l'installer en utilisant la commande suivante dans votre conteneur PHP :

```bash
docker-php-ext-install pdo_mysql
```
Redémarrez les conteneurs Docker :

Après avoir installé l'extension pdo_mysql, redémarrez vos conteneurs Docker pour que les modifications prennent effet :

```bash

docker-compose restart
```
Après avoir suivi ces étapes, vérifiez à nouveau si l'extension pdo_mysql est chargée en exécutant la commande docker-compose exec webserver php -m | grep pdo_mysql. Vous devriez voir la sortie sans avertissement, ce qui indiquerait que l'extension est chargée avec succès.