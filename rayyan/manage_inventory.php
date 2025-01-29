<?php
session_start();
require 'config.php';

// Ensure only Admins or Procurement Officers can access
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['Admin', 'Procurement Officer'])) {
    header("Location: login.php");
    exit();
}

// Handle Add Inventory Item
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_item'])) {
    $item_name = $_POST['item_name'];
    $description = $_POST['description'];
    $quantity = intval($_POST['quantity']);
    $updated_by = $_SESSION['user_id'];

    $stmt = $conn->prepare("INSERT INTO inventory (item_name, description, quantity, updated_by) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssii", $item_name, $description, $quantity, $updated_by);
    $stmt->execute();
    header("Location: manage_inventory.php");
    exit();
}

// Handle Update Inventory Item
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_item'])) {
    $inventory_id = intval($_POST['inventory_id']);
    $quantity = intval($_POST['quantity']);
    $updated_by = $_SESSION['user_id'];

    $stmt = $conn->prepare("UPDATE inventory SET quantity = ?, updated_by = ? WHERE inventory_id = ?");
    $stmt->bind_param("iii", $quantity, $updated_by, $inventory_id);
    $stmt->execute();
    header("Location: manage_inventory.php");
    exit();
}

// Handle Delete Inventory Item
if (isset($_GET['delete'])) {
    $inventory_id = intval($_GET['delete']);
    $conn->query("DELETE FROM inventory WHERE inventory_id = $inventory_id");
    header("Location: manage_inventory.php");
    exit();
}

// Fetch Inventory Items
$result = $conn->query("
    SELECT inventory.*, users.username AS updated_by_user 
    FROM inventory 
    LEFT JOIN users ON inventory.updated_by = users.user_id
");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Inventory</title>
</head>
<body>
    <h1>Manage Inventory</h1>
    <a href="officer_dashboard.php">Back to Dashboard</a>

    <h2>Add New Item</h2>
    <form method="POST">
        <input type="text" name="item_name" placeholder="Item Name" required>
        <input type="text" name="description" placeholder="Description">
        <input type="number" name="quantity" placeholder="Quantity" required>
        <button type="submit" name="add_item">Add Item</button>
    </form>

    <h2>Inventory List</h2>
    <table border="1">
        <tr>
            <th>ID</th>
            <th>Item Name</th>
            <th>Description</th>
            <th>Quantity</th>
            <th>Last Updated By</th>
            <th>Last Updated At</th>
            <th>Actions</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= $row['inventory_id'] ?></td>
                <td><?= htmlspecialchars($row['item_name']) ?></td>
                <td><?= htmlspecialchars($row['description']) ?></td>
                <td><?= $row['quantity'] ?></td>
                <td><?= htmlspecialchars($row['updated_by_user'] ?? 'Unknown') ?></td>
                <td><?= $row['updated_at'] ?></td>
                <td>
                    <form method="POST" style="display:inline;">
                        <input type="hidden" name="inventory_id" value="<?= $row['inventory_id'] ?>">
                        <input type="number" name="quantity" value="<?= $row['quantity'] ?>" required>
                        <button type="submit" name="update_item">Update</button>
                    </form>
                    | <a href="manage_inventory.php?delete=<?= $row['inventory_id'] ?>" onclick="return confirm('Are you sure?');">Delete</a>
                </td>
            </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>
