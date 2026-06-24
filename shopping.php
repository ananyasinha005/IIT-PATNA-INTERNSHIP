
<?php
require_once "includes/auth.php";
require_once "config/database.php";
require_once "includes/csrf.php";
require_once "includes/security.php";
require_once "includes/headers.php";
require_once "includes/session.php";
require_once "includes/logger.php";
require_once "includes/db_logger.php";
require_once "includes/access_logger.php";
requireLogin();
writeLog(
    "audit.log",
    "User ".$_SESSION['id']." viewed shopping page"
);
$sort = $_GET['sort'] ?? "";

$order_by = "id DESC";

if($sort == "low")
{
    $order_by = "price ASC";
}
elseif($sort == "high")
{
    $order_by = "price DESC";
}
elseif($sort == "new")
{
    $order_by = "id DESC";
}

if(isset($_GET['category']))
{ logQuery(
"SELECT * FROM products WHERE category_id=? ORDER BY ".$order_by, [$category_id]);
    $category_id = $_GET['category'];

    $stmt = mysqli_prepare(
        $con,
        "SELECT * FROM products
 WHERE category_id=?
 ORDER BY $order_by"
    );

    mysqli_stmt_bind_param($stmt,"i",$category_id);

}
elseif(isset($_GET['search']))
{logQuery(
"SELECT * FROM products WHERE name LIKE ? ORDER BY ".$order_by,
[$search]
);
    $search = "%".substr(trim($_GET['search']),0,50)."%";
    writeLog(
    "audit.log",
    "User ".$_SESSION['id']." searched products"
);
    $stmt = mysqli_prepare(
        $con,
        "SELECT * FROM products
 WHERE name LIKE ?
 ORDER BY $order_by"
    );

    mysqli_stmt_bind_param($stmt,"s",$search);

}
else
{ logQuery(
"SELECT * FROM products ORDER BY ".$order_by,
[]
);
    $stmt = mysqli_prepare(
        $con,
        "SELECT * FROM products
 ORDER BY $order_by"
    );
}


mysqli_stmt_execute($stmt);


$result = mysqli_stmt_get_result($stmt);
$user_id = $_SESSION['id'];

$stmt = mysqli_prepare(
    $con,
    "SELECT SUM(quantity) AS total_items 
     FROM cart 
     WHERE user_id=?"
);

mysqli_stmt_bind_param($stmt,"i",$user_id);
logQuery(
"SELECT SUM(quantity) AS total_items FROM cart WHERE user_id=?",
[$user_id]
);
mysqli_stmt_execute($stmt);

$cart_result = mysqli_stmt_get_result($stmt);

$cart_row = mysqli_fetch_assoc($cart_result);

$cart_count = $cart_row['total_items'];

if($cart_count == NULL)
{
    $cart_count = 0;
}
?>
<!DOCTYPE html>
<html>
<head>
    <head>

<title>ShopKart | Products</title>

<link rel="stylesheet" href="style.css">

<style>

body{
    background:#f5f7fb;
}


/* Top Navigation */

.top-bar{

    display:flex;

    justify-content:space-between;

    align-items:center;

    padding:18px 40px;

    background:#111827;

    color:white;

    margin-bottom:30px;

}


.top-bar a{

    color:white;

    text-decoration:none;

    font-weight:500;

    margin-left:20px;

}


.top-bar a:hover{

    color:#60a5fa;

}



/* Search */

.search-box{

    background:white;

    padding:20px;

    border-radius:15px;

    box-shadow:0 5px 20px rgba(0,0,0,.08);

    margin-bottom:30px;

}


.search-box input{

    width:300px;

}



.search-box select{

    width:180px;

}



/* Product grid */

.product-container{

    display:grid;

    grid-template-columns:
    repeat(auto-fit,minmax(250px,1fr));

    gap:25px;

}



/* Product card */

.product-card{

    background:white;

    border-radius:18px;

    padding:20px;

    text-align:center;

    box-shadow:0 5px 20px rgba(0,0,0,.08);

    transition:.3s;

}


.product-card:hover{

    transform:translateY(-8px);

    box-shadow:0 10px 30px rgba(0,0,0,.15);

}



.product-card img{

    width:200px;

    height:200px;

    object-fit:cover;

    border-radius:12px;

}



.product-card h3{

    margin:15px 0;

}



.price{

    font-size:22px;

    font-weight:bold;

    color:#2563eb;

}



.stock{

    color:green;

    font-weight:500;

}



.out-stock{

    color:red;

    font-weight:bold;

}



.category{

    margin-top:30px;

    text-align:center;

    font-size:18px;

}


.category a{

    margin:10px;

    font-weight:500;

}


</style>

</head>


<body>
    <div class="top-bar">

<div>
<h2>🛒 ShopKart</h2>
</div>

    <a href="cart.php">
        🛒 Cart (<?php echo $cart_count; ?>)
    </a>

    <a href="logout.php">
        Logout
    </a>
    <a href="my_account.php">
    👤 My Account
</a>
<a href="my_orders.php">
📦 My Orders
</a>

</div>

<div class="container">


<div class="search-box">


<form method="GET" style="text-align:center; margin-bottom:20px;">

<input
type="text"
name="search"
placeholder="🔍 Search Products">
<select name="sort">

<option value="">
Sort By
</option>

<option value="low">
Price Low → High
</option>

<option value="high">
Price High → Low
</option>

<option value="new">
Newest First
</option>

</select>

<button type="submit">
Search
</button>

</form>

</div>

<?php

$cat_stmt = $con->prepare(
    "SELECT * FROM categories"
);



?>
<?php

$cat_stmt->execute();

$cat_result = $cat_stmt->get_result();
while($row = mysqli_fetch_assoc($result))
{
?>

<div class="card">

    <img src="uploads/<?= htmlspecialchars($row['image'], ENT_QUOTES, 'UTF-8'); ?>" class="product-img">
    <h3>
<a href="product_details.php?id=<?= (int)$row['id']; ?>">
<?= htmlspecialchars($row['name'], ENT_QUOTES, 'UTF-8'); ?>
</a>
</h3>

   <p>
₹<?= htmlspecialchars($row['price'], ENT_QUOTES, 'UTF-8'); ?>
</p>

    <?php
if($row['stock'] > 0)
{
?>
    <p>
Stock:
<?= htmlspecialchars($row['stock'], ENT_QUOTES, 'UTF-8'); ?>
</p>

    <form action="add_to_cart.php" method="POST">

<input type="hidden"
name="csrf_token"
value="<?php echo generateCSRFToken(); ?>">

<input type="hidden"
name="id"
value="<?php echo $row['id']; ?>">

<button type="submit">
🛒 Add to Cart
</button>

</form>
<?php
}
else
{
?>
    <p>Out Of Stock</p>
<?php
}
?>

    
       


</div>
<?php
}
?>




<?php



echo "<center>";

echo "<a href='shopping.php'>All Products</a> | ";

while($cat = mysqli_fetch_assoc($cat_result))
{
    echo "<a href='shopping.php?category=".$cat['id']."'>".$cat['name']."</a> | ";
}

echo "</center><br>";

?>



</div>


</body>
</html>