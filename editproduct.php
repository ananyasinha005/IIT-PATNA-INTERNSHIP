<?php



require_once "includes/error_handler.php";
require_once "includes/session.php";
require_once "includes/logger.php";
require_once "includes/security.php";
require_once "includes/auth.php";
require_once "config/database.php";
require_once "config/config.php";
require_once "includes/csrf.php";

checkSessionTimeout();
requireLogin();
requireRole("Seller");





$id = (int)$_GET['id'];
$seller_id = (int)$_SESSION['id'];


// fetch product
$stmt = $con->prepare(
    "SELECT *
     FROM products
     WHERE id=? AND seller_id=?"
);

$stmt->bind_param(
    "ii",
    $id,
    $seller_id
);

$stmt->execute();

$product = $stmt->get_result()->fetch_assoc();
if(!$product)
{
    writeLog(
        "security.log",
        "Seller ".$seller_id." tried accessing invalid product ".$id
    );

    die("Product not found");
}

if(isset($_POST['update']))
{

validateCSRFToken($_POST['csrf_token']);

$name = trim($_POST['name']);
$price = (float)$_POST['price'];
$stock = (int)$_POST['stock'];
$description = trim($_POST['description']);


$stmt = $con->prepare(
"UPDATE products
SET name=?,
    price=?,
    stock=?,
    description=?
WHERE id=?
AND seller_id=?"
);

$stmt->bind_param(
    "sdisii",
    $name,
    $price,
    $stock,
    $description,
    $id,
    $seller_id
);

if($stmt->execute())
{ writeLog(
    "audit.log",
    "Seller ".$seller_id." updated product ".$id
);
    header("Location: manage_product.php");
    exit();
}

}

?>


<!DOCTYPE html>
<html>
<head>
<title>Edit Product</title>
</head>

<body>


<h1>Edit Product</h1>


<form method="POST">
    <input type="hidden"
       name="csrf_token"
       value="<?php echo generateCSRFToken(); ?>">


<label>Name</label><br>
<input type="text" name="name" 
value="<?php echo htmlspecialchars($product['price']); ?>">
<br><br>


<label>Price</label><br>
<input type="number" name="price"
value="<?= htmlspecialchars($product['price'], ENT_QUOTES, 'UTF-8'); ?>">
<br><br>


<label>Stock</label><br>
<input type="number" name="stock"
<?= htmlspecialchars($product['stock'], ENT_QUOTES, 'UTF-8'); ?>>
<br><br>


<label>Description</label><br>

<textarea name="description"><?= htmlspecialchars($product['description'], ENT_QUOTES, 'UTF-8'); ?></textarea>

<br><br>


<button name="update">
Update Product
</button>


</form>


</body>
</html>