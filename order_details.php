<?php

session_start();

require_once "includes/security.php";
require_once "includes/auth.php";
require_once "config/database.php";
require_once "config/config.php";

checkSessionTimeout();
requireLogin();


$order_id = (int)$_GET['id'];
$user_id = (int)$_SESSION['id'];

$stmt = $con->prepare(
"
SELECT
products.name,
order_items.quantity,
order_items.price

FROM order_items

JOIN products
ON order_items.product_id = products.id

WHERE order_items.order_id=?
"
);

$stmt->bind_param(
    "i",
    $order_id
);

$stmt->execute();

$result = $stmt->get_result();
$stmt2 = $con->prepare(
    "SELECT *
     FROM orders
     WHERE id=?
     AND user_id=?"
);

$stmt2->bind_param(
    "ii",
    $order_id,
    $user_id
);

$stmt2->execute();

$order = $stmt2->get_result()->fetch_assoc();


?>

<!DOCTYPE html>
<html>
<head>
<title>Order Details</title>
</head>
<body>

<h1>Order #<?php echo $order_id; ?></h1>

<h3>Status: <?php echo $order['status']; ?></h3>

<table border="1" cellpadding="10">

<tr>
<th>Product</th>
<th>Quantity</th>
<th>Price</th>
</tr>

<?php

$total = 0;

while($row=mysqli_fetch_assoc($result))
{
$total += $row['quantity'] * $row['price'];
?>

<tr>
<td><?php echo $row['name']; ?></td>
<td><?php echo $row['quantity']; ?></td>
<td>₹<?php echo $row['price']; ?></td>
</tr>

<?php
}
?>

</table>

<h2>Total: ₹<?php echo $total; ?></h2>

<a href="my_orders.php">Back</a>

</body>
</html>