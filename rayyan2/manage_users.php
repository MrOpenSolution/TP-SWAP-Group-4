<?php
session_start();
require 'config.php';  // Include database configuration

// Ensure user is logged in as Admin or authorized role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
    header("Location: login.php");
    exit();
}

// Handle adding a new user
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_user'])) {
    $username = trim($_POST['username']);
    $password = password_hash(trim($_POST['password']), PASSWORD_BCRYPT);
    $email = trim($_POST['email']);
    $role = $_POST['role'];

    if (!empty($username) && !empty($password) && !empty($email) && !empty($role)) {
        $stmt = $conn->prepare("INSERT INTO users (username, password, email, role) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $username, $password, $email, $role);
        $stmt->execute();
        $stmt->close();
        header("Location: manage_users.php");
        exit();
    }
}

// Handle user updates
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_user'])) {
    $user_id = $_POST['user_id'];
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $role = $_POST['role'];

    if (!empty($user_id) && !empty($username) && !empty($email) && !empty($role)) {
        $stmt = $conn->prepare("UPDATE users SET username = ?, email = ?, role = ? WHERE user_id = ?");
        $stmt->bind_param("sssi", $username, $email, $role, $user_id);
        $stmt->execute();
        $stmt->close();
        header("Location: manage_users.php");
        exit();
    }
}

// Fetch existing users
$users = $conn->query("SELECT * FROM users");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users - Secure AMC</title>
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

        .users-box {
            background-color: #ffffff;
            padding: 40px;
            border-radius: 20px;
            width: 1100px;
            box-shadow: 0px 10px 30px rgba(0, 0, 0, 0.3);
            text-align: center;
        }

        .users-box h1 {
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

        table input[type="text"], table input[type="email"], table select {
            width: 95%;
            padding: 10px;
            border-radius: 8px;
            border: 1px solid #ccc;
            font-size: 16px;
        }

        table input[type="email"] {
            width: 97%;
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
    <div class="users-box">
        <h1>Manage Users</h1>
        <a href="admin_dashboard.php" class="back-link">Back to Dashboard</a>

        <h3>Add New User</h3>
        <form method="POST" action="">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <input type="email" name="email" placeholder="Email" required>
            <select name="role" required>
                <option value="Admin">Admin</option>
                <option value="Department Head">Department Head</option>
                <option value="Procurement Officer">Procurement Officer</option>
            </select>
            <button type="submit" name="add_user">Add User</button>
        </form>

        <h3>Existing Users</h3>
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
                <?php while ($user = $users->fetch_assoc()): ?>
                    <tr>
                        <form method="POST" action="">
                            <td>
                                <?php echo $user['user_id']; ?>
                                <input type="hidden" name="user_id" value="<?php echo $user['user_id']; ?>">
                            </td>
                            <td>
                                <input type="text" name="username" value="<?php echo $user['username']; ?>" required>
                            </td>
                            <td>
                                <input type="email" name="email" value="<?php echo $user['email']; ?>" required>
                            </td>
                            <td>
                                <select name="role" required>
                                    <option value="Admin" <?php if ($user['role'] === 'Admin') echo 'selected'; ?>>Admin</option>
                                    <option value="Department Head" <?php if ($user['role'] === 'Department Head') echo 'selected'; ?>>Department Head</option>
                                    <option value="Procurement Officer" <?php if ($user['role'] === 'Procurement Officer') echo 'selected'; ?>>Procurement Officer</option>
                                </select>
                            </td>
                            <td class="action-buttons">
                                <button type="submit" name="update_user" class="update-btn">Update</button>
                                <a href="delete_user.php?user_id=<?php echo $user['user_id']; ?>" class="delete-btn">Delete</a>
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













