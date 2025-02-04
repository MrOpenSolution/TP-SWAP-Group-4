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

// Handle Create or Update Request
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['delete_request']) && $_SESSION['role'] === ROLE_ADMIN) {
        // Handle Delete Request
        $request_id = intval($_POST['request_id']);
        $stmt = $conn->prepare("DELETE FROM req WHERE REQUEST_ID = ?");
        $stmt->bind_param("i", $request_id);
        $stmt->execute();
        $stmt->close();
        redirect($_SERVER['PHP_SELF']); // Redirect to the current page
    } else {
        $item_name = sanitize_input($_POST['item_name']);
        $quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 0;
        $department = sanitize_input($_POST['department']);
        $priority_level = sanitize_input($_POST['priority_level']);
        $status = sanitize_input($_POST['status']);
        $request_id = isset($_POST['request_id']) ? intval($_POST['request_id']) : null;
        // Define valid options for priority level and status
        $valid_priority_levels = ['Low', 'Medium', 'High'];
        $valid_statuses = ['Pending', 'Approved', 'Rejected'];
        // Validate inputs
        $valid_pattern = "/^[a-zA-Z0-9\s\-,.()]+$/";
        if (!preg_match($valid_pattern, $item_name) || 
            !preg_match($valid_pattern, $department) || 
            $quantity < 0 ||
            !in_array($priority_level, $valid_priority_levels) || 
            !in_array($status, $valid_statuses)) {
            echo "<script>alert('Invalid input. Please check your data.');</script>";
        } else {
            if (isset($_POST['add_request']) && in_array($_SESSION['role'], [ROLE_ADMIN, ROLE_PROCUREMENT_OFFICER])) {
                $stmt = $conn->prepare("INSERT INTO REQ (ITEM_NAME, QUANTITY, DEPARTMENT, PRIORITY_LEVEL, CREATED_BY, STATUS) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("sissis", $item_name, $quantity, $department, $priority_level, $_SESSION['user_id'], $status);
            } elseif (isset($_POST['update_request']) && in_array($_SESSION['role'], [ROLE_ADMIN, ROLE_PROCUREMENT_OFFICER])) {
                $stmt = $conn->prepare("UPDATE REQ SET ITEM_NAME = ?, QUANTITY = ?, DEPARTMENT = ?, PRIORITY_LEVEL = ?, STATUS = ? WHERE REQUEST_ID = ?");
                $stmt->bind_param("sisssi", $item_name, $quantity, $department, $priority_level, $status, $request_id);
            }

            if (isset($stmt)) {
                $stmt->execute();
                $stmt->close();
            }
            redirect($_SERVER['PHP_SELF']); // Redirect to the current page
        }
    }
}

// Get Request for Editing
$edit_request = null;
if (isset($_GET['edit'])) {
    $edit_id = intval($_GET['edit']);
    $stmt = $conn->prepare("SELECT * FROM REQ WHERE REQUEST_ID = ?");
    $stmt->bind_param("i", $edit_id);
    $stmt->execute();
    $edit_request = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}

$requests = $conn->query("SELECT * FROM REQ");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Manage Requests</title>
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
            <h1>Manage Requests</h1>
            <a href="../dashboard.php" class="back-link">Back to Dashboard</a>

            <h2><?= $edit_request ? "Edit Request" : "Add Request" ?></h2>
<form method="POST">
    <input type="hidden" name="request_id" value="<?= htmlspecialchars($edit_request['REQUEST_ID'] ?? '') ?>">
    <input type="text" name="item_name" placeholder="Item Name" value="<?= htmlspecialchars($edit_request['ITEM_NAME'] ?? '') ?>" required>
    <input type="number" name="quantity" placeholder="Quantity" value="<?= htmlspecialchars($edit_request['QUANTITY'] ?? '') ?>" required min="0">
    <input type="text" name="department" placeholder="Department" value="<?= htmlspecialchars($edit_request['DEPARTMENT'] ?? '') ?>" required>
    <select name="priority_level" required>
        <option value="Low" <?= (isset($edit_request['PRIORITY_LEVEL']) && $edit_request['PRIORITY_LEVEL'] == 'Low') ? 'selected' : '' ?>>Low</option>
        <option value="Medium" <?= (isset($edit_request['PRIORITY_LEVEL']) && $edit_request['PRIORITY_LEVEL'] == 'Medium') ? 'selected' : '' ?>>Medium</option>
        <option value="High" <?= (isset($edit_request['PRIORITY_LEVEL']) && $edit_request['PRIORITY_LEVEL'] == 'High') ? 'selected' : '' ?>>High</option>
    </select>
    <select name="status" required>
        <option value="Pending" <?= (isset($edit_request['STATUS']) && $edit_request['STATUS'] == 'Pending') ? 'selected' : '' ?>>Pending</option>
        <option value="Approved" <?= (isset($edit_request['STATUS']) && $edit_request['STATUS'] == 'Approved') ? 'selected' : '' ?>>Approved</option>
        <option value="Rejected" <?= (isset($edit_request['STATUS']) && $edit_request['STATUS'] == 'Rejected') ? 'selected' : '' ?>>Rejected</option>
    </select>

    <?php if (in_array($_SESSION['role'], [ROLE_ADMIN, ROLE_PROCUREMENT_OFFICER])): ?>
        <button type="submit" name="<?= $edit_request ? 'update_request' : 'add_request' ?>">
            <?= $edit_request ? "Update Request" : "Add Request" ?>
        </button>
    <?php endif; ?>
</form>
            <h2>Existing Requests</h2>
            <table>
                <thead>
                    <tr>
                        <th>Request ID</th>
                        <th>Item Name</th>
                        <th>Quantity</th>
                        <th>Department</th>
                        <th>Priority Level</th>
                        <th>Created By</th>
                        <th>Request Date</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($request = $requests->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($request['request_id']) ?></td>
                            <td><?= htmlspecialchars($request['item_name']) ?></td>
                            <td><?= htmlspecialchars($request['quantity']) ?></td>
                            <td><?= htmlspecialchars($request['department']) ?></td>
                            <td><?= htmlspecialchars($request['priority_level']) ?></td>
                            <td><?= htmlspecialchars($request['created_by']) ?></td>
                            <td><?= htmlspecialchars($request['request_date']) ?></td>
                            <td><?= htmlspecialchars($request['status']) ?></td>
                            <td>
                                <a href="<?= $_SERVER['PHP_SELF']?>?edit=<?= $request['request_id'] ?>" class="update-btn">Edit</a>
                                <?php if ($_SESSION['role'] === ROLE_ADMIN): ?>
                                    <form method="POST" style="display:inline;">
                                        <input type="hidden" name="request_id" value="<?= $request['request_id'] ?>">
                                        <button type="submit" name="delete_request" onclick="return confirm('Are you sure you want to delete this request?');" class="delete-btn">Delete</button>
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

            background-colo
