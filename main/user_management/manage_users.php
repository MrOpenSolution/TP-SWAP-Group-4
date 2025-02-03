<?php
session_start();
include_once "../common/db_conn.php";

// Ensure only Admins can access
if (!isset($_SESSION['user_id'])){
    header("Location: ../auth/login.php");
    exit();
}
if ($_SESSION['role'] !== 'Admin'){
    header("Location: ../auth/login.php");
    exit();
} 
// Handle Create User
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_user'])) {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $role = $_POST['role'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO USERS (username, email, password_hash, role) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $username, $email, $password, $role);
    $stmt->execute();
    header("Location: manage_users.php");
    exit();
}

// Handle Update User
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_user'])) {
    $user_id = intval($_POST['user_id']);
    $username = $_POST['username'];
    $email = $_POST['email'];
    $role = $_POST['role'];

    // Prevent admins from changing their own role
    if ($user_id == $_SESSION['user_id']) {
        $stmt = $conn->prepare("UPDATE USERS SET username = ?, email = ? WHERE user_id = ?");
        $stmt->bind_param("ssi", $username, $email, $user_id);
    } else {
        $stmt = $conn->prepare("UPDATE USERS SET username = ?, email = ?, role = ? WHERE user_id = ?");
        $stmt->bind_param("sssi", $username, $email, $role, $user_id);
    }

    $stmt->execute();
    header("Location: manage_users.php");
    exit();
}

// Handle Delete User
if (isset($_GET['delete'])) {
    $user_id = intval($_GET['delete']);
    if ($user_id != $_SESSION['user_id']) { // Prevent deleting self
        $conn->query("DELETE FROM USERS WHERE user_id = $user_id");
    }
    header("Location: manage_users.php");
    exit();
}

// Fetch Users
$result = $conn->query("SELECT user_id, username, email, role FROM USERS");

// Get User for Editing
$edit_user = null;
if (isset($_GET['edit'])) {
    $edit_id = intval($_GET['edit']);
    $edit_user = $conn->query("SELECT * FROM USERS WHERE user_id = $edit_id")->fetch_assoc();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Users</title>
</head>
<body>
    <h1>Manage Users</h1>
    <a href="../admin_dashboard.php">Back to Dashboard</a>

    <h2><?= $edit_user ? "Edit User" : "Add User" ?></h2>
    <form method="POST">
        <input type="hidden" name="user_id" value="<?= $edit_user['user_id'] ?? '' ?>">
        <input type="text" name="username" placeholder="Username" value="<?= $edit_user['username'] ?? '' ?>" required>
        <input type="email" name="email" placeholder="Email" value="<?= $edit_user['email'] ?? '' ?>" required>
        <?php if (!$edit_user || $edit_user['user_id'] != $_SESSION['user_id']): ?>
            <select name="role">
                <option value="Admin" <?= isset($edit_user) && $edit_user['role'] === 'Admin' ? 'selected' : '' ?>>Admin</option>
                <option value="Procurement Officer" <?= isset($edit_user) && $edit_user['role'] === 'Procurement Officer' ? 'selected' : '' ?>>Procurement Officer</option>
                <option value="Department Head" <?= isset($edit_user) && $edit_user['role'] === 'Department Head' ? 'selected' : '' ?>>Department Head</option>
            </select>
        <?php endif; ?>
        <button type="submit" name="<?= $edit_user ? 'update_user' : 'add_user' ?>">
            <?= $edit_user ? "Update User" : "Add User" ?>
        </button>
    </form>

    <h2>Existing Users</h2>
    <table border="1">
        <tr>
            <th>ID</th>
            <th>Username</th>
            <th>Email</th>
            <th>Role</th>
            <th>Actions</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= $row['user_id'] ?></td>
                <td><?= htmlspecialchars($row['username']) ?></td>
                <td><?= htmlspecialchars($row['email']) ?></td>
                <td><?= $row['role'] ?></td>
                <td>
                    <a href="manage_users.php?edit=<?= $row['user_id'] ?>">Edit</a>
                    <?php if ($row['user_id'] != $_SESSION['user_id']): ?>
                        | <a href="manage_users.php?delete=<?= $row['user_id'] ?>" onclick="return confirm('Are you sure?');">Delete</a>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>


