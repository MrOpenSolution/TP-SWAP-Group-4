<?php
session_start();

// Check if the logged-in user is an officer
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'officer') {
    header("Location: login.php");  // Redirect to login page if no valid session or not officer
    exit();
}

$username = $_SESSION['username'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Officer Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.1.3/css/bootstrap.min.css">
    <style>
        body {
            background: url('background.jpg') no-repeat center center fixed;
            background-size: cover;
            font-family: Arial, sans-serif;
        }

        .dashboard-container {
            text-align: center;
            background-color: rgba(0, 0, 0, 0.75);
            color: white;
            padding: 40px;
            border-radius: 15px;
            width: 50%;
            margin: 100px auto;
        }

        .dashboard-container h2 {
            font-size: 32px;
            margin-bottom: 20px;
        }

        .btn-custom {
            width: 250px;
            padding: 15px;
            margin: 10px;
            font-size: 18px;
        }

        .btn-logout {
            background-color: #dc3545;
            color: white;
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <h2>Welcome, <?php echo $username; ?> (Procurement Officer)</h2>
        
        <a href="manage_orders.php" class="btn btn-primary btn-custom">Manage Purchase Orders</a>
        <a href="manage_vendors.php" class="btn btn-primary btn-custom">Manage Vendors</a>
        <a href="manage_inventory.php" class="btn btn-primary btn-custom">Manage Inventory</a>
        <a href="logout.php" class="btn btn-logout btn-custom">Logout</a>
    </div>
</body>
</html>




