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



header("Content-Type: text/csv");

header(
"Content-Disposition: attachment; filename=users.csv"
);



$output=fopen("php://output","w");



fputcsv(
$output,
[
"ID",
"Username",
"Email",
"Phone",
"Role"
]
);



$result=$con->query(
"SELECT ID,USERNAME,EMAIL,phone,role 
FROM users"
);



while($row=$result->fetch_assoc())
{

fputcsv(
$output,
$row
);

}


fclose($output);

exit();

?>