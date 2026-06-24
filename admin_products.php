<?php


require_once "includes/error_handler.php";
require_once "includes/security.php";
require_once "includes/auth.php";
require_once "includes/headers.php";
require_once "includes/session.php";
require_once "includes/access_logger.php";
require_once "includes/logger.php";
require_once "includes/db_logger.php";
require_once "includes/csrf.php";

require_once "config/database.php";
require_once "config/config.php";

checkSessionTimeout();

requireLogin();

requireRole("Admin");

logQuery(
    "SELECT * FROM products",
    []
);

writeLog(
    "audit.log",
    "Admin ".$_SESSION['id']." viewed all products"
);

$stmt = $con->prepare(
    "SELECT * FROM products"
);

$stmt->execute();

$result = $stmt->get_result();

?>

<!DOCTYPE html>
<html>

<head>

<title>Manage Products | ShopKart</title>

<style>

body{
    background:#f1f5f9;
    font-family:'Poppins',sans-serif;
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

    width:90%;
    max-width:1200px;
    margin:40px auto;

}


.card{

    background:white;
    padding:25px;
    border-radius:20px;
    box-shadow:0 8px 25px rgba(0,0,0,.08);

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



button{

    background:#dc2626;
    color:white;
    border:none;
    padding:10px 18px;
    border-radius:8px;
    cursor:pointer;

}


button:hover{

    background:#991b1b;

}



.back{

    display:inline-block;
    margin-top:20px;
    padding:12px 20px;
    background:#2563eb;
    color:white;
    text-decoration:none;
    border-radius:10px;

}


</style>


</head>


<body>


<div class="header">

<h1>
📦 Manage Products
</h1>


<a href="admin_dashboard.php">
⬅ Dashboard
</a>


</div>



<div class="container">


<div class="card">


<table>


<tr>

<th>
Product Name
</th>

<th>
Price
</th>

<th>
Stock
</th>

<th>
Action
</th>

</tr>



<?php

while($row=mysqli_fetch_assoc($result))

{

?>


<tr>


<td>
<?= htmlspecialchars($row['name'], ENT_QUOTES,'UTF-8'); ?>
</td>



<td>
₹<?= htmlspecialchars($row['price'], ENT_QUOTES,'UTF-8'); ?>
</td>



<td>
<?= htmlspecialchars($row['stock'], ENT_QUOTES,'UTF-8'); ?>
</td>



<td>


<form method="POST" action="delete_product.php">


<input type="hidden"
name="product_id"
value="<?= (int)$row['id']; ?>">



<input type="hidden"
name="csrf_token"
value="<?= generateCSRFToken(); ?>">



<button type="submit"
onclick="return confirm('Delete this product?');">

🗑 Delete

</button>



</form>


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