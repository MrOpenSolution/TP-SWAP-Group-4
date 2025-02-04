<?php
session_start();
include_once "../common/db_conn.php";

// Constants for user roles
define('ROLE_ADMIN', 'Admin');
define('ROLE_PROCUREMENT_OFFICER', 'Procurement Officer');

// Redirect function
function redirect($url) {
    header("Location: $url");
    exit();
}

// Check Login
if (!isset($_SESSION['user_id'])) {
    redirect("../auth/login.php");
}

// Ensure only Admins or Procurement Officers can access
if (!in_array($_SESSION['role'], [ROLE_ADMIN, ROLE_PROCUREMENT_OFFICER])) {
    redirect("../dashboard.php");
}

// Function to sanitize input
function sanitize_input($data) {
    return htmlspecialchars(trim($data));
}

// Handle Create or Update Inventory Item
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['delete_inventory']) && $_SESSION['role'] === ROLE_ADMIN) {
        // Handle Delete Inventory Item
        $inventory_id = intval($_POST['inventory_id']);
        $stmt = $conn->prepare("DELETE FROM INVENTORY WHERE inventory_id = ?");
        $stmt->bind_param("i", $inventory_id);
        $stmt->execute();
        $stmt->close();
        redirect($_SERVER['PHP_SELF']); // Redirect to the current page
    } else {
        $item_name = sanitize_input($_POST['item_name']);
        $description = isset($_POST['description']) ? sanitize_input($_POST['description']) : null;
        $quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 0;
        $restock_level = isset($_POST['restock_level']) ? intval($_POST['restock_level']) : 0;

        $inventory_id = isset($_POST['inventory_id']) ? intval($_POST['inventory_id']) : null;

        // Validate inputs
        if (empty($item_name) || $quantity < 0 || $restock_level < 0) {
            // Handle validation error (e.g., redirect or show an error message)
            echo "<script>alert('Invalid input. Please check your data.');</script>";
        } else {
            if (isset($_POST['add_inventory']) && in_array($_SESSION['role'], [ROLE_ADMIN, ROLE_PROCUREMENT_OFFICER])) {
                $stmt = $conn->prepare("INSERT INTO INVENTORY (item_name, description, quantity, restock_level) VALUES (?, ?, ?, ?)");
                $stmt->bind_param("ssii", $item_name, $description, $quantity, $restock_level);
            } elseif (isset($_POST['update_inventory']) && in_array($_SESSION['role'], [ROLE_ADMIN, ROLE_PROCUREMENT_OFFICER])) {
                $stmt = $conn->prepare("UPDATE INVENTORY SET item_name = ?, description = ?, quantity = ?, restock_level = ? WHERE inventory_id = ?");
                $stmt->bind_param("ssiii", $item_name, $description, $quantity, $restock_level, $inventory_id);
            }

            if (isset($stmt)) {
                $stmt->execute();
                $stmt->close();
            }
            redirect($_SERVER['PHP_SELF']); // Redirect to the current page
        }
    }
}

// Get Inventory for Editing
$edit_inventory = null;
if (isset($_GET['edit'])) {
    $edit_id = intval($_GET['edit']);
    $stmt = $conn->prepare("SELECT * FROM INVENTORY WHERE inventory_id = ?");
    $stmt->bind_param("i", $edit_id);
    $stmt->execute();
    $edit_inventory = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}

$inventories = $conn->query("SELECT * FROM INVENTORY");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Manage Inventory</title>
    <link rel="stylesheet" href="../css/styles.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }
        th {
            background-color: #dc3545; /* Red background for the header */
            color: white; /* White text color */
            padding: 10px; /* Padding for the header cells */
            text-align: left; /* Align text to the left */
            border: 1px solid white; /* Invisible white border */
        }
        td {
            padding: 10px; /* Padding for the data cells */
            border: 1px solid white; /* Invisible white border */
        }
        table {
            width: 100%; /* Adjust the width as needed */
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="vendors-box">
            <h1>Manage Inventory</h1>
            <a href="../dashboard.php" class="back-link">Back to Dashboard</a>

            <h2><?= $edit_inventory ? "Edit Inventory Item" : "Add Inventory Item" ?></h2>
            <form method="POST">
                <input type="hidden" name="inventory_id" value="<?= htmlspecialchars($edit_inventory['inventory_id'] ?? '') ?>">
                <input type="text" name="item_name" placeholder="Item Name" value="<?= htmlspecialchars($edit_inventory['item_name'] ?? '') ?>" required>
                <textarea name="description" placeholder="Description"><?= htmlspecialchars($edit_inventory['description'] ?? '') ?></textarea>
                <input type="number" name="quantity" placeholder="Quantity" value="<?= htmlspecialchars($edit_inventory['quantity'] ?? '') ?>" required min="0">
                <input type="number" name="restock_level" placeholder="Restock Level" value="<?= htmlspecialchars($edit_inventory['restock_level'] ?? '') ?>" required min="0">

                <?php if (in_array($_SESSION['role'], [ROLE_ADMIN, ROLE_PROCUREMENT_OFFICER])): ?>
                    <button type="submit" name="<?= $edit_inventory ? 'update_inventory' : 'add_inventory' ?>">
                        <?= $edit_inventory ? "Update Inventory Item" : "Add Inventory Item" ?>
                    </button>
                <?php endif; ?>
            </form>

            <h2>Existing Inventory Items</h2>
            <table>
                <thead>
                    <tr>
                        <th>Inventory ID</th>
                        <th>Item Name</th>
                        <th>Description</th>
                        <th>Quantity</th>
                        <th>Restock Level</th>
                        <th>Updated At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($inventory = $inventories->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($inventory['inventory_id']) ?></td>
                            <td><?= htmlspecialchars($inventory['item_name']) ?></td>
                            <td><?= htmlspecialchars($inventory['description']) ?></td>
                            <td><?= htmlspecialchars($inventory['quantity']) ?></td>
                            <td><?= htmlspecialchars($inventory['restock_level']) ?></td>
                            <td><?= htmlspecialchars($inventory['updated_at']) ?></td>
                            <td>
                                <a href="<?= $_SERVER['PHP_SELF']?>?edit=<?= $inventory['inventory_id'] ?>" class="update-btn">Edit</a>
                                <?php if ($_SESSION['role'] === ROLE_ADMIN): ?>
                                    <form method="POST" style="display:inline;">
                                        <input type="hidden" name="inventory_id" value="<?= $inventory['inventory_id'] ?>">
                                        <button type="submit" name="delete_inventory" onclick="return confirm('Are you sure you want to delete this item?');" class="delete-btn">Delete</button>
                                    </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
