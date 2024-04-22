<?php

require("connexionDB.php");

$stmt = $pdo->prepare("SELECT id_utilisateur,email,Name FROM Utilisateur");
$stmt->execute();

// Fetch all rows as an associative array
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($users as $user) {
    echo "<tr>";
    echo "<th scope='row'>" . $user['id_utilisateur'] . "</td>";
    echo "<td>" . $user['email'] . "</td>";
    echo "<td>" . $user['Name'] . "</td>";
    echo "</tr>";
}
