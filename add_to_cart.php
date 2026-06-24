<?php


require_once "includes/error_handler.php";
require_once "includes/security.php";
require_once "includes/auth.php";
require_once "config/database.php";
require_once "config/config.php";
require_once "includes/headers.php";
require_once "includes/session.php";
require_once "includes/csrf.php";
require_once "includes/logger.php";
validateCSRFToken($_POST['csrf_token']);
checkSessionTimeout();
requireLogin();

if(!isset($_SESSION['id']))
{
    header("Location: home.php");
    exit();
}

$con = mysqli_connect(
    "127.0.0.1",
    "root",
    "root",
    "shopkart",
    3307
);

if(!$con)
{

    writeLog(
        "error.log",
        "Database connection failed: ".$con->connect_error
    );


    echo "Something went wrong. Please try again later.";

    exit();

}

$user_id = $_SESSION['id'];
$product_id = $_POST['id'];
$product_id = (int)$product_id;
$user_id = (int)$user_id;

// Check if product already in cart
$stmt = $con->prepare(
    "SELECT * FROM cart
     WHERE user_id=? AND product_id=?"
);

$stmt->bind_param(
    "ii",
    $user_id,
    $product_id
);

$stmt->execute();

$result = $stmt->get_result();
$stmt = $con->prepare(
    "SELECT stock
    FROM products
    WHERE id=?
    AND stock > 0"
);

$stmt->bind_param(
    "i",
    $product_id
);

$stmt->execute();

$product = $stmt->get_result()->fetch_assoc();

if(!$product)
{
    die("Product unavailable");
}

if(mysqli_num_rows($result) > 0)
{
    // Increase quantity
    $stmt = $con->prepare(
    "UPDATE cart
     SET quantity = quantity + 1
     WHERE user_id=? AND product_id=?"
);

$stmt->bind_param(
    "ii",
    $user_id,
    $product_id
);

$stmt->execute();
}
else
{
    // Add new product
    $stmt = $con->prepare(
    "INSERT INTO cart
    (user_id, product_id, quantity)
    VALUES(?,?,1)"
);

$stmt->bind_param(
    "ii",
    $user_id,
    $product_id
);

$stmt->execute();
}
writeLog(
"audit.log",
"User ".$user_id." added product ".$product_id." to cart"
);
header("Location: shopping.php");
exit();
?>