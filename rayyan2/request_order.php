<?php
session_start();
require 'config.php';  // Include database configuration

// Ensure user is logged in as Department Head
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Department Head') {
    header("Location: login.php");
    exit();
}

// Correct the vendor column (e.g., replace 'vendor_name' with the actual column name)
$vendors = $conn->query("SELECT vendor_id, name FROM vendors");  // Update 'name' to match your column

// Process the form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $vendor_id = $_POST['vendor_id'];
    $requested_items = trim($_POST['requested_items']);
    $quantity = intval($_POST['quantity']);
    $requested_by = $_SESSION['user_id'];

    if (!empty($vendor_id) && !empty($requested_items) && $quantity > 0) {
        $stmt = $conn->prepare("INSERT INTO purchase_orders (vendor_id, items, quantity, requested_by, status) VALUES (?, ?, ?, ?, 'Pending')");
        $stmt->bind_param("isii", $vendor_id, $requested_items, $quantity, $requested_by);
        $stmt->execute();
        $stmt->close();

        header("Location: view_head_orders.php");
        exit();
    } else {
        $error = "Please fill all fields with valid data.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Request New Purchase Order - Secure AMC</title>
    <style>
        body {
            background: url('iit.png') no-repeat center center fixed;
            background-size: cover;
            font-family: 'Arial', sans-serif;
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

        .form-box {
            background-color: #ffffff;
            padding: 30px;
            border-radius: 15px;
            width: 600px;
            box-shadow: 0px 10px 30px rgba(0, 0, 0, 0.3);
            text-align: center;
        }

        .form-box h1 {
            font-size: 28px;
            color: #333;
            margin-bottom: 20px;
            font-weight: bold;
        }

        form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        select, input[type="text"], input[type="number"] {
            width: 100%;
            padding: 10px;
            border-radius: 8px;
            border: 1px solid #ccc;
            font-size: 16px;
        }

        button {
            background-color: #007bff;
            color: white;
            padding: 12px;
            border: none;
            border-radius: 8px;
            font-size: 18px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        button:hover {
            background-color: #0056b3;
        }

        .error {
            color: red;
            font-size: 14px;
            margin-top: -10px;
        }

        a {
            display: block;
            margin-top: 20px;
            color: #007bff;
            text-decoration: none;
            font-weight: bold;
        }

        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="form-box">
        <h1>Request New Purchase Order</h1>
        <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
        <form method="POST" action="">
            <label for="vendor_id">Select Vendor:</label>
            <select name="vendor_id" id="vendor_id" required>
                <option value="">Select Vendor</option>
                <?php while ($vendor = $vendors->fetch_assoc()): ?>
                    <option value="<?php echo $vendor['vendor_id']; ?>"><?php echo $vendor['name']; ?></option>  <!-- Adjust to correct column name -->
                <?php endwhile; ?>
            </select>

            <label for="requested_items">Requested Items:</label>
            <input type="text" id="requested_items" name="requested_items" placeholder="Enter items" required>

            <label for="quantity">Quantity:</label>
            <input type="number" id="quantity" name="quantity" min="1" placeholder="Enter quantity" required>

            <button type="submit">Submit Request</button>
        </form>
        <a href="head_dashboard.php">Back to Dashboard</a>
    </div>
</div>

</body>
</html>
