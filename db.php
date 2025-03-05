<?php

// Database connection details
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "learn";

// Create a connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}



?>