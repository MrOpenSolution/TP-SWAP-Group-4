<?php
session_start();

// Redirect to login page if not logged in
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] !== 'Department Head' && $_SESSION['role'] !== 'Head')) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Department Head Dashboard</title>
</head>
<body>
    <h2>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?> (<?php echo htmlspecialchars($_SESSION['role']); ?>)</h2>

    <p><strong>Select an option:</strong></p>
    <ul>
        <li><a href="view_head_orders.php">View Department Procurement Orders</a></li>
        <li><a href="request_order.php">Request New Purchase Order</a></li>
        <li><a href="logout.php">Logout</a></li>
    </ul>
</body>
</html>




