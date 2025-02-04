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

// Ensure Admins and Procurement Officers can access
if ($_SESSION['role'] !== ROLE_ADMIN && $_SESSION['role'] !== ROLE_PROCUREMENT_OFFICER) {
    redirect("../dashboard.php");
}

// Function to sanitize input
function sanitize_input($data) {
    return htmlspecialchars(trim($data));
}

// Handle Create or Update Vendor
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['delete_vendor'])) {
        // Handle Delete Vendor
    if ($_SESSION['role'] !== ROLE_ADMIN) {
        redirect("../dashboard.php"); // Redirect if not an Admin
    }
        $vendor_id = intval($_POST['vendor_id']);
        $stmt = $conn->prepare("DELETE FROM VENDORS WHERE vendor_id = ?");
        $stmt->bind_param("i", $vendor_id);
        $stmt->execute();
        $stmt->close();
        redirect($_SERVER['PHP_SELF']); // Redirect to the current page
    } else {
        $name = sanitize_input($_POST['name']);
        $contact_info = sanitize_input($_POST['contact_info']);
        $services = isset($_POST['services']) ? sanitize_input($_POST['services']) : null;
        $payment_terms = isset($_POST['payment_terms']) ? sanitize_input($_POST['payment_terms']) : null;

        $vendor_id = isset($_POST['vendor_id']) ? intval($_POST['vendor_id']) : null;

        // Validate inputs
        if (empty($name) || empty($contact_info)) {
            echo "<script>alert('Invalid input. Please check your data.');</script>";
        } else {
            if (isset($_POST['add_vendor'])) {
                $stmt = $conn->prepare("INSERT INTO VENDORS (name, contact_info, services, payment_terms, created_by) VALUES (?, ?, ?, ?, ?)");
                $stmt->bind_param("ssssi", $name, $contact_info, $services, $payment_terms, $_SESSION['user_id']);
            } elseif (isset($_POST['update_vendor'])) {
                $stmt = $conn->prepare("UPDATE VENDORS SET name = ?, contact_info = ?, services = ?, payment_terms = ? WHERE vendor_id = ?");
                $stmt->bind_param("ssssi", $name, $contact_info, $services, $payment_terms, $vendor_id);
            }

            if (isset($stmt)) {
                $stmt->execute();
                $stmt->close();
            }
            redirect($_SERVER['PHP_SELF']); // Redirect to the current page
        }
    }
}

// Get Vendor for Editing
$edit_vendor = null;
if (isset($_GET['edit'])) {
    $edit_id = intval($_GET['edit']);
    $stmt = $conn->prepare("SELECT * FROM VENDORS WHERE vendor_id = ?");
    $stmt->bind_param("i", $edit_id);
    $stmt->execute();
    $edit_vendor = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}

$vendors = $conn->query("SELECT * FROM VENDORS");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Manage Vendors</title>
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
            <h1>Manage Vendors</h1>
            <a href="../dashboard.php" class="back-link">Back to Dashboard</a>

            <h2><?= $edit_vendor ? "Edit Vendor" : "Add Vendor" ?></h2>
            <form method="POST">
                <input type="hidden" name="vendor_id" value="<?= htmlspecialchars($edit_vendor['vendor_id'] ?? '') ?>">
                <input type="text" name="name" placeholder="Vendor Name" value="<?= htmlspecialchars($edit_vendor['name'] ?? '') ?>" required>
                <input type="text" name="contact_info" placeholder="Contact Info" value="<?= htmlspecialchars($edit_vendor['contact_info'] ?? '') ?>" required>
                <textarea name="services" placeholder="Services"><?= htmlspecialchars($edit_vendor['services'] ?? '') ?></textarea>
            <select name="payment_terms" required>
                <option value="Cash" <?= isset($edit_vendor) && $edit_vendor['payment_terms'] === 'Cash' ? 'selected' : '' ?>>Cash</option>
                <option value="Credit" <?= isset($edit_vendor) && $edit_vendor['payment_terms'] === 'Credit' ? 'selected' : '' ?>>Credit</option>
                <option value="Cash and Card" <?= isset($edit_vendor) && $edit_vendor['payment_terms'] === 'Cash and Card' ? 'selected' : '' ?>>Cash and Card</option>
            </select>
                <button type="submit" name="<?= $edit_vendor ? 'update_vendor' : 'add_vendor' ?>">
                    <?= $edit_vendor ? "Update Vendor" : "Add Vendor" ?>
                </button>
            </form>

            <h2>Existing Vendors</h2>
            <table>
                <thead>
                    <tr>
                        <th>Vendor ID</th>
                        <th>Name</th>
                        <th>Contact Info</th>
                        <th>Services</th>
                        <th>Payment Terms</th>
                        <th>Created At</th>
                        <th>Updated At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($vendor = $vendors->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($vendor['vendor_id']) ?></td>
                            <td><?= htmlspecialchars($vendor['name']) ?></td>
                            <td><?= htmlspecialchars($vendor['contact_info']) ?></td>
                            <td><?= htmlspecialchars($vendor['services']) ?></td>
                            <td><?= htmlspecialchars($vendor['payment_terms']) ?></td>
                            <td><?= htmlspecialchars($vendor['created_at']) ?></td>
                            <td><?= htmlspecialchars($vendor['updated_at']) ?></td>
                            <td>
                                <a href="<?= $_SERVER['PHP_SELF']?>?edit=<?= $vendor['vendor_id'] ?>" class="update-btn">Edit</a>
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="vendor_id" value="<?= $vendor['vendor_id'] ?>">
                                    <button type="submit" name="delete_vendor" onclick="return confirm('Are you sure you want to delete this vendor?');" class="delete-btn">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
