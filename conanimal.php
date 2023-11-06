<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set up your database connection
$servername = "200.98.129.120:3306";
$username = "marcosvir_e4p";
$password = "&squadrao4p2023";
$database = "marcosvir_e4p";

// Add the following lines to set CORS headers
header("Access-Control-Allow-Origin: *"); // Allow requests from any origin (you can restrict this in a production environment)
header("Access-Control-Allow-Methods: POST, GET"); // Allow POST and GET requests
header("Access-Control-Allow-Headers: Content-Type"); // Allow Content-Type header

$con = new mysqli($servername, $username, $password, $database);

if ($con->connect_error) {
    die("Connection failed: " . $con->connect_error);
}

// Retrieve the request parameter
$stringParam = file_get_contents('php://input');

// Convert to JSON parameter
$jsonParamRequest = json_decode($stringParam, true);

// Checking if it's a JSON array
if ($stringParam[0] == '[') {
    $jsonParam = $jsonParamRequest[0]; // Take the first object of the JSON array as the filter
} else {
    $jsonParam = $jsonParamRequest; // Keep what was received if it's a JSON object
}

$json = array();// Create a response array

if (!empty($jsonParam)) {
    // Prepare the WHERE clause
    $whereClause = ' WHERE ';
    foreach ($jsonParam as $field => $value) {
        if ($value != '' && $value != '0') {
            $whereClause .= "$field = '$value' AND ";
        }
    }
    $whereClause = rtrim($whereClause, ' AND ');

    // Prepare the SQL statement for selecting data from the 'animal' table
    $consulta = "SELECT idanimal, nmanimal, nmtutor, idcor, idporte FROM animal $whereClause";

    // Set the content type to JSON
    header('Content-Type: application/json');

    // Output the JSON data

    //echo $consulta;

    $result = $con->query($consulta);

    if ($result) {
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                    $json[] = $row;
            }
        } 
    }
} 
$result->free_result();
$con->close();
// Send the JSON response
header('Content-Type: application/json; charset=utf-8');
echo json_encode($json);

?>
