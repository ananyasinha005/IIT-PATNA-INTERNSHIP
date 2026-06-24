<?php

require_once "includes/session.php";
require_once "includes/auth.php";
require_once "includes/csrf.php";
require_once "config/database.php";
require_once "includes/logger.php";

checkSessionTimeout();

requireLogin();

requireRole("Admin");

validateCSRFToken($_POST['csrf_token']);


$user_id = (int)$_POST['user_id'];
$role = $_POST['role'];


$allowed_roles = [
    "Customer",
    "Seller",
    "Admin"
];


if(!in_array($role,$allowed_roles))
{
    die("Invalid role");
}


$stmt = $con->prepare(
"UPDATE users
SET role=?
WHERE ID=?"
);


$stmt->bind_param(
"si",
$role,
$user_id
);


if($stmt->execute())
{

writeLog(
"audit.log",
"Admin ".$_SESSION['id'].
" changed user ".$user_id.
" role to ".$role
);


header("Location: admin_users.php");
exit();

}
else
{
    die("Role update failed");
}

?>