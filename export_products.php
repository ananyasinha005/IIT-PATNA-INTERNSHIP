<?php

require_once "config/database.php";

header("Content-Type: text/csv");
header("Content-Disposition: attachment; filename=products.csv");


$output = fopen("php://output","w");


fputcsv($output,[
"ID",
"Name",
"Price",
"Stock",
"Seller ID"
]);


$result = mysqli_query(
$con,
"SELECT id,name,price,stock,seller_id FROM products"
);


while($row=mysqli_fetch_assoc($result))
{

fputcsv($output,$row);

}


fclose($output);

?>