<?php

require_once "includes/error_handler.php";
require_once "includes/security.php";
require_once "includes/auth.php";
require_once "includes/session.php";
require_once "includes/csrf.php";
require_once "includes/logger.php";
require_once "includes/validator.php";

require_once "config/database.php";
require_once "config/config.php";

checkSessionTimeout();
requireLogin();

validateCSRFToken($_POST['csrf_token']);

$user_id = (int)$_SESSION['id'];

$full_name = cleanInput(trim($_POST['full_name']));
$phone = cleanInput(trim($_POST['phone']));
$address_line = cleanInput(trim($_POST['address_line']));
$city = cleanInput(trim($_POST['city']));
$state = cleanInput(trim($_POST['state']));
$pincode = cleanInput(trim($_POST['pincode']));

$stmt = $con->prepare(
"
INSERT INTO addresses
(
user_id,
full_name,
phone,
address_line,
city,
state,
pincode
)

VALUES(?,?,?,?,?,?,?)
"
);

$stmt->bind_param(
"issssss",
$user_id,
$full_name,
$phone,
$address_line,
$city,
$state,
$pincode
);

$stmt->execute();

writeLog(
"audit.log",
"User ".$user_id." added shipping address"
);

header("Location: my_addresses.php");
exit();