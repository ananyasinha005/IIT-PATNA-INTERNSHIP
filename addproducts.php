<?php
session_start();
require_once "includes/error_handler.php";
require_once "config/config.php";
require_once "includes/logger.php";
require_once "config/database.php";
require_once "includes/security.php";
require_once "includes/session.php";
require_once "includes/auth.php";
require_once "includes/csrf.php";
require_once "includes/headers.php";
require_once "includes/validator.php";
checkSessionTimeout();
requireLogin();
requireRole("Seller");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Product</title>
</head>
<body>

<h1>Add Product</h1>

<form action="saveproducts.php" method="POST" enctype="multipart/form-data">
    <input type="hidden"
       name="csrf_token"
       value="<?php echo generateCSRFToken(); ?>">

    <label>Product Name</label><br>
    <input type="text"
       name="name"
       maxlength="100"
       required><br><br>

    <label>Category ID</label><br>
    <input type="number" name="category_id" required><br><br>

    <label>Price</label><br>
    <input type="number"
       step="0.01"
       min="0"
       max="1000000"
       name="price"
       required><br><br>

    <label>Stock</label><br>
    <input type="number"
       name="stock"
       min="0"
       max="99999"
       required><br><br>

    <label>Description</label><br>
    <textarea
    name="description"
    maxlength="1000"></textarea><br><br>

    <label>Product Image</label><br>
    <input type="file"
       name="image"
       accept=".jpg,.jpeg,.png,.webp"
       required><br><br>

    <button type="submit">Add Product</button>

</form>

<br>
<a href="seller_dashboard.php">Back to Dashboard</a>

</body>
</html>