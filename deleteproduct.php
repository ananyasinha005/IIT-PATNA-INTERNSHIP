<?php

require_once "includes/error_handler.php";
require_once "includes/security.php";
require_once "includes/auth.php";
require_once "includes/csrf.php";
require_once "includes/session.php";
require_once "config/database.php";
require_once "config/config.php";
require_once "includes/logger.php";
require_once "includes/db_logger.php";

checkSessionTimeout();

requireLogin();

requireRole("Seller");

validateCSRFToken($_POST['csrf_token']);

$product_id = (int)$_POST['product_id'];
$seller_id = (int)$_SESSION['id'];
logQuery(
    "DELETE FROM products WHERE id=? AND seller_id=?",
    [$product_id, $seller_id]
);
$stmt = $con->prepare(
    "DELETE FROM products
     WHERE id=? AND seller_id=?"
);

$stmt->bind_param(
    "ii",
    $product_id,
    $seller_id
);

if($stmt->execute())
{
    if($stmt->affected_rows > 0)
    {
        writeLog(
            "audit.log",
            "Seller ".$seller_id." deleted product ".$product_id
        );

        header("Location: manage_product.php");
        exit();
    }
    else
    {
        writeLog(
            "security.log",
            "Seller ".$seller_id." attempted deleting unauthorized/non-existent product ".$product_id
        );

        echo "Product not found";
    }
}
else
{
    writeLog(
        "error.log",
        "Delete product failed for seller ".$seller_id." product ".$product_id
    );

    echo "Unable to delete product";
}

?>