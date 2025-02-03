<?php
// Database connection
$host = 'DB';
$user = 'root';
$password = 'password';
$dbname = 'APP';

$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
