<?php





require_once "includes/security.php";
require_once "includes/auth.php";
require_once "config/database.php";
require_once "config/config.php";
require_once "includes/headers.php";
require_once "includes/session.php";
require_once "includes/logger.php";
require_once "includes/db_logger.php";
require_once "includes/access_logger.php";
checkSessionTimeout();
requireLogin();
requireRole("Seller");
writeLog(
"audit.log",
"Seller ".$_SESSION['id']." viewed seller orders"
);


$seller_id = (int)$_SESSION['id'];

$stmt = mysqli_prepare(
$con,
"SELECT
    orders.id AS order_id,
    orders.user_id,
    orders.status,
    orders.created_at,
    products.name,
    order_items.quantity,
    order_items.price

FROM order_items

JOIN products
ON order_items.product_id = products.id

JOIN orders
ON order_items.order_id = orders.id

WHERE products.seller_id = ?

ORDER BY orders.created_at DESC
"
);


mysqli_stmt_bind_param($stmt,"i",$seller_id);
logQuery(
"SELECT orders.id AS order_id,
orders.user_id,
orders.status,
orders.created_at,
products.name,
order_items.quantity,
order_items.price
FROM order_items
JOIN products ON order_items.product_id = products.id
JOIN orders ON order_items.order_id = orders.id
WHERE products.seller_id=?
ORDER BY orders.created_at DESC",
[$seller_id]
);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

?>

<!DOCTYPE html>
<html>

<head>

<title>
Seller Orders | ShopKart
</title>

<link rel="stylesheet" href="style.css">


<style>


body{

background:#f1f5f9;

}



/* Header */


.header{

background:#111827;

color:white;

padding:25px 40px;

display:flex;

justify-content:space-between;

align-items:center;

border-radius:0 0 20px 20px;

}



.header h1{

color:white;

}



.header a{

color:white;

text-decoration:none;

margin-left:20px;

}




.container{

width:95%;

max-width:1300px;

margin:40px auto;

}





.order-box{

background:white;

padding:25px;

border-radius:20px;

box-shadow:0 8px 25px rgba(0,0,0,.08);

overflow-x:auto;

}





table{

width:100%;

border-collapse:collapse;

}



th{

background:#111827;

color:white;

padding:15px;

}



td{

padding:15px;

text-align:center;

border-bottom:1px solid #eee;

}



tr:hover{

background:#f8fafc;

}





.status{

padding:8px 15px;

border-radius:20px;

font-weight:bold;

}




select{

padding:10px;

border-radius:8px;

border:1px solid #ddd;

}





.update-btn{

background:#2563eb;

color:white;

border:none;

padding:10px 18px;

border-radius:8px;

cursor:pointer;

}



.update-btn:hover{

background:#1d4ed8;

}




.back{

display:inline-block;

margin-top:25px;

background:#111827;

color:white;

padding:12px 20px;

border-radius:10px;

text-decoration:none;

}



</style>


</head>


<body>




<div class="header">


<h1>
🛒 Seller Orders
</h1>


<div>

<a href="seller_dashboard.php">
Dashboard
</a>


<a href="logout.php">
Logout
</a>


</div>


</div>





<div class="container">



<div class="order-box">



<table>


<tr>

<th>
Order ID
</th>

<th>
Customer
</th>

<th>
Product
</th>

<th>
Quantity
</th>

<th>
Price
</th>

<th>
Status
</th>

<th>
Date
</th>

<th>
Update
</th>


</tr>





<?php

while($row = mysqli_fetch_assoc($result))

{

?>

<tr>


<td>

<?= htmlspecialchars($row['order_id'], ENT_QUOTES, 'UTF-8'); ?>

</td>




<td>

👤 <?= htmlspecialchars($row['user_id'], ENT_QUOTES, 'UTF-8'); ?>

</td>




<td>

<?= htmlspecialchars($row['name'], ENT_QUOTES, 'UTF-8'); ?>

</td>




<td>

<?= htmlspecialchars($row['quantity'], ENT_QUOTES, 'UTF-8'); ?>

</td>




<td>

₹<?= htmlspecialchars($row['price'], ENT_QUOTES, 'UTF-8'); ?>

</td>





<td>

<span class="status">

<?= htmlspecialchars($row['status'], ENT_QUOTES, 'UTF-8'); ?>

</span>

</td>





<td>

<?= htmlspecialchars($row['created_at'], ENT_QUOTES, 'UTF-8'); ?>

</td>





<td>


<form action="update_status.php" method="POST">


<input type="hidden"
name="csrf_token"
value="<?= generateCSRFToken(); ?>">



<input type="hidden"
name="order_id"
value="<?= (int)$row['order_id']; ?>">





<select name="status">


<option value="Pending"
<?= $row['status']=="Pending"?"selected":""; ?>>

Pending

</option>



<option value="Confirmed"
<?= $row['status']=="Confirmed"?"selected":""; ?>>

Confirmed

</option>



<option value="Shipped"
<?= $row['status']=="Shipped"?"selected":""; ?>>

Shipped

</option>



<option value="Delivered"
<?= $row['status']=="Delivered"?"selected":""; ?>>

Delivered

</option>



<option value="Cancelled"
<?= $row['status']=="Cancelled"?"selected":""; ?>>

Cancelled

</option>



</select>



<br><br>



<button class="update-btn"
type="submit">

Update

</button>



</form>


</td>


</tr>


<?php

}

?>


</table>


</div>





<a class="back"
href="seller_dashboard.php">

⬅ Back To Dashboard

</a>




</div>


</body>

</html>