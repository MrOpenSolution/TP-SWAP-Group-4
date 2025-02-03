<?php
session_start();
if (!isset($_SESSION['role'])) { die("Access denied. Please log in."); }
require 'config.php'; // Database connection file

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if (!empty($username) && !empty($password)) {
        $stmt = $conn->prepare("SELECT user_id, username, password_hash, role FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();

            // Debugging: Check stored hash before verifying password
            echo "Stored Hash: " . $user['password_hash'] . "<br>";

            if (password_verify($password, $user['password_hash'])) {
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];                

                // Debugging: Print session data
                echo "Session Set: " . print_r($_SESSION, true) . "<br>";

                // ✅ Redirect based on role
                if ($user['role'] === 'Admin') {
                    header("Location: crudd.php");
                } elseif ($user['role'] === 'Procurement Officer') {
                    header("Location: crudd.php");
                } elseif ($user['role'] === 'Department Head' || $user['role'] === 'Head') { 
                    header("Location: crudd.php");
                } else {
                    die("❌ Invalid Role Detected: " . htmlspecialchars($user['role']));
                }
                exit();
            } else {
                echo "<p style='color:red;'>Invalid password. Debug: Entered password does not match stored hash.</p>";
            }
        } else {
            echo "<p style='color:red;'>No user found with that username.</p>";
        }
        $stmt->close();
    } else {
        echo "<p style='color:red;'>Please fill in all fields.</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Secure AMC</title>
</head>
<body>
    <h2>Login</h2>
    <form method="POST" action="login.php">
        <label>Username:</label>
        <input type="text" name="username" required>
        <label>Password:</label>
        <input type="password" name="password" required>
        <button type="submit">Login</button>
    </form>
</body>
</html>




