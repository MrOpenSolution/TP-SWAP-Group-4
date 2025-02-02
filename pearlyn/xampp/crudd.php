<?php
$connect = mysqli_connect("localhost", "root", "", "requests");
if (!$connect) {
    echo "<script>alert('Connection failed: " . mysqli_connect_error() . "');</script>";
} else {
    echo "<script>alert('Connected successfully');</script>";
}
if(isset($_POST["insert_button"])){
    if($_POST["insert"]=="yes")
    {   
        $itemname=$_POST["item_name"];
        $quantity=$_POST["quantity"];
        $department=$_POST["department"];
        $priority_level=$_POST["priority_level"];
        $created_by=$_POST["created_by"];
        $request_date=$_POST["request_date"];
        $status=$_POST["status"];
        $valid_pattern = "/^[a-zA-Z0-9\s\-,.()]+$/";

        if (!preg_match($valid_pattern, $itemname) || 
            !preg_match($valid_pattern, $department) || 
            !preg_match($valid_pattern, $created_by)) {
            echo "<script>alert('Invalid input! Only letters, numbers, spaces, and -,.() are allowed.');</script>";
        } else {
            // Prepare and bind
            $query = $connect->prepare("INSERT INTO req(ITEM_NAME, QUANTITY, DEPARTMENT, PRIORITY_LEVEL, CREATED_BY, REQUEST_DATE, STATUS) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $query->bind_param('sisssss', $itemname, $quantity, $department, $priority_level, $created_by, $request_date, $status);

            if ($query->execute()) {
                echo "<script>alert('Record Inserted Successfully!');</script>";
            } else {
                echo "<script>alert('Error inserting record.');</script>";
            }
        }
    }
}

if (isset($_POST["update_button"])) {
    if (isset($_POST["request_id"], $_POST["item_name"], $_POST["quantity"], $_POST["department"], $_POST["priority_level"], $_POST["created_by"], $_POST["request_date"], $_POST["status"])) {
        $request_id = $_POST["request_id"];

        $check_query = $connect->prepare("SELECT REQUEST_ID FROM req WHERE REQUEST_ID = ?");
        $check_query->bind_param('i', $request_id);
        $check_query->execute();
        $check_query->store_result();
        $check_query->close();
        $itemname = $_POST["item_name"];
        $quantity = $_POST["quantity"];
        $department = $_POST["department"];
        $priority_level = $_POST["priority_level"];
        $created_by = $_POST["created_by"];
        $request_date = $_POST["request_date"];
        $status = $_POST["status"];

        $check_query = $connect->prepare("SELECT REQUEST_ID FROM req WHERE REQUEST_ID = ?");
        $check_query->bind_param('i', $request_id);
        $check_query->execute();
        $check_query->store_result();
        if ($check_query->num_rows === 0) {
            die("Error: The provided REQUEST_ID does not exist in the database.");
        }
        $check_query->close();

        $query = $connect->prepare("UPDATE req SET ITEM_NAME=?, QUANTITY=?, DEPARTMENT=?, PRIORITY_LEVEL=?, CREATED_BY=?, REQUEST_DATE=?, STATUS=? WHERE REQUEST_ID=?");
        $query->bind_param('sisssssi', $itemname, $quantity, $department, $priority_level, $created_by, $request_date, $status, $request_id);

        if ($query->execute()) {
            if ($query->affected_rows > 0) {
                echo "<center>Record Updated Successfully!</center><br>";
            } else {
                echo "<center>No changes made to the record.</center><br>";
            }
        } else {
            echo "Error during query execution: " . $query->error . "<br>";
        }

        $query->close();
    }
}
if(isset($_POST["delete_button"])){
    $request_id=$_POST["request_id"];
    $query=$connect->prepare("DELETE FROM req WHERE REQUEST_ID=?");
    $query->bind_param('i', $request_id);
    if($query->execute())
    {
        echo "<center>Record Deleted!</center><br>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Insert Record</title>
</head>
<body>
    <form method="post" action="crudd.php">
        <table align="center" border="0">
            <tr>
                <td>Item Name:</td>
                <td><input type="text" name="item_name" value="<?php echo isset($_GET['item_name']) ? htmlspecialchars($_GET['item_name'], ENT_QUOTES, 'UTF-8') : ''; ?>" /></td>
           </tr>

           <tr>
                <td>Quantity:</td>
                <td><input type="number" name="quantity" min="1" step="1" value="<?php echo isset($_GET['quantity']) ? htmlspecialchars($_GET['quantity'], ENT_QUOTES, 'UTF-8') : ''; ?>" 
                oninput="this.value = this.value.replace(/[^0-9]/g, '');"/></td>
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
                <td><input type="date" name="request_date" value="<?php echo isset($_GET['request_date']) ? htmlspecialchars($_GET['request_date'], ENT_QUOTES, 'UTF-8') : ''; ?>" /></td>
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
                    <input type="hidden" name="id" value="<?php echo isset($_GET['request_id']) ? htmlspecialchars($_GET['request_id'], ENT_QUOTES, 'UTF-8') : ''; ?>" />
                    <input type="hidden" name="insert" value="yes" />
                    <input type="submit" name="insert_button" value="Insert Record" />
                </td>
            </tr>
        </table>

        <?php
$query=$connect->prepare("SELECT * FROM req");
$query->execute();
$query->bind_result( $request_id, $itemname, $quantity, $department, $priority_level, $created_by, $request_date, $status );
echo "<table align='center' border='1'>";
echo "<tr>";
echo "<th>Id</th>";
echo "<th>Item Name</th>";
echo "<th>Quantity</th>";
echo "<th>Department</th>";
echo "<th>Priority Level</th>";
echo "<th>Created By</th>";
echo "<th>Request Date</th>";
echo "<th>Status</th>";
echo "<th>EDIT</th>";
echo "<th>DELETE</th>";
echo "</tr>";

while($query->fetch())
{
    echo "<tr>";
    echo "<td>".$request_id."</td>";
    echo "<td>".$itemname."</td>";
    echo "<td>".$quantity."</td>";
    echo "<td>".$department."</td>";
    echo "<td>".$priority_level."</td>";
    echo "<td>".$created_by."</td>";
    echo "<td>".$request_date."</td>";
    echo "<td>".$status."</td>";
    echo "<td><a href='edit.php?operation=edit&request_id=".urlencode($request_id)."&itemname=".urlencode($itemname)."&quantity=".urlencode($quantity)."&department=".urlencode($department)."&priority_level=".urlencode($priority_level)."&created_by=".urlencode($created_by)."&request_date=".urlencode($request_date)."&status=".urlencode($status)."'>edit</a></td>";

    echo "<td align='center'>";
    echo "<input type='hidden' name='request_id' value=".$request_id." />";
    echo "<input type='submit' name='delete_button' value='delete' class='button' />";
    echo "</td>";   
    echo "</tr>";   
}
echo "</table>";
?>
    </form>
</body>
</html>

