<?php


require_once "includes/csrf.php";



require_once "includes/error_handler.php";
require_once "includes/session.php";
require_once "includes/security.php";
require_once "includes/auth.php";
require_once "config/database.php";
require_once "config/config.php";
require_once "includes/logger.php";
validateCSRFToken($_POST['csrf_token']);
checkSessionTimeout();
requireLogin();
requireRole("Customer");
writeLog(
    "audit.log",
    "User ".$_SESSION['id']." started checkout"
);
$con->begin_transaction();


try{

$user_id = (int)$_SESSION['id'];
$address_id = (int)$_POST['address_id'];
$stmt = $con->prepare(
"
SELECT id
FROM addresses
WHERE id=?
AND user_id=?
"
);

$stmt->bind_param(
"ii",
$address_id,
$user_id
);

$stmt->execute();

if($stmt->get_result()->num_rows == 0)
{
    throw new Exception("Invalid address");
}
$stmt = $con->prepare(
"SELECT cart.product_id,
        cart.quantity,
        products.price
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

if($stmt->affected_rows == 0)
{
    throw new Exception("Stock unavailable");
}

$result = $stmt->get_result();

$total = 0;

$items = [];

while($row = mysqli_fetch_assoc($result))
{
    $total += $row['price'] * $row['quantity'];
    $items[] = $row;
}

if(count($items)==0)
{
    throw new Exception("Cart empty");
}

$stmt = $con->prepare(
"INSERT INTO orders
(user_id,address_id,total_amount,status)
VALUES(?,?,?,?)"
);

$status = "Pending";

$stmt->bind_param(
    "iids",
    $user_id,
    $address_id,
    $total,
    $status
);

if(!$stmt->execute())
{
    throw new Exception("Order item failed");
}

$order_id = $con->insert_id;
foreach($items as $item)
{
    $stmt = $con->prepare(
"INSERT INTO order_items
(order_id,product_id,quantity,price)
VALUES(?,?,?,?)"
);

$stmt->bind_param(
    "iiid",
    $order_id,
    $item['product_id'],
    $item['quantity'],
    $item['price']
);
if(!$stmt->execute() || $stmt->affected_rows == 0)
{
    throw new Exception("Stock unavailable");
}
$stmt = $con->prepare(
"UPDATE products
 SET stock = stock - ?
 WHERE id = ?
 AND stock >= ?"
);

$stmt->bind_param(
    "iii",
    $item['quantity'],
    $item['product_id'],
    $item['quantity']
);

$stmt->execute();
}

$stmt = $con->prepare(
"DELETE FROM cart
 WHERE user_id=?"
);

$stmt->bind_param(
    "i",
    $user_id
);

$stmt->execute();
$con->commit();


writeLog(
"audit.log",
"User ".$user_id." placed order ".$order_id
);

echo "<h1>Order Placed Successfully!</h1>";
echo "<a href='shopping.php'>Continue Shopping</a>";
}
catch(Exception $e)
{

$con->rollback();

writeLog(
"error.log",
"Checkout failed for user ".$user_id
);

echo "Order failed. Please try again.";

}

?>