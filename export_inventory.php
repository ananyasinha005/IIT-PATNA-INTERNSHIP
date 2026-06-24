<?php

require_once "config/database.php";


header("Content-Type:text/csv");
header("Content-Disposition: attachment; filename=inventory.csv");


$output=fopen("php://output","w");


fputcsv($output,[
"Product ID",
"Product Name",
"Stock"
]);


$result=mysqli_query(
$con,
"SELECT id,name,stock FROM products"
);


while($row=mysqli_fetch_assoc($result))
{

fputcsv($output,$row);

}


fclose($output);

?>