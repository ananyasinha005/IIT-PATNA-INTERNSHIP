<?php

require_once "includes/security.php";
require_once "includes/auth.php";
require_once "includes/session.php";
require_once "includes/csrf.php";
require_once "config/database.php";
require_once "config/config.php";
require_once "includes/logger.php";

checkSessionTimeout();
requireLogin();

validateCSRFToken($_POST['csrf_token']);


$user_id = (int)$_SESSION['id'];

$cart_id = (int)$_POST['cart_id'];


// Check cart belongs to logged-in user

$stmt = $con->prepare(
    "SELECT id 
     FROM cart
     WHERE id=? AND user_id=?"
);

$stmt->bind_param(
    "ii",
    $cart_id,
    $user_id
);

$stmt->execute();

$result = $stmt->get_result();


if($result->num_rows == 0)
{
    die("Unauthorized action");
}


// Delete item

$stmt = $con->prepare(
    "DELETE FROM cart
     WHERE id=? AND user_id=?"
);


$stmt->bind_param(
    "ii",
    $cart_id,
    $user_id
);


$stmt->execute();



writeLog(
    "audit.log",
    "User ".$user_id." removed cart item ".$cart_id
);



header("Location: cart.php");
exit();

?>