<?php
session_start();
include_once '../common/db_conn.php';  // Include database configuration

// Initialize error message
$error = '';

// Handle login request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    
    if (!empty($username) && !empty($password)) {
        $stmt = $conn->prepare("SELECT user_id, password_hash, role FROM USERS WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();
        
        if ($stmt->num_rows === 1) {
            $stmt->bind_result($user_id, $hashed_password, $role);
            $stmt->fetch();
            
            if (password_verify($password, $hashed_password)) {
                // Valid login
                $_SESSION['user_id'] = $user_id;
                $_SESSION['username'] = $username;
                $_SESSION['role'] = $role;
                /*
                // Redirect based on role
                if ($role === 'Admin') {
                    header("Location: admin_dashboard.php");
                } elseif ($role === 'Procurement Officer') {
                    header("Location: officer_dashboard.php");
                } elseif ($role === 'Department Head') {
                    header("Location: head_dashboard.php");
                }
                */
                exit();
            } else {
                $error = "Invalid password.";
            }
        } else {
            $error = "Invalid username.";
        }
        $stmt->close();
    } else {
        $error = "Please fill in both fields.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Secure AMC</title>
    <style>
        body {
            background: url('../img/login.png') no-repeat center center fixed;
            background-size: cover;
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }

        .login-container {
            width: 100%;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .login-box {
            background-color: rgba(0, 0, 0, 0.7);
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.5);
            width: 400px;
            text-align: center;
            color: white;
        }

        .login-box img {
            width: 400px;
            margin-bottom: -40px;
        }

        .login-box h2 {
            margin-bottom: 10px;
            color: #ffffff;
        }

        .login-box p {
            margin-bottom: 25px;
            color: #cccccc;
        }

        .login-box input {
            width: 100%;
            padding: 12px;
            margin: 8px 0;
            border: 1px solid #444;
            border-radius: 5px;
            font-size: 16px;
            background-color: #333;
            color: white;
        }

        .login-box input::placeholder {
            color: #bbbbbb;
        }

        .login-box button {
            width: 100%;
            padding: 12px;
            border: none;
            background-color: #0066cc;
            color: white;
            font-size: 18px;
            border-radius: 5px;
            cursor: pointer;
        }

        .login-box button:hover {
            background-color: #004999;
        }

        .error {
            color: #ff4d4d;
            font-size: 14px;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>

<div class="login-container">
    <div class="login-box">
        <img src="../img/temasek_logo.png" alt="Temasek Logo">
        <h2>Sign in</h2>
        <p>to continue to Dashboard</p>
        <?php if ($error) echo "<p class='error'>$error</p>"; ?>
        <form method="POST" action="">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Sign In</button>
        </form>
    </div>
</div>

</body>
</html>
