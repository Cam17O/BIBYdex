<?php

// Connexion à la base de données
$servername = 'database';
$dbname = 'BIBYdex';
$username = 'paugetc';
$password = 'Capa1677';

// On établit la connexion
try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (Exception $e) {
    die('Erreur : ' . $e->getMessage());
}
