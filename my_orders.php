<?php
require_once "includes/session.php";
require_once "includes/error_handler.php";
require_once "includes/security.php";
require_once "includes/logger.php";
require_once "includes/auth.php";
require_once "config/database.php";
require_once "config/config.php";
require_once "includes/session.php";
require_once "includes/db_logger.php";
require_once "includes/access_logger.php";
checkSessionTimeout();
requireLogin();
requireRole("Customer");

writeLog(
    "audit.log",
    "Customer ".$_SESSION['id']." viewed order history"
);


$user_id = (int)$_SESSION['id'];
logQuery(
    "SELECT * FROM orders WHERE user_id=? ORDER BY created_at DESC",
    [$user_id]
);
$stmt = $con->prepare(
    "SELECT *
     FROM orders
     WHERE user_id=?
     ORDER BY created_at DESC"
);

$stmt->bind_param(
    "i",
    $user_id
);

$stmt->execute();

$result = $stmt->get_result();
if($result->num_rows == 0)
{
    echo "No orders found";
}

?>

<!DOCTYPE html>
<html>
<head>
<title>My Orders</title>

<style>

table{
    width:100%;
    border-collapse:collapse;
}

th,td{
    border:1px solid black;
    padding:10px;
    text-align:center;
}

</style>

</head>
<body>

<h1>My Orders</h1>

<a href="shopping.php">⬅ Back to Shopping</a>

<br><br>

<table>

<tr>
    <th>Order ID</th>
    <th>Total Amount</th>
    <th>Status</th>
    <th>Date</th>
    <th>Invoice</th>
</tr>

<?php
while($row=mysqli_fetch_assoc($result))
{
?>

<tr>
<td>
<a href="order_details.php?id=<?php echo (int)$row['id']; ?>">
<?php echo htmlspecialchars($row['id']); ?>
</a>
</td>
<td>₹<?php echo htmlspecialchars($row['total_amount']); ?></td>
<td><?php echo htmlspecialchars($row['status']); ?></td>
<td><?php echo htmlspecialchars($row['created_at']); ?></td>
<td>
<a href="invoice.php?id=<?php echo (int)$row['id']; ?>">
📄 Invoice
</a>
</td>
</tr>

<?php
}
?>

</table>

</body>
</html>