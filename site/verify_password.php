<?php
header("Content-Type: application/json");

// Database connection
require("connexionDB.php");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $input = json_decode(file_get_contents("php://input"), true);
    $p_Name = $input["Name"];
    $p_password = $input["password"];

    if (empty($p_Name) || empty($p_password)) {
        echo json_encode(["success" => false, "message" => "Name and password are required"]);
        http_response_code(400);
        exit();
    }

    // Query to get user by name
    $stmt = $pdo->prepare("SELECT id_utilisateur, password FROM Utilisateur WHERE Name = :Name");
    $stmt->execute([':Name' => $p_Name]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        // Verify the password
        if (password_verify($p_password, $user["password"])) {
            echo json_encode(["success" => true, "id_utilisateur" => $user["id_utilisateur"]]);
        } else {
            echo json_encode(["success" => false, "message" => "Incorrect Name or password"]);
            http_response_code(401);
        }
    } else {
        echo json_encode(["success" => false, "message" => "Incorrect Name or password"]);
        http_response_code(401);
    }
} else {
    echo json_encode(["success" => false, "message" => "Invalid request method"]);
    http_response_code(405);
}
?>
