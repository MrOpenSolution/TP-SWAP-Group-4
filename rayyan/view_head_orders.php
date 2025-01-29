<?php
session_start();
require 'config.php';

// Ensure user is logged in as Department Head
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Department Head') {
    header("Location: login.php");
    exit();
}

// Fetch orders requested by the Department Head
$dept_head_id = $_SESSION['user_id'];
$result = $conn->query("SELECT * FROM purchase_orders WHERE requested_by = $dept_head_id");

if ($result->num_rows > 0) {
    echo "<h2>Department Procurement Orders</h2>";
    echo "<table border='1'><tr><th>Order ID</th><th>Vendor</th><th>Items</th><th>Status</th><th>Created At</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>{$row['order_id']}</td>
                <td>{$row['vendor_id']}</td>
                <td>{$row['items']}</td>
                <td>{$row['status']}</td>
                <td>{$row['created_at']}</td>
              </tr>";
    }
    echo "</table>";
} else {
    echo "No procurement orders found.";
}
?>
<a href="head_dashboard.php">Back to Dashboard</a>








