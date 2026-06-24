<?php


require_once "includes/error_handler.php";
require_once "includes/security.php";
require_once "includes/auth.php";
require_once "config/database.php";
require_once "config/config.php";
require_once "includes/csrf.php";
require_once "includes/headers.php";
require_once "includes/session.php";
require_once "includes/logger.php";
checkSessionTimeout();
requireLogin();

if($_SESSION['role']!="Customer")
{
    header("Location: home.php");
    exit();
}





$user_id = (int)$_SESSION['id'];

$stmt = $con->prepare(
"SELECT cart.id AS cart_id,
        cart.quantity,
        products.id,
        products.name,
        products.price,
        products.image
 FROM cart
 JOIN products
 ON cart.product_id = products.id
 WHERE cart.user_id=?"
);

$stmt->bind_param(
    "i",
    $user_id
);

$stmt->execute();

$result = $stmt->get_result();

$total = 0;
?>

<!DOCTYPE html>
<html>
<head>
<title>My Cart</title>

<style>

body{
    font-family: Arial;
    background:#f5f5f5;
    padding:20px;
}

table{
    width:100%;
    background:white;
    border-collapse:collapse;
}

th,td{
    border:1px solid #ddd;
    padding:10px;
    text-align:center;
}

img{
    width:80px;
    height:80px;
    object-fit:cover;
}

.btn{
    padding:8px 12px;
    text-decoration:none;
    background:red;
    color:white;
    border-radius:5px;
}

.top{
    margin-bottom:20px;
}

</style>

</head>

<body>

<div class="top">
    <a href="shopping.php">⬅ Continue Shopping</a>
</div>

<h1>🛒 My Cart</h1>

<table>

<tr>
    <th>Image</th>
    <th>Product</th>
    <th>Price</th>
    <th>Quantity</th>
    <th>Subtotal</th>
    <th>Action</th>
</tr>

<?php

while($row = mysqli_fetch_assoc($result))
{
    $subtotal = $row['price'] * $row['quantity'];
    $total += $subtotal;
?>

<tr>

<td>
    <img src="uploads/<?php echo htmlspecialchars($row['image']); ?>">
</td>

<td>
   <?php echo htmlspecialchars($row['name']); ?>
</td>

<td>
   ₹<?php echo htmlspecialchars($row['price']); ?>
</td>

<td>
    <?php echo $row['quantity']; ?>
</td>

<td>
   ₹<?php echo htmlspecialchars($subtotal); ?>
</td>

<td>
    <form method="POST" action="remove_from_cart.php">

<input type="hidden"
name="cart_id"
value="<?php echo $row['cart_id']; ?>">

<input type="hidden"
name="csrf_token"
value="<?php echo generateCSRFToken(); ?>">

<button class="btn" type="submit">
Remove
</button>

</form>
</td>

</tr>

<?php
}
?>

</table>

<h2>
Total: ₹<?php echo $total; ?>
</h2>

<br>

<form action="checkout.php" method="POST">

<input type="hidden"
name="csrf_token"
value="<?php echo generateCSRFToken(); ?>">

<button type="submit">
✅ Checkout
</button>

</form>

</body>
</html>