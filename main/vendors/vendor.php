<?php
session_start();
include_once '../common/db_conn.php';  // Include database configuration

/*TODO Add back
// Ensure user is logged in as Admin or authorized role
if (!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}
// TODO potential issue when role is not synced with DB
if (['role'] !== 'Admin'){
    header("Location: login.php");
    exit();
}
*/
// Handle adding a new vendor
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_vendor'])) {
    $name = trim($_POST['name']);
    $contact_info = trim($_POST['contact_info']);
    $services = trim($_POST['services']);
    $payment_terms = $_POST['payment_terms'];

    if (!empty($name) && !empty($contact_info) && !empty($services) && !empty($payment_terms)) {
        $stmt = $conn->prepare("INSERT INTO VENDORS (name, contact_info, services, payment_terms) VALUES (?, ?, ?, ?)");
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
        $stmt = $conn->prepare("UPDATE VENDORS SET name = ?, contact_info = ?, services = ?, payment_terms = ? WHERE vendor_id = ?");
        $stmt->bind_param("ssssi", $name, $contact_info, $services, $payment_terms, $vendor_id);
        $stmt->execute();
        $stmt->close();
        header("Location: manage_vendors.php");
        exit();
    }
}

// Fetch existing vendors
$vendors = $conn->query("SELECT * FROM VENDORS");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Vendors - Secure AMC</title>
    <link rel="stylesheet" href="../css/styles.css">
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
