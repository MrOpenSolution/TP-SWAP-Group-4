<?php
// Database connection
$host = 'db';
$user = 'root';
$password = 'password';
$dbname = 'APP';

$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Ensure session is not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
