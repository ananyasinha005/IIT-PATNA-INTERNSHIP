<?php

session_start();

require_once "includes/csrf.php";
require_once "config/database.php";
require_once "includes/logger.php";
require_once "includes/db_logger.php";
require_once "includes/access_logger.php";

validateCSRFToken($_POST['csrf_token']);


$email = trim($_POST['email']);



$stmt=$con->prepare(
"SELECT ID FROM users WHERE EMAIL=?"
);


$stmt->bind_param(
"s",
$email
);
logQuery(
"SELECT ID FROM users WHERE EMAIL=?",
[$email]
);

$stmt->execute();


$result=$stmt->get_result();



if($result->num_rows==0)
{
    die("If account exists, reset link will be sent.");
}


$user=$result->fetch_assoc();



$user_id=$user['ID'];


// Generate secure token

$token = bin2hex(
    random_bytes(32)
);


// Store HASHED token

$hashed_token=password_hash(
    $token,
    PASSWORD_BCRYPT
);



$expiry=date(
"Y-m-d H:i:s",
strtotime("+15 minutes")
);



$stmt=$con->prepare(

"INSERT INTO password_resets
(user_id,token,expires_at)
VALUES(?,?,?)"

);



$stmt->bind_param(
"iss",
$user_id,
$hashed_token,
$expiry
);

logQuery(
"INSERT INTO password_resets (user_id,token,expires_at) VALUES (?,?,?)",
[
$user_id,
"[HASHED_TOKEN]",
$expiry
]
);
$stmt->execute();



writeLog(
"audit.log",
"Password reset requested for user ".$user_id
);



// Normally email this link

echo "Reset link generated:<br>";

echo 
"reset_password.php?token=".$token."&id=".$user_id;


?>