<?php

session_start();

require_once "includes/security.php";
require_once "includes/auth.php";
require_once "includes/csrf.php";
require_once "config/database.php";
require_once "config/config.php";
require_once "includes/logger.php";
require_once "includes/session.php";


checkSessionTimeout();

requireLogin();

requireRole("Admin");


validateCSRFToken($_POST['csrf_token']);


$user_id = (int)$_POST['user_id'];


// Prevent admin changing himself
if($user_id == $_SESSION['id'])
{
    die("Cannot change your own role");
}


// Update role

$stmt = $con->prepare(
"
UPDATE users
SET role='Seller'
WHERE ID=?
AND role='Customer'
"
);


$stmt->bind_param(
"i",
$user_id
);


$stmt->execute();



writeLog(
"audit.log",
"Admin ".$_SESSION['id']." promoted user ".$user_id." to Seller"
);



header("Location: admin_users.php");
exit();

?>