<?php

require_once "includes/auth.php";
require_once "includes/security.php";
require_once "config/database.php";
require_once "config/config.php";
require_once "includes/headers.php";
require_once "includes/session.php";
require_once "includes/db_logger.php";
require_once "includes/access_logger.php";
checkSessionTimeout();
requireLogin();



$id = (int)$_GET['id'];
logQuery(
    "SELECT * FROM products WHERE id=?",
    [$id]
);
$stmt = $con->prepare(
    "SELECT *
     FROM products
     WHERE id=?"
);

$stmt->bind_param(
    "i",
    $id
);

$stmt->execute();

$result = $stmt->get_result();

$product = $result->fetch_assoc();



if(!$product)
{
    die("Product not found");
}
?>

<!DOCTYPE html>
<html>

<head>

<title>
<?= htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8'); ?>
</title>

<link rel="stylesheet" href="style.css">


<style>


.product-page{

    width:90%;

    max-width:1100px;

    margin:40px auto;

    display:grid;

    grid-template-columns:1fr 1fr;

    gap:40px;

}



/* Image section */

.product-image{

    background:white;

    padding:25px;

    border-radius:20px;

    box-shadow:0 5px 20px rgba(0,0,0,.1);

    text-align:center;

}



.product-image img{

    width:100%;

    max-width:450px;

    height:450px;

    object-fit:cover;

    border-radius:15px;

}



/* Details */

.product-info{

    background:white;

    padding:30px;

    border-radius:20px;

    box-shadow:0 5px 20px rgba(0,0,0,.1);

}



.product-info h1{

    font-size:35px;

    margin-bottom:20px;

}



.price{

    font-size:30px;

    font-weight:bold;

    color:#2563eb;

}



.stock{

    color:green;

    font-weight:bold;

}



.description{

    margin-top:20px;

    line-height:1.7;

}



/* Review box */


.review-box{

    margin-top:30px;

    background:white;

    padding:25px;

    border-radius:15px;

    box-shadow:0 5px 20px rgba(0,0,0,.1);

}



textarea{

    height:120px;

}



/* Buttons */


.action-btn{

    width:100%;

    margin-top:15px;

}



@media(max-width:800px){

.product-page{

    grid-template-columns:1fr;

}

}



</style>


</head>


<body>


<div class="product-page">



<!-- Image -->

<div class="product-image">


<img src="uploads/<?= htmlspecialchars($product['image'], ENT_QUOTES, 'UTF-8'); ?>">


</div>





<!-- Details -->


<div class="product-info">


<h1>
<?= htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8'); ?>
</h1>



<p class="price">

₹<?= htmlspecialchars($product['price'], ENT_QUOTES, 'UTF-8'); ?>

</p>



<br>



<p class="stock">

✔ Available Stock:
<?= htmlspecialchars($product['stock'], ENT_QUOTES, 'UTF-8'); ?>

</p>




<div class="description">

<h3>Description</h3>

<p>

<?= htmlspecialchars($product['description'], ENT_QUOTES, 'UTF-8'); ?>

</p>

</div>




<form action="add_to_cart.php" method="POST">


<input type="hidden"
name="id"
value="<?= (int)$product['id']; ?>">



<input type="hidden"
name="csrf_token"
value="<?= generateCSRFToken(); ?>">



<button class="action-btn">

🛒 Add To Cart

</button>


</form>



<br>


<a href="shopping.php">

⬅ Continue Shopping

</a>



</div>


</div>






<!-- Review Section -->


<div class="review-box container">


<h2>
⭐ Add Review
</h2>



<form action="add_review.php" method="POST">


<input type="hidden"
name="csrf_token"
value="<?= generateCSRFToken(); ?>">



<input type="hidden"
name="product_id"
value="<?= $product['id']; ?>">



<select name="rating">


<option value="5">
⭐⭐⭐⭐⭐
</option>


<option value="4">
⭐⭐⭐⭐
</option>


<option value="3">
⭐⭐⭐
</option>


<option value="2">
⭐⭐
</option>


<option value="1">
⭐
</option>


</select>



<textarea
name="review"
placeholder="Write your review..."
required></textarea>




<button>

Submit Review

</button>



</form>


</div>



</body>

</html>