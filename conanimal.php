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
header("Access-Control-Allow-Methods: POST"); // Allow POST requests
header("Access-Control-Allow-Methods: GET"); // Allow POST requests
header("Access-Control-Allow-Headers: Content-Type"); // Allow Content-Type header

$con = new mysqli($servername, $username, $password, $database);

if ($con->connect_error) {
    die("Connection failed: " . $con->connect_error);
}

//Retrieve the request parameter
$stringParam = file_get_contents('php://input');
//convert to JSON parameter
$jsonParam = json_decode($stringParam, true);
//vendo se veio array json
if ($stringParam[0] == '['){
    //pegando o primeiro objeto do json array - filtro da consulta
    $jsonParam = $jsonParam[0];
} 

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

    $result = $con->query($consulta);

    $json = array();

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            // Convert character encoding for each field
            foreach ($row as &$value) {
                $value = mb_convert_encoding($value, 'UTF-8', 'ISO-8859-1');
            }

            $animal = array(
                "idanimal" => $row['idanimal'],
                "nmanimal" => $row['nmanimal'],
                "nmtutor" => $row['nmtutor'],
                "idcor" => $row['idcor'],
                "idporte" => $row['idporte']
            );
            $json[] = $animal;
        }
    } else {
        $animal = array(
            "idanimal" => 0,
            "nmanimal" => "",
            "nmtutor" => "",
            "idcor" => 0,
            "idporte" => 0
        );
        $json[] = $animal;
    }

    if ($json) {
        $encoded_json = json_encode($json);
        if ($encoded_json === false) {
            echo "Error encoding JSON: " . json_last_error_msg();
        } else {
            header('Content-Type: application/json; charset=utf-8');
            echo $encoded_json;
        }
    } else {
        echo "Empty JSON data.";
    }

    $result->free_result();
}

$con->close();

?>
