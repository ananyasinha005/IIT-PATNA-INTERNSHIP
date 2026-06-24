<?php
require_once "includes/error_handler.php";
require_once "includes/security.php";
require_once "includes/auth.php";
require_once "includes/csrf.php";
require_once "includes/logger.php";

require_once "config/database.php";
require_once "config/config.php";

require_once "includes/session.php";

checkSessionTimeout();

requireLogin();

requireRole("Admin");


validateCSRFToken($_POST['csrf_token']);


$product_id = (int)$_POST['product_id'];


// Get product image before deleting
$stmt = $con->prepare(
    "SELECT image 
     FROM products 
     WHERE id=?"
);

$stmt->bind_param(
    "i",
    $product_id
);

$stmt->execute();

$result = $stmt->get_result();

$product = $result->fetch_assoc();


if(!$product)
{
    writeLog(
        "security.log",
        "Admin ".$_SESSION['id']." attempted deleting invalid product ".$product_id
    );

    echo "Product not found";
    exit();
}


// Delete product
$stmt = $con->prepare(
    "DELETE FROM products 
     WHERE id=?"
);

$stmt->bind_param(
    "i",
    $product_id
);


if($stmt->execute())
{

    // Delete image from uploads folder
    if(!empty($product['image']))
    {
        $image_path = "uploads/".$product['image'];

        if(file_exists($image_path))
        {
            unlink($image_path);
        }
    }


    writeLog(
        "audit.log",
        "Admin ".$_SESSION['id']." deleted product ".$product_id
    );


    header("Location: admin_products.php");
    exit();

}
else
{
    echo "Failed to delete product";
}

?>