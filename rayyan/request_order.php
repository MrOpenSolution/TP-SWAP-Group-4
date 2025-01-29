<?php
session_start();
require 'config.php'; // Database connection

// Ensure Department Head is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Department Head') {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $requested_by = $_SESSION['user_id'];
    $vendor_id = $_POST['vendor_id']; // Ensure this is a valid vendor_id
    $items = $_POST['items'];

    // Check if vendor exists
    $vendor_check = $conn->prepare("SELECT vendor_id FROM vendors WHERE vendor_id = ?");
    $vendor_check->bind_param("i", $vendor_id);
    $vendor_check->execute();
    $vendor_check->store_result();
    
    if ($vendor_check->num_rows == 0) {
        die("Error: Selected vendor does not exist.");
    }

    // Insert into `purchase_orders`
    $stmt = $conn->prepare("INSERT INTO purchase_orders (requested_by, vendor_id, items, status) VALUES (?, ?, ?, 'Pending')");
    $stmt->bind_param("iis", $requested_by, $vendor_id, $items);

    if ($stmt->execute()) {
        echo "Purchase request submitted successfully!";
        header("Location: view_head_orders.php");
        exit();
    } else {
        echo "Error submitting request: " . $conn->error;
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Request New Purchase Order</title>
</head>
<body>
    <h2>Request New Purchase Order</h2>
    <form method="POST" action="request_order.php">
        <label>Select Vendor:</label>
        <select name="vendor_id" required>
            <option value="" disabled selected>Select Vendor</option>
            <?php
            $vendor_result = $conn->query("SELECT vendor_id, name FROM vendors");
            while ($vendor = $vendor_result->fetch_assoc()) {
                echo "<option value='{$vendor['vendor_id']}'>{$vendor['name']}</option>";
            }
            ?>
        </select>
        <label>Requested Items:</label>
        <input type="text" name="items" required>
        <button type="submit">Submit Request</button>
    </form>
    <a href="head_dashboard.php">Back to Dashboard</a>
</body>
</html>









