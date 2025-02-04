<?php
// Database connection function
function getconn() {
    $host = 'localhost';
    $user = 'root';
    $password = 'password';
    $dbname = 'APP';

    // Create a new mysqli connection
    $conn = new mysqli($host, $user, $password, $dbname);

    // Check for connection errors
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    return $conn;
}

// For legacy usage
$conn = getconn();
