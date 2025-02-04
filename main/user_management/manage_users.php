<?php
session_start();
include_once "../common/db_conn.php";

// Constants for user roles
define('ROLE_ADMIN', 'Admin');
define('ROLE_PROCUREMENT_OFFICER', 'Procurement Officer');
define('ROLE_DEPARTMENT_HEAD', 'Department Head');

// Redirect function
function redirect($url) {
    header("Location: $url");
    exit();
}

// Ensure only Admins can access
if (!isset($_SESSION['user_id'])){
    redirect("../auth/login.php");
}


if ($_SESSION['role'] !== ROLE_ADMIN) {
    redirect("../dashboard.php");
}

// Function to sanitize input
function sanitize_input($data) {
    return htmlspecialchars(trim($data));
}
$valid_roles = [ROLE_ADMIN, ROLE_PROCUREMENT_OFFICER, ROLE_DEPARTMENT_HEAD];

// Handle Create, Update, or Reset Password
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['delete_user'])) {
        // Handle Delete User
        $user_id = intval($_POST['user_id']);
        if ($user_id != $_SESSION['user_id']) {
            $stmt = $conn->prepare("DELETE FROM USERS WHERE user_id = ?");
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $stmt->close();
        }
        redirect($_SERVER['PHP_SELF']);
    } elseif (isset($_POST['reset_password'])) {
        // Handle Reset Password
        $user_id = intval($_POST['user_id']);
        $new_password = sanitize_input($_POST['new_password']);
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

        $stmt = $conn->prepare("UPDATE USERS SET password_hash = ? WHERE user_id = ?");
        $stmt->bind_param("si", $hashed_password, $user_id);
        $stmt->execute();
        $stmt->close();
        redirect($_SERVER['PHP_SELF']);
    } else {
        $username = sanitize_input($_POST['username']);
        $email = sanitize_input($_POST['email']);
        $role = $_POST['role'] ?? null; // Role can be null for self-update
        $user_id = isset($_POST['user_id']) ? intval($_POST['user_id']) : null;

        // Validate email format
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo "<script>alert('Invalid email format.');</script>";
        } elseif ($role && !in_array($role, $valid_roles)) {
            echo "<script>alert('Invalid role selected.');</script>";
        } else {
            if (isset($_POST['add_user'])) {
                $stmt = $conn->prepare("INSERT INTO USERS (username, email, role) VALUES (?, ?, ?)");
                $stmt->bind_param("sss", $username, $email, $role);
            } elseif (isset($_POST['update_user'])) {
                if ($user_id == $_SESSION['user_id']) {
                    $stmt = $conn->prepare("UPDATE USERS SET username = ?, email = ? WHERE user_id = ?");
                    $stmt->bind_param("ssi", $username, $email, $user_id);
                } else {
                    $stmt = $conn->prepare("UPDATE USERS SET username = ?, email = ?, role = ? WHERE user_id = ?");
                    $stmt->bind_param("sssi", $username, $email, $role, $user_id);
                }
            }

            if (isset($stmt)) {
                $stmt->execute();
                $stmt->close();
            }
            redirect($_SERVER['PHP_SELF']);
        }
    }
}

// Fetch Users
$result = $conn->query("SELECT user_id, username, email, role FROM USERS");

// Get User for Editing
$edit_user = null;
if (isset($_GET['edit'])) {
    $edit_id = intval($_GET['edit']);
    $stmt = $conn->prepare("SELECT * FROM USERS WHERE user_id = ?");
    $stmt->bind_param("i", $edit_id);
    $stmt->execute();
    $edit_user = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users</title>
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
            <h1>Manage Users</h1>
            <a href="../dashboard.php" class="back-link">Back to Dashboard</a>

            <h2><?= $edit_user ? "Edit User" : "Add User" ?></h2>
            <form method="POST">
                <input type="hidden" name="user_id" value="<?= htmlspecialchars($edit_user['user_id'] ?? '') ?>">
                <input type="text" name="username" placeholder="Username" value="<?= htmlspecialchars($edit_user['username'] ?? '') ?>" required>
                <input type="email" name="email" placeholder="Email" value="<?= htmlspecialchars($edit_user['email'] ?? '') ?>" required>
                <?php if (!$edit_user || $edit_user['user_id'] != $_SESSION['user_id']): ?>
                    <select name="role" required>
                        <option value="Admin" <?= isset($edit_user) && $edit_user['role'] === ROLE_ADMIN ? 'selected' : '' ?>>Admin</option>
                        <option value="Procurement Officer" <?= isset($edit_user) && $edit_user['role'] === ROLE_PROCUREMENT_OFFICER ? 'selected' : '' ?>>Procurement Officer</option>
                        <option value="Department Head" <?= isset($edit_user) && $edit_user['role'] === ROLE_DEPARTMENT_HEAD ? 'selected' : '' ?>>Department Head</option>
                    </select>
                <?php endif; ?>
                <button type="submit" name="<?= $edit_user ? 'update_user' : 'add_user' ?>">
                    <?= $edit_user ? "Update User" : "Add User" ?>
                </button>
            </form>

            <?php if ($edit_user): ?>
                <h2>Reset Password for <?= htmlspecialchars($edit_user['username']) ?></h2>
                <form method="POST">
                    <input type="hidden" name="user_id" value="<?= htmlspecialchars($edit_user['user_id']) ?>">
                    <input type="password" name="new_password" placeholder="New Password" required>
                    <button type="submit" name="reset_password">Reset Password</button>
                </form>
            <?php endif; ?>

            <h2>Existing Users</h2>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['user_id']) ?></td>
                            <td><?= htmlspecialchars($row['username']) ?></td>
                            <td><?= htmlspecialchars($row['email']) ?></td>
                            <td><?= htmlspecialchars($row['role']) ?></td>
                            <td>
                                <a href="<?= $_SERVER['PHP_SELF']?>?edit=<?= $row['user_id'] ?>" class="update-btn">Edit</a>
                                <?php if ($row['user_id'] != $_SESSION['user_id']): ?>
                                    <form method="POST" style="display:inline;">
                                        <input type="hidden" name="user_id" value="<?= $row['user_id'] ?>">
                                        <button type="submit" name="delete_user" onclick="return confirm('Are you sure you want to delete this user?');" class="delete-btn">Delete</button>
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
