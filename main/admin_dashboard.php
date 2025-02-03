<?php
session_start();

// Check if user is logged in and is an Admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
    header("Location: login.php"); // Redirect to login if not an admin
    exit();
}
$username = $_SESSION['username']; // Get admin's username
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Secure AMC</title>
    <link rel="stylesheet" href="styles.css"> <!-- Link to external CSS -->
</head>
<body>
    <div class="dashboard-container">
        <h1>Welcome, <?php echo htmlspecialchars($username); ?> (Admin)</h1>
        <nav>
            <ul>
                <li><a href="user_management/manage_users.php">Manage Users</a></li>
                <li><a href="manage_vendors.php">Manage Vendors</a></li>
                <li><a href="manage_orders.php">Manage Purchase Orders</a></li>
                <li><a href="auth/logout.php">Logout</a></li>
            </ul>
        </nav>
    </div>
</body>
</html>
