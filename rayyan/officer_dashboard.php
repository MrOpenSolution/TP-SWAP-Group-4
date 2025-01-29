<?php
session_start();
require 'config.php';

// Ensure only Admins or Procurement Officers can access
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['Admin', 'Procurement Officer'])) {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username']; // Get officer's username
?>

<!DOCTYPE html>
<html>
<head>
    <title>Procurement Officer Dashboard</title>
</head>
<body>
    <h1>Welcome, <?= htmlspecialchars($username) ?> (<?= $_SESSION['role'] ?>)</h1>
    <p>Select an option:</p>
    <ul>
        <li><a href="manage_orders.php">Manage Purchase Orders</a></li>
        <li><a href="manage_vendors.php">Manage Vendors</a></li>
        <li><a href="manage_inventory.php">Manage Inventory</a></li>
        <li><a href="logout.php">Logout</a></li>
    </ul>
</body>
</html>

