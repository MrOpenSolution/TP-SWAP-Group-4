<?php
// Database connection
$host = 'localhost';
$user = 'root';
$password = '';
$dbname = 'swap_secure_amc';

$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Ensure session is not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
