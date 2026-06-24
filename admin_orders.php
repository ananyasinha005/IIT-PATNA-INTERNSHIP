<?php



require_once "includes/error_handler.php";
require_once "includes/security.php";
require_once "includes/auth.php";
require_once "includes/headers.php";
require_once "includes/session.php";
require_once "includes/logger.php";
require_once "includes/db_logger.php"; 
require_once "config/database.php";
require_once "config/config.php";
require_once "includes/csrf.php";
require_once "includes/access_logger.php";
checkSessionTimeout();

requireLogin();

requireRole("Admin");

writeLog(
    "audit.log",
    "Admin ".$_SESSION['id']." viewed all orders"
);


$stmt = $con->prepare("SELECT id,user_id,total_amount,status, created_at FROM orders ORDER BY created_at DESC");
logQuery(
    "SELECT id,user_id,total_amount,status,created_at FROM orders ORDER BY created_at DESC",
    []
);
$stmt->execute();

$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html>

<head>

<title>Admin Orders | ShopKart</title>

<link rel="stylesheet" href="style.css">


<style>


body{

background:#f1f5f9;

}



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

}



.container{

width:95%;

max-width:1300px;

margin:40px auto;

}



.order-card{

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

padding:7px 15px;

border-radius:20px;

font-weight:600;

background:#e5e7eb;

}



select{

padding:10px;

border-radius:8px;

border:1px solid #ddd;

}



button{

background:#2563eb;

color:white;

border:none;

padding:10px 18px;

border-radius:8px;

cursor:pointer;

}



button:hover{

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
🛒 Manage Orders
</h1>


<a href="admin_dashboard.php">
⬅ Dashboard
</a>


</div>




<div class="container">



<div class="order-card">



<table>


<tr>

<th>
Order ID
</th>


<th>
User ID
</th>


<th>
Total
</th>


<th>
Status
</th>


<th>
Update Status
</th>


<th>
Date
</th>


</tr>




<?php


while($row=mysqli_fetch_assoc($result))

{


?>

<tr>



<td>

#<?= htmlspecialchars($row['id']); ?>

</td>




<td>

👤 <?= htmlspecialchars($row['user_id']); ?>

</td>




<td>

₹<?= htmlspecialchars($row['total_amount']); ?>

</td>




<td>

<span class="status">

<?= htmlspecialchars($row['status']); ?>

</span>

</td>




<td>


<form method="POST" action="update_status.php">


<input type="hidden"
name="order_id"
value="<?= (int)$row['id']; ?>">



<input type="hidden"
name="csrf_token"
value="<?= generateCSRFToken(); ?>">





<select name="status">


<option value="Pending">
Pending
</option>


<option value="Confirmed">
Confirmed
</option>


<option value="Shipped">
Shipped
</option>


<option value="Delivered">
Delivered
</option>


<option value="Cancelled">
Cancelled
</option>


</select>


<br><br>


<button type="submit">
Update
</button>


</form>



</td>




<td>

<?= htmlspecialchars($row['created_at']); ?>

</td>



</tr>


<?php

}

?>


</table>


</div>




<a class="back" href="admin_dashboard.php">

⬅ Back To Dashboard

</a>




</div>



</body>

</html>