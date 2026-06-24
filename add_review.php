<?php

require_once "includes/error_handler.php";
require_once "includes/security.php";
require_once "includes/auth.php";
require_once "includes/session.php";
require_once "includes/csrf.php";
require_once "includes/logger.php";
require_once "config/database.php";
require_once "config/config.php";


checkSessionTimeout();

requireLogin();


validateCSRFToken($_POST['csrf_token']);



$user_id = (int)$_SESSION['id'];

$product_id = (int)$_POST['product_id'];

$rating = (int)$_POST['rating'];

$review = trim($_POST['review']);



// Validate rating

if($rating < 1 || $rating > 5)
{
    die("Invalid rating");
}



// Check if user purchased this product

$stmt = $con->prepare(
"SELECT order_items.id

FROM order_items

JOIN orders
ON order_items.order_id = orders.id

WHERE orders.user_id=?
AND order_items.product_id=?

"
);


$stmt->bind_param(
"ii",
$user_id,
$product_id
);


$stmt->execute();


$result = $stmt->get_result();


if($result->num_rows == 0)
{if($result->num_rows == 0)
{
    echo "Purchase check failed";
    exit();
}
    die("You can review only purchased products");
}



// Check duplicate review

$stmt = $con->prepare(
"SELECT id
FROM reviews
WHERE user_id=?
AND product_id=?
"
);


$stmt->bind_param(
"ii",
$user_id,
$product_id
);


$stmt->execute();


$result = $stmt->get_result();


if($result->num_rows > 0)
{
    die("You already reviewed this product");
}




// Insert review

$stmt = $con->prepare(
"INSERT INTO reviews
(
user_id,
product_id,
rating,
review
)

VALUES(?,?,?,?)

"
);



$stmt->bind_param(
"iiis",
$user_id,
$product_id,
$rating,
$review
);



if($stmt->execute())
{

    writeLog(
        "audit.log",
        "User ".$user_id." added review for product ".$product_id
    );


    header(
        "Location: product_details.php?id=".$product_id
    );

    exit();

}
else
{

    writeLog(
        "error.log",
        "Failed review insert by user ".$user_id
    );


    echo "Unable to add review";

}

?>