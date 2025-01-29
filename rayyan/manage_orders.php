<?php
session_start();
require 'config.php';

// Ensure only Admins or Procurement Officers can access
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['Admin', 'Procurement Officer'])) {
    header("Location: login.php");
    exit();
}

// Fetch Purchase Orders
$result = $conn->query("
    SELECT purchase_orders.order_id, users.username AS requested_by, vendors.name AS vendor_name, purchase_orders.items, purchase_orders.status
    FROM purchase_orders
    JOIN users ON purchase_orders.requested_by = users.user_id
    JOIN vendors ON purchase_orders.vendor_id = vendors.vendor_id
");

// Fetch Vendors for Order Creation
$vendors = $conn->query("SELECT * FROM vendors");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Orders</title>
</head>
<body>
    <h1>Manage Orders</h1>
    <a href="admin_dashboard.php">Back to Dashboard</a>

    <h2>Existing Orders</h2>
    <table border="1">
        <tr>
            <th>Order ID</th>
            <th>Requested By</th>
            <th>Vendor</th>
            <th>Items</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= $row['order_id'] ?></td>
                <td><?= htmlspecialchars($row['requested_by']) ?></td>
                <td><?= htmlspecialchars($row['vendor_name']) ?></td>
                <td><?= htmlspecialchars($row['items']) ?></td>
                <td><?= $row['status'] ?></td>
                <td>
                    <form method="POST" action="update_order.php" style="display:inline;">
                        <input type="hidden" name="order_id" value="<?= $row['order_id'] ?>">
                        <select name="status">
                            <option value="Pending" <?= $row['status'] === 'Pending' ? 'selected' : '' ?>>Pending</option>
                            <option value="Approved" <?= $row['status'] === 'Approved' ? 'selected' : '' ?>>Approved</option>
                            <option value="Completed" <?= $row['status'] === 'Completed' ? 'selected' : '' ?>>Completed</option>
                        </select>
                        <button type="submit">Update</button>
                    </form>
                    <?php if ($row['status'] === 'Pending'): ?>
                        | <a href="delete_order.php?id=<?= $row['order_id'] ?>" onclick="return confirm('Are you sure?');">Delete</a>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>

