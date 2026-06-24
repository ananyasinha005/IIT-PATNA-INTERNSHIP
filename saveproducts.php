<?php



require_once "includes/csrf.php";

validateCSRFToken($_POST['csrf_token']);

require_once "includes/security.php";
require_once "includes/auth.php";
require_once "config/database.php";
require_once "config/config.php";
require_once "includes/headers.php";
require_once "includes/session.php";
require_once "includes/logger.php";
require_once "includes/validator.php";
require_once "includes/db_logger.php";
checkSessionTimeout();
requireLogin();
requireRole("Seller");



$seller_id = (int)$_SESSION['id'];

$category_id = (int)$_POST['category_id'];

$name = cleanInput(trim($_POST['name']));

$price = (float)$_POST['price'];

$stock = (int)$_POST['stock'];

$description = cleanInput(trim($_POST['description']));


$image_name = $_FILES['image']['name'];
$temp_name = $_FILES['image']['tmp_name'];

$allowed_types = [
    "image/jpeg",
    "image/png",
    "image/webp"
];


$file_info = finfo_open(FILEINFO_MIME_TYPE);

$mime = finfo_file(
    $file_info,
    $_FILES['image']['tmp_name']
);

finfo_close($file_info);


if(!in_array($mime,$allowed_types))
{
    die("Invalid image type");
}


if($_FILES['image']['size'] > 2 * 1024 * 1024)
{
    die("Image too large");
}


$extension = strtolower(
    pathinfo($image_name, PATHINFO_EXTENSION)
);


$allowed_extensions = [
    "jpg",
    "jpeg",
    "png",
    "webp"
];


if(!in_array($extension,$allowed_extensions))
{
    die("Invalid extension");
}

$new_name = uniqid().".".$extension;


move_uploaded_file(
    $temp_name,
    "uploads/".$new_name
);

$image_name = $new_name;

$stmt = $con->prepare(
"INSERT INTO products
(seller_id, category_id, name, price, stock, image, description)

VALUES
(?,?,?,?,?,?,?)"
);


$stmt->bind_param(
    "iisdiss",
    $seller_id,
    $category_id,
    $name,
    $price,
    $stock,
    $image_name,
    $description
);

logQuery(
"INSERT INTO products (seller_id, category_id, name, price, stock, image, description) VALUES (?,?,?,?,?,?,?)",
[
$seller_id,
$category_id,
$name,
$price,
$stock,
$image_name,
$description
]
);
$result = $stmt->execute();

if($result)
{writeLog(
"audit.log",
"Seller ".$seller_id." added product ".$name
);
    echo "Product Added Successfully";
    echo "<br><a href='seller_dashboard.php'>Back to Dashboard</a>";
}
else
{
    echo "Something went wrong. Please try again.";
}
?>