<?php
session_start();
include_once '../common/db_conn.php';

/* TODO Add back
// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
*/

// Handle creating new orders
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_order'])) {
    $vendor_id = $_POST['vendor_id'];
    $items = $_POST['items'];
    $quantity = $_POST['quantity'];
    $requested_by = $_SESSION['user_id'];

    if ($quantity >= 1) {
        $conn->query("INSERT INTO purchase_orders (requested_by, vendor_id, items, quantity, status) 
                      VALUES ('$requested_by', '$vendor_id', '$items', '$quantity', 'Pending')");
    } else {
        echo "<script>alert('Quantity must be 1 or more');</script>";
    }
}

// Handle updates
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_order'])) {
    $order_id = $_POST['order_id'];
    $vendor_id = $_POST['vendor_id'];
    $items = $_POST['items'];
    $quantity = $_POST['quantity'];
    $status = $_POST['status'];

    $conn->query("UPDATE purchase_orders 
                  SET vendor_id = '$vendor_id', items = '$items', quantity = '$quantity', status = '$status', updated_at = NOW() 
                  WHERE order_id = '$order_id'");
}

// Handle deletions
// FIXME This should use delete
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_order'])) {
    $order_id = $_POST['delete_order'];
    $conn->query("DELETE FROM ORDERS WHERE order_id = '$order_id'");
    header("Location: index.php");
    exit;
}

// Fetch all orders
$orders = $conn->query("SELECT * FROM ORDERS");
$vendors = $conn->query("SELECT vendor_id, name FROM VENDORS");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Orders</title>
    <link rel="stylesheet" href="../css/styles.css">
    <style>
    <style>
            body {
            background: url('../img/dashboard.jpg') no-repeat center center fixed;
            background-size: cover;
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }
        .container {
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            max-width: 1200px;
            margin: 50px auto;
        }
        h2 {
            text-align: center;
        }
        th {
            background-color: black;
            color: white;
            text-align: center;
            font-size: 18px;
        }
        .table {
            border: 1px solid #007bff;
            box-shadow: 0px 2px 10px rgba(0, 0, 0, 0.1);
        }
        .btn-update, .btn-delete {
            width: 80px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="vendors-box">
        <h1>Manage Orders</h1>
        <a href="../dashboard.php" class="back-link">Back to Dashboard</a>

        <h3>Add New Order</h3>
        <form method="POST" action="">
             <select class="form-select" name="vendor_id" required>
             <option value="">Select Vendor</option>
             <?php
                   while ($vendor = $vendors->fetch_assoc()) {
                   echo htmlspecialchars("<option value='{$vendor['vendor_id']}'>{$vendor['name']}</option>");
                   }
             ?>
                    </select>
                    <input type="text" name="items" class="form-control" placeholder="Items" required>
                    <input type="number" name="quantity" class="form-control" placeholder="Quantity" required min="1">
                    <button type="submit" name="create_order" class="btn btn-success w-100">Create Order</button>
        </form>

        <h3>Existing Orders</h3>
        <table class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Requested By</th>
                    <th>Vendor</th>
                    <th>Items</th>
                    <th>Quantity</th>
                    <th>Status</th>
                    <th>Created At</th>
                    <th>Updated At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($order = $orders->fetch_assoc()): ?>
                <tr>
                    <form method="POST">
                        <td>
                           <?php echo $order['order_id'] ?>
                           <input type="hidden" name="order_id" value="<?php echo $vendor['order_id']; ?>">
                        </td>
                        <td>
                           <?php 
                           
                            echo $order['order_id'] ?>
                           <input type="hidden" name="order_id" value="<?php echo $vendor['order_id']; ?>">
                        </td>
                        <td><?= $order['requested_by'] ?></td>
                        <td><?= $order['requested_by'] ?></td>
                        <td>
                            <input type="hidden" name="order_id" value="<?= $row['order_id'] ?>">
                            <select class="form-select" name="vendor_id">
                                <option value="<?= $row['vendor_id'] ?>" selected><?= $row['vendor_name'] ?></option>
                                <?php 
                                while ($vendor = $vendors->fetch_assoc()) {
                                    echo "<option value='{$vendor['vendor_id']}'>{$vendor['name']}</option>";
                                }
                                ?>
                            </select>
                        </td>
                        <td><input type="text" class="form-control" name="items" value="<?= $row['items'] ?>"></td>
                        <td><input type="number" class="form-control" name="quantity" value="<?= $row['quantity'] ?>" min="1"></td>
                        <td>
                            <select class="form-select" name="status">
                                <option <?= $row['status'] === 'Pending' ? 'selected' : '' ?>>Pending</option>
                                <option <?= $row['status'] === 'Approved' ? 'selected' : '' ?>>Approved</option>
                                <option <?= $row['status'] === 'Completed' ? 'selected' : '' ?>>Completed</option>
                            </select>
                        </td>
                        <td><?= $row['created_at'] ?></td>
                        <td><?= $row['updated_at'] ?></td>
                        <td>
                            <button type="submit" name="update_order" class="btn btn-primary btn-update">Update</button>
                            <a href="?delete_order=<?= $row['order_id'] ?>" class="btn btn-danger btn-delete">Delete</a>
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
