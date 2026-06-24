<?php

require_once "includes/session.php";
require_once "includes/error_handler.php";
require_once "includes/logger.php";
require_once "includes/csrf.php";
require_once "includes/security.php";
require_once "includes/auth.php";
require_once "config/database.php";
require_once "config/config.php";
require_once "includes/headers.php";

require_once "includes/db_logger.php";
require_once "includes/access_logger.php";
checkSessionTimeout();
requireLogin();
requireRole("Seller");

writeLog(
    "audit.log",
    "Seller ".$_SESSION['id']." viewed product list"
);



$seller_id = $_SESSION['id'];
logQuery(
    "SELECT * FROM products WHERE seller_id=?",
    [$seller_id]
);
$stmt = $con->prepare(
    "SELECT *
     FROM products
     WHERE seller_id=?"
);

$stmt->bind_param(
    "i",
    $seller_id
);

$stmt->execute();

$result = $stmt->get_result();
if($result->num_rows == 0)
{
    echo "<h3 align='center'>No products added yet.</h3>";
}
?>

<!DOCTYPE html>
<html>

<head>

<title>My Products | ShopKart</title>

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

margin-left:20px;

}





.container{

width:90%;

max-width:1200px;

margin:40px auto;

}




.product-table{

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

border-bottom:1px solid #eee;

text-align:center;

}




tr:hover{

background:#f8fafc;

}



.product-img{

width:90px;

height:90px;

object-fit:cover;

border-radius:12px;

}




.edit-btn{

background:#2563eb;

color:white;

padding:8px 15px;

border-radius:8px;

text-decoration:none;

}



.edit-btn:hover{

background:#1d4ed8;

}




.delete-btn{

background:#dc2626;

color:white;

padding:8px 15px;

border:none;

border-radius:8px;

cursor:pointer;

}



.delete-btn:hover{

background:#991b1b;

}




.back-btn{

display:inline-block;

margin-top:30px;

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
📦 My Products
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



<?php

if($result->num_rows == 0)
{

echo "

<div class='product-table'>

<h2 align='center'>
No products added yet.
</h2>

</div>

";

}

?>





<div class="product-table">


<table>


<tr>

<th>
Image
</th>

<th>
Name
</th>

<th>
Price
</th>

<th>
Stock
</th>

<th>
Description
</th>

<th>
Action
</th>


</tr>




<?php

while($row = mysqli_fetch_assoc($result))

{

?>

<tr>


<td>

<img class="product-img"
src="uploads/<?= htmlspecialchars($row['image'], ENT_QUOTES, 'UTF-8'); ?>">

</td>




<td>

<?= htmlspecialchars($row['name'], ENT_QUOTES, 'UTF-8'); ?>

</td>





<td>

₹<?= htmlspecialchars($row['price']); ?>

</td>





<td>

<?= htmlspecialchars($row['stock']); ?>

</td>





<td>

<?= htmlspecialchars($row['description']); ?>

</td>





<td>


<a class="edit-btn"
href="editproduct.php?id=<?= (int)$row['id']; ?>">

✏ Edit

</a>


<br><br>



<form method="POST" action="deleteproduct.php">


<input
type="hidden"
name="product_id"
value="<?= (int)$row['id']; ?>">



<input
type="hidden"
name="csrf_token"
value="<?= generateCSRFToken(); ?>">



<button class="delete-btn"
type="submit"
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




<center>

<a class="back-btn"
href="seller_dashboard.php">

⬅ Back To Dashboard

</a>

</center>




</div>


</body>

</html>