<?php
session_start();
require 'config.php';

// Ensure only Admins or Procurement Officers can access
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['Admin', 'Procurement Officer'])) {
    header("Location: login.php");
    exit();
}

// Handle Add Vendor
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_vendor'])) {
    $name = $_POST['name'];
    $contact = $_POST['contact'];
    $services = $_POST['services'];
    $payment = $_POST['payment'];

    $stmt = $conn->prepare("INSERT INTO vendors (name, contact_information, services_provided, payment_terms, created_by) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssi", $name, $contact, $services, $payment, $_SESSION['user_id']);
    $stmt->execute();
    header("Location: manage_vendors.php");
    exit();
}

// Handle Update Vendor
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_vendor'])) {
    $vendor_id = intval($_POST['vendor_id']);
    $name = $_POST['name'];
    $contact = $_POST['contact'];
    $services = $_POST['services'];
    $payment = $_POST['payment'];

    $stmt = $conn->prepare("UPDATE vendors SET name = ?, contact_information = ?, services_provided = ?, payment_terms = ? WHERE vendor_id = ?");
    $stmt->bind_param("ssssi", $name, $contact, $services, $payment, $vendor_id);
    $stmt->execute();
    header("Location: manage_vendors.php");
    exit();
}

// Handle Delete Vendor
if (isset($_GET['delete'])) {
    $vendor_id = intval($_GET['delete']);
    $conn->query("DELETE FROM vendors WHERE vendor_id = $vendor_id");
    header("Location: manage_vendors.php");
    exit();
}

// Fetch Vendors
$result = $conn->query("SELECT * FROM vendors");

// Get Vendor for Editing
$edit_vendor = null;
if (isset($_GET['edit'])) {
    $edit_id = intval($_GET['edit']);
    $edit_vendor = $conn->query("SELECT * FROM vendors WHERE vendor_id = $edit_id")->fetch_assoc();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Vendors</title>
</head>
<body>
    <h1>Manage Vendors</h1>
    <a href="admin_dashboard.php">Back to Dashboard</a>

    <h2><?= $edit_vendor ? "Edit Vendor" : "Add New Vendor" ?></h2>
    <form method="POST">
        <input type="hidden" name="vendor_id" value="<?= $edit_vendor['vendor_id'] ?? '' ?>">
        <input type="text" name="name" placeholder="Name" value="<?= $edit_vendor['name'] ?? '' ?>" required>
        <input type="text" name="contact" placeholder="Contact Info" value="<?= $edit_vendor['contact_information'] ?? '' ?>" required>
        <input type="text" name="services" placeholder="Services" value="<?= $edit_vendor['services_provided'] ?? '' ?>">
        <input type="text" name="payment" placeholder="Payment Terms" value="<?= $edit_vendor['payment_terms'] ?? '' ?>">
        <button type="submit" name="<?= $edit_vendor ? 'update_vendor' : 'add_vendor' ?>">
            <?= $edit_vendor ? "Update Vendor" : "Add Vendor" ?>
        </button>
    </form>

    <h2>Existing Vendors</h2>
    <table border="1">
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Contact</th>
            <th>Services</th>
            <th>Payment Terms</th>
            <th>Actions</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= $row['vendor_id'] ?></td>
                <td><?= htmlspecialchars($row['name']) ?></td>
                <td><?= htmlspecialchars($row['contact_information']) ?></td>
                <td><?= htmlspecialchars($row['services_provided']) ?></td>
                <td><?= htmlspecialchars($row['payment_terms']) ?></td>
                <td>
                    <a href="manage_vendors.php?edit=<?= $row['vendor_id'] ?>">Edit</a> |
                    <a href="manage_vendors.php?delete=<?= $row['vendor_id'] ?>" onclick="return confirm('Are you sure?');">Delete</a>
                </td>
            </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>




