<?php
session_start();

// Check if user is logged in and is an Admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
    header("Location: auth/login.php"); // Redirect to login if not an admin
    exit();
}

$username = $_SESSION['username']; // Get admin's username
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <style>
        body {
            background: url('img/dashboard.jpg') no-repeat center center fixed;
            background-size: cover;
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }

        .dashboard-container {
            width: 100%;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: rgba(0, 0, 0, 0.5);
        }

        .dashboard-box {
            background-color: rgba(0, 0, 0, 0.8);
            padding: 40px;
            border-radius: 15px;
            width: 500px;
            box-shadow: 0px 10px 20px rgba(0, 0, 0, 0.5);
            text-align: center;
        }

        .dashboard-box h1 {
            color: #ffffff;
            font-size: 28px;
            margin-bottom: 20px;
        }

        .grid-container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-top: 20px;
        }

        .grid-item {
            padding: 12px;
            background-color: #007bff;
            color: white;
            border-radius: 8px;
            text-decoration: none;
            text-align: center;
            font-size: 18px;
            font-weight: bold;
            transition: background-color 0.3s, transform 0.2s;
        }

        .grid-item:hover {
            background-color: #0056b3;
            transform: translateY(-3px);
        }

        .logout {
            grid-column: 1 / span 2;
            background-color: #dc3545;
        }

        .logout:hover {
            background-color: #c82333;
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
    <div class="dashboard-box">
        <h1>Welcome, <?php echo $username; ?></h1>
        <div class="grid-container">
        <a href="user_management/manage_users.php" class="grid-item">Users</a>
        <a href="vendors" class="grid-item">Vendors</a>
        <a href="orders" class="grid-item">Orders</a>
        <a href="inventory" class = "grid-item">Inventory</a></li>
        <a href="requests" class = "grid-item">Requests</a></li>
        <a href="auth/logout.php" class = "grid-item logout">Logout</a></li>
            </ul>
        </nav>
    </div>
</body>
</html>
