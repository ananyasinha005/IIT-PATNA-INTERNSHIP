<?php

require_once "includes/error_handler.php";
require_once "includes/csrf.php";
require_once "includes/security.php";
require_once "includes/auth.php";
require_once "config/database.php";
require_once "config/config.php";
require_once "includes/headers.php";
require_once "includes/session.php";
require_once "includes/logger.php";
require_once "includes/access_logger.php";

checkSessionTimeout();

requireLogin();


// Allow only Seller and Admin

if(
    $_SESSION['role']!="Seller" &&
    $_SESSION['role']!="Admin"
)
{
    die("Unauthorized access");
}


validateCSRFToken($_POST['csrf_token']);


$order_id = (int)$_POST['order_id'];
$status = trim($_POST['status']);


// Allowed statuses

$allowed_status = [
    "Pending",
    "Confirmed",
    "Shipped",
    "Delivered",
    "Cancelled"
];


if(!in_array($status,$allowed_status))
{
    die("Invalid status");
}


// Get current order status

$stmt = mysqli_prepare(
$con,
"SELECT status
 FROM orders
 WHERE id=?"
);


mysqli_stmt_bind_param(
$stmt,
"i",
$order_id
);


mysqli_stmt_execute($stmt);


$result = mysqli_stmt_get_result($stmt);


$current = mysqli_fetch_assoc($result);


if(!$current)
{
    die("Order not found");
}


$old_status = $current['status'];



// Status flow control

$allowed_flow = [

"Pending" => [
    "Confirmed",
    "Cancelled"
],

"Confirmed" => [
    "Shipped",
    "Cancelled"
],

"Shipped" => [
    "Delivered"
],

"Delivered" => [],

"Cancelled" => []

];


if(
!in_array(
$status,
$allowed_flow[$old_status]
)
)
{
    die("Invalid status transition");
}




// Seller can update only their own products orders

if($_SESSION['role']=="Seller")
{

    $seller_id = $_SESSION['id'];


    $stmt = mysqli_prepare(
    $con,
    "
    SELECT orders.id
    FROM orders

    JOIN order_items
    ON orders.id = order_items.order_id

    JOIN products
    ON order_items.product_id = products.id

    WHERE orders.id=?
    AND products.seller_id=?
    "
    );


    mysqli_stmt_bind_param(
    $stmt,
    "ii",
    $order_id,
    $seller_id
    );


    mysqli_stmt_execute($stmt);


    $check = mysqli_stmt_get_result($stmt);


    if(mysqli_num_rows($check)==0)
    {
        die("Unauthorized access");
    }

}
else
{
    // Admin access

    $seller_id = $_SESSION['id'];

}




// Update order status

$stmt = mysqli_prepare(
$con,
"UPDATE orders
SET status=?
WHERE id=?"
);


mysqli_stmt_bind_param(
$stmt,
"si",
$status,
$order_id
);



if(mysqli_stmt_execute($stmt))
{


    // Save status history

    $history = mysqli_prepare(
    $con,
    "
    INSERT INTO order_status_history
    (
    order_id,
    old_status,
    new_status,
    changed_by
    )

    VALUES(?,?,?,?)
    "
    );


    mysqli_stmt_bind_param(
    $history,
    "issi",
    $order_id,
    $old_status,
    $status,
    $seller_id
    );


    mysqli_stmt_execute($history);



    writeLog(
        "audit.log",
        $_SESSION['role']." ".$_SESSION['id'].
        " updated order ".$order_id.
        " from ".$old_status.
        " to ".$status
    );



}
else
{

    writeLog(
        "error.log",
        "Failed updating order ".$order_id
    );


    die("Unable to update order status");

}



// Redirect according to role

if($_SESSION['role']=="Admin")
{
    header("Location: admin_orders.php");
}
else
{
    header("Location: sellers_order.php");
}

exit();

?>