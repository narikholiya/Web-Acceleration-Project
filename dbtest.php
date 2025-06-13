<?php

// Database connection settings
$servername = "172.31.12.84";
$username = "love";
$password = "Indian@123";
$database = "myDatabase";

// Create a connection to the database
$pdo = new PDO("mysql:host=$servername;dbname=$database", $username, $password);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

?>

