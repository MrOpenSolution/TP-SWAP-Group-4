<?php
session_start();
require 'config.php';

// Ensure only Admins can delete vendors
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
    header("Location: login.php");
    exit();
}

// Check if the vendor ID is provided in the URL
if (isset($_GET['id'])) {
    $vendor_id = intval($_GET['id']);

    // Prepare and execute the delete query
    $stmt = $conn->prepare("DELETE FROM vendors WHERE vendor_id = ?");
    $stmt->bind_param("i", $vendor_id);

    if ($stmt->execute()) {
        header("Location: manage_vendors.php");
        exit();
    } else {
        echo "Error: Could not delete vendor.";
    }
} else {
    echo "Error: No vendor ID specified.";
}
?>

