
<?php

require_once "includes/logger.php";
require_once "includes/security.php";
require_once "includes/auth.php";
require_once "config/database.php";
require_once "config/config.php";
require_once "includes/headers.php";
require_once "includes/session.php";
require_once "includes/db_logger.php";
require_once "includes/access_logger.php";
checkSessionTimeout();
requireLogin();
requireRole("Seller");
writeLog(
    "audit.log",
    "Seller ".$_SESSION['id']." opened dashboard"
);
$seller_id = $_SESSION['id'];
$stmt = mysqli_prepare(
    $con,
    "SELECT COUNT(*) AS total_products 
     FROM products 
     WHERE seller_id=?"
);

mysqli_stmt_bind_param($stmt, "i", $seller_id);

logQuery(
"SELECT COUNT(*) AS total_products FROM products WHERE seller_id=?",
[$seller_id]
);
mysqli_stmt_execute($stmt);

$product_query = mysqli_stmt_get_result($stmt);

$product_data = mysqli_fetch_assoc($product_query);
$total_products = $product_data['total_products'];
$stmt = mysqli_prepare(
    $con,
    "SELECT COUNT(DISTINCT orders.id) AS total_orders
     FROM orders
     JOIN order_items
     ON orders.id = order_items.order_id
     JOIN products
     ON order_items.product_id = products.id
     WHERE products.seller_id=?"
);

mysqli_stmt_bind_param($stmt,"i",$seller_id);
logQuery(
"SELECT COUNT(DISTINCT orders.id) AS total_orders
FROM orders
JOIN order_items ON orders.id = order_items.order_id
JOIN products ON order_items.product_id = products.id
WHERE products.seller_id=?",
[$seller_id]
);
mysqli_stmt_execute($stmt);

$order_query = mysqli_stmt_get_result($stmt);

$order_data = mysqli_fetch_assoc($order_query);
$total_orders = $order_data['total_orders'];

$stmt = mysqli_prepare(
$con,
"SELECT SUM(order_items.price * order_items.quantity) AS revenue
 FROM order_items
JOIN products
ON order_items.product_id=products.id

JOIN orders
ON order_items.order_id=orders.id

WHERE products.seller_id=?
AND orders.status!='Cancelled'
);

mysqli_stmt_bind_param($stmt,"i",$seller_id);
logQuery(
"SELECT SUM(order_items.price * order_items.quantity) AS revenue
FROM order_items
JOIN products ON order_items.product_id = products.id
WHERE products.seller_id=? AND orders.status != 'Cancelled'",
[$seller_id]
);
mysqli_stmt_execute($stmt);

$revenue_query = mysqli_stmt_get_result($stmt);

$revenue_data = mysqli_fetch_assoc($revenue_query);

$revenue = $revenue_data['revenue'] ?? 0;


?>




<!DOCTYPE html>
<html>

<head>

<title>
Seller Dashboard | ShopKart
</title>

<link rel="stylesheet" href="style.css">


<style>


body{

background:#f1f5f9;

}


/* Header */

.seller-header{

background:#111827;

color:white;

padding:25px 40px;

display:flex;

justify-content:space-between;

align-items:center;

border-radius:0 0 20px 20px;

}


.seller-header h1{

color:white;

}



.seller-header a{

color:white;

margin-left:20px;

}




/* Main */

.seller-container{

width:90%;

max-width:1200px;

margin:40px auto;

}





/* Stats */

.stats{

display:grid;

grid-template-columns:
repeat(auto-fit,minmax(250px,1fr));

gap:25px;

}




.stat-card{

background:white;

padding:25px;

border-radius:20px;

box-shadow:0 8px 25px rgba(0,0,0,.08);

transition:.3s;

}



.stat-card:hover{

transform:translateY(-8px);

}



.stat-card h3{

color:#64748b;

}



.stat-card h2{

font-size:35px;

color:#2563eb;

}





/* Actions */

.actions{

margin-top:40px;

background:white;

padding:30px;

border-radius:20px;

box-shadow:0 5px 20px rgba(0,0,0,.08);

}




.action-card{

display:inline-block;

background:#111827;

color:white;

padding:20px;

margin:15px;

border-radius:15px;

width:220px;

transition:.3s;

}



.action-card:hover{

background:#2563eb;

transform:translateY(-5px);

}



.action-card a{

color:white;

text-decoration:none;

}



.action-card h2{

color:white;

font-size:20px;

}



</style>


</head>



<body>



<div class="seller-header">


<h1>
🛒 ShopKart Seller Panel
</h1>


<div>

<a href="my_account.php">
👤 Account
</a>


<a href="logout.php">
Logout
</a>


</div>


</div>





<div class="seller-container">



<h1>
Seller Dashboard
</h1>


<br>




<div class="stats">



<div class="stat-card">

<h3>
📦 My Products
</h3>

<h2>
<?= htmlspecialchars($total_products, ENT_QUOTES, 'UTF-8'); ?>
</h2>

</div>




<div class="stat-card">

<h3>
🛒 Orders
</h3>

<h2>
<?= htmlspecialchars($total_orders, ENT_QUOTES, 'UTF-8'); ?>
</h2>

</div>




<div class="stat-card">

<h3>
💰 Revenue
</h3>

<h2>
₹<?= htmlspecialchars($revenue, ENT_QUOTES, 'UTF-8'); ?>
</h2>

</div>



</div>






<div class="actions">


<h2>
Seller Management
</h2>



<div class="action-card">

<a href="addproducts.php">

<h2>
➕ Add Product
</h2>

</a>

</div>




<div class="action-card">

<a href="manage_product.php">

<h2>
📦 My Products
</h2>

</a>

</div>





<div class="action-card">

<a href="sellers_order.php">

<h2>
🛒 Orders
</h2>

</a>

</div>



</div>



</div>


</body>

</html>