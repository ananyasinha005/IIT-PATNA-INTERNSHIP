<?php

require_once "config/database.php";


header("Content-Type:text/csv");
header("Content-Disposition: attachment; filename=orders.csv");


$output=fopen("php://output","w");


fputcsv($output,[
"Order ID",
"User ID",
"Amount",
"Status",
"Date"
]);


$result=mysqli_query(
$con,
"SELECT id,user_id,total_amount,status,created_at FROM orders"
);



while($row=mysqli_fetch_assoc($result))
{

fputcsv($output,$row);

}


fclose($output);

?>