<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set up your database connection
$servername = "localhost:3306";
$username = "root";
$password = "aluno";
$database = "s4p";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$database", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    die();
}

// Retrieve the JSON parameter from the POST request
$inputFile = file_get_contents('php://input');
if ($data = json_decode($inputFile, true)) {

    if (json_last_error() === JSON_ERROR_NONE) {
        // Validate and sanitize data here (e.g., using filter_var)

        // Insert data into the 'animal' table using prepared statements
        $stmt = $conn->prepare("INSERT INTO animal (nmanimal, nmtutor, idcor, idporte) VALUES (:nmanimal, :nmtutor, :idcor, :idporte)");
        $stmt->bindParam(':nmanimal', $data['nmanimal']);
        $stmt->bindParam(':nmtutor', $data['nmtutor']);
        $stmt->bindParam(':idcor', $data['idcor'], PDO::PARAM_INT);
        $stmt->bindParam(':idporte', $data['idporte'], PDO::PARAM_INT);

        if ($stmt->execute()) {
            $response = [
                "success" => true,
                "message" => "Data saved successfully."
            ];
        } else {
            $response = [
                "success" => false,
                "message" => "Failed to save data."
            ];
        }
    } else {
        $response = [
            "success" => false,
            "message" => "Invalid JSON format."
        ];
    }
} else {
    $response = [
        "success" => false,
        "message" => "Input JSON file not found."
    ];
}

// Return the JSON response
header('Content-Type: application/json');
echo json_encode($response, JSON_PRETTY_PRINT);

// Close the database connection
$conn = null;
?>
