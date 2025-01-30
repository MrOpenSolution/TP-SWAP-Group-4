<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Record</title>
</head>
<body>
    <form action="crudd.php" method="POST">
        <table align="center" border="0">
            <tr>
                <td>Item Name:</td>
                <td><input type="text" name="item_name" value="<?php echo isset($_GET['item_name']) ? htmlspecialchars($_GET['item_name'], ENT_QUOTES, 'UTF-8') : ''; ?>" /></td>
            </tr>
            <tr>
                <td>Quantity:</td>
                <td><input type="text" name="quantity" value="<?php echo isset($_GET['quantity']) ? htmlspecialchars($_GET['quantity'], ENT_QUOTES, 'UTF-8') : ''; ?>" /></td>
            </tr>
            <tr>
                <td>Department:</td>
                <td><input type="text" name="department" value="<?php echo isset($_GET['department']) ? htmlspecialchars($_GET['department'], ENT_QUOTES, 'UTF-8') : ''; ?>" /></td>
            </tr>
            <tr>
                <td>Priority Level:</td>
                <td>
                    <select name="priority_level">
                        <option value="Low" <?php echo (isset($_GET['priority_level']) && $_GET['priority_level'] === 'Low') ? 'selected' : ''; ?>>Low</option>
                        <option value="Medium" <?php echo (isset($_GET['priority_level']) && $_GET['priority_level'] === 'Medium') ? 'selected' : ''; ?>>Medium</option>
                        <option value="High" <?php echo (isset($_GET['priority_level']) && $_GET['priority_level'] === 'High') ? 'selected' : ''; ?>>High</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td>Created By:</td>
                <td><input type="text" name="created_by" value="<?php echo isset($_GET['created_by']) ? htmlspecialchars($_GET['created_by'], ENT_QUOTES, 'UTF-8') : ''; ?>" /></td>
            </tr>
            <tr>
                <td>Request Day:</td>
                <td>
                    <?php
                    $request_date = isset($_GET['request_date']) ? $_GET['request_date'] : '';
                    if ($request_date === '0000-00-00 00:00:00' || empty($request_date)) {
                        $request_date = '';
                    } else {
                        $request_date = date('Y-m-d', strtotime($request_date));
                    }
                    ?>
                    <input type="date" name="request_date" value="<?php echo htmlspecialchars($request_date, ENT_QUOTES, 'UTF-8'); ?>" />
                </td>
            </tr>
            <tr>
                <td>Status:</td>
                <td>
                    <select name="status">
                        <option value="Pending" <?php echo (isset($_GET['status']) && $_GET['status'] === 'Pending') ? 'selected' : ''; ?>>Pending</option>
                        <option value="Approved" <?php echo (isset($_GET['status']) && $_GET['status'] === 'Approved') ? 'selected' : ''; ?>>Approved</option>
                        <option value="Rejected" <?php echo (isset($_GET['status']) && $_GET['status'] === 'Rejected') ? 'selected' : ''; ?>>Rejected</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td>&nbsp;</td>
                <td align="right">
                <input type="hidden" name="request_id" value="<?php echo isset($_GET['request_id']) ? htmlspecialchars($_GET['request_id'], ENT_QUOTES, 'UTF-8') : ''; ?>" />
                    <input type="hidden" name="update" value="yes" />
                    <input type="submit" name="update_button" value="Edit Record" />
                </td>
            </tr>
        </table>
    </form>
</body>
</html>