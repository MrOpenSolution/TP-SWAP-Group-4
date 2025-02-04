<?php
session_start();
require 'config.php';  // Include database configuration

// Ensure user is logged in as Admin or authorized role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
    header("Location: login.php");
    exit();
}

// Handle adding a new vendor
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_vendor'])) {
    $name = trim($_POST['name']);
    $contact_info = trim($_POST['contact_info']);
    $services = trim($_POST['services']);
    $payment_terms = $_POST['payment_terms'];

    if (!empty($name) && !empty($contact_info) && !empty($services) && !empty($payment_terms)) {
        $stmt = $conn->prepare("INSERT INTO vendors (name, contact_info, services, payment_terms) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $name, $contact_info, $services, $payment_terms);
        $stmt->execute();
        $stmt->close();
        header("Location: manage_vendors.php");
        exit();
    }
}

// Handle vendor updates
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_vendor'])) {
    $vendor_id = $_POST['vendor_id'];
    $name = trim($_POST['name']);
    $contact_info = trim($_POST['contact_info']);
    $services = trim($_POST['services']);
    $payment_terms = $_POST['payment_terms'];

    if (!empty($vendor_id) && !empty($name) && !empty($contact_info) && !empty($services) && !empty($payment_terms)) {
        $stmt = $conn->prepare("UPDATE vendors SET name = ?, contact_info = ?, services = ?, payment_terms = ? WHERE vendor_id = ?");
        $stmt->bind_param("ssssi", $name, $contact_info, $services, $payment_terms, $vendor_id);
        $stmt->execute();
        $stmt->close();
        header("Location: manage_vendors.php");
        exit();
    }
}

// Fetch existing vendors
$vendors = $conn->query("SELECT * FROM vendors");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Vendors - Secure AMC</title>
    <style>
        body {
            background: url('dashboard.jpg') no-repeat center center fixed;
            background-size: cover;
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 100%;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .vendors-box {
            background-color: #ffffff;
            padding: 40px;
            border-radius: 20px;
            width: 1100px;
            box-shadow: 0px 10px 30px rgba(0, 0, 0, 0.3);
            text-align: center;
        }

        .vendors-box h1 {
            font-size: 32px;
            color: #333;
            margin-bottom: 20px;
            font-weight: bold;
        }

        form {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        form input, form select {
            padding: 12px;
            width: 19%;
            border-radius: 10px;
            border: 1px solid #ccc;
            font-size: 16px;
        }

        form button {
            padding: 12px 20px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            font-size: 16px;
        }

        form button:hover {
            background-color: #0056b3;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table th, table td {
            padding: 15px;
            text-align: left;
            border: 1px solid #ddd;
        }

        table th {
            background-color: #007bff;
            color: white;
            font-size: 18px;
        }

        table input[type="text"], table select {
            width: 95%;
            padding: 10px;
            border-radius: 8px;
            border: 1px solid #ccc;
            font-size: 16px;
        }

        .action-buttons {
            display: flex;
            gap: 10px;
        }

        .update-btn, .delete-btn {
            padding: 8px 12px;
            border-radius: 8px;
            font-size: 14px;
            border: none;
            cursor: pointer;
            font-weight: bold;
        }

        .update-btn {
            background-color: #007bff;
            color: white;
        }

        .update-btn:hover {
            background-color: #0056b3;
        }

        .delete-btn {
            background-color: #dc3545;
            color: white;
        }

        .delete-btn:hover {
            background-color: #c82333;
        }

        a.back-link {
            display: inline-block;
            margin-top: 15px;
            color: #007bff;
            text-decoration: none;
            font-size: 18px;
        }

        a.back-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="vendors-box">
        <h1>Manage Vendors</h1>
        <a href="admin_dashboard.php" class="back-link">Back to Dashboard</a>

        <h3>Add New Vendor</h3>
        <form method="POST" action="">
            <input type="text" name="name" placeholder="Name" required>
            <input type="text" name="contact_info" placeholder="Contact Info (8 digits)" required>
            <input type="text" name="services" placeholder="Services" required>
            <select name="payment_terms" required>
                <option value="Cash">Cash</option>
                <option value="Credit">Credit</option>
                <option value="Cash and Card">Cash and Card</option>
            </select>
            <button type="submit" name="add_vendor">Add Vendor</button>
        </form>

        <h3>Existing Vendors</h3>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Contact Info</th>
                    <th>Services</th>
                    <th>Payment Terms</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($vendor = $vendors->fetch_assoc()): ?>
                    <tr>
                        <form method="POST" action="">
                            <td>
                                <?php echo $vendor['vendor_id']; ?>
                                <input type="hidden" name="vendor_id" value="<?php echo $vendor['vendor_id']; ?>">
                            </td>
                            <td>
                                <input type="text" name="name" value="<?php echo $vendor['name']; ?>" required>
                            </td>
                            <td>
                                <input type="text" name="contact_info" value="<?php echo $vendor['contact_info']; ?>" required>
                            </td>
                            <td>
                                <input type="text" name="services" value="<?php echo $vendor['services']; ?>" required>
                            </td>
                            <td>
                                <select name="payment_terms" required>
                                    <option value="Cash" <?php if ($vendor['payment_terms'] === 'Cash') echo 'selected'; ?>>Cash</option>
                                    <option value="Credit" <?php if ($vendor['payment_terms'] === 'Credit') echo 'selected'; ?>>Credit</option>
                                    <option value="Cash and Card" <?php if ($vendor['payment_terms'] === 'Cash and Card') echo 'selected'; ?>>Cash and Card</option>
                                </select>
                            </td>
                            <td class="action-buttons">
                                <button type="submit" name="update_vendor" class="update-btn">Update</button>
                                <a href="delete_vendor.php?vendor_id=<?php echo $vendor['vendor_id']; ?>" class="delete-btn">Delete</a>
                            </td>
                        </form>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>




















