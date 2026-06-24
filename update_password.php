<?php

session_start();
require_once "includes/mailer.php";
require_once "config/database.php";
require_once "includes/logger.php";


$user_id=$_POST['user_id'];
$token=$_POST['token'];
$password=$_POST['password'];



$stmt=$con->prepare(

"SELECT *
FROM password_resets
WHERE user_id=?
AND used=0
AND expires_at > NOW()"

);


$stmt->bind_param(
"i",
$user_id
);


$stmt->execute();


$result=$stmt->get_result();


$row=$result->fetch_assoc();



if(!$row)
{
    die("Invalid or expired token");
}



// verify token

if(!password_verify($token,$row['token']))
{
    die("Invalid token");
}



// update password


$new_password=password_hash(
$password,
PASSWORD_BCRYPT
);



$stmt=$con->prepare(

"UPDATE users
SET PASSWORD=?
WHERE ID=?"
// Get user email for notification

$email_stmt = $con->prepare(
    "SELECT EMAIL, USERNAME FROM users WHERE ID=?"
);

$email_stmt->bind_param(
    "i",
    $user_id
);

$email_stmt->execute();

$email_result = $email_stmt->get_result();

$user_data = $email_result->fetch_assoc();



// Send password change email

sendEmail(
    $user_data['EMAIL'],
    "Password Changed - ShopKart",
    "
    <h2>Password Changed Successfully</h2>

    <p>Hello ".$user_data['USERNAME'].",</p>

    <p>
    Your ShopKart account password was changed successfully.
    </p>

    <p>
    If you did not make this change,
    please contact support immediately.
    </p>
    "
);
);



$stmt->bind_param(
"si",
$new_password,
$user_id
);


$stmt->execute();



// destroy token

$stmt=$con->prepare(

"UPDATE password_resets
SET used=1
WHERE id=?"

);


$stmt->bind_param(
"i",
$row['id']
);


$stmt->execute();



writeLog(
"audit.log",
"Password changed for user ".$user_id
);


echo "Password changed successfully";

?>