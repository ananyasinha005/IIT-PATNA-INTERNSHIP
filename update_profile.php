

<?php



require_once "includes/security.php";
require_once "includes/csrf.php";
require_once "config/database.php";
require_once "config/config.php";
require_once "includes/headers.php";
require_once "includes/session.php";
require_once "includes/logger.php";
require_once "includes/validator.php";
requireLogin();


validateCSRFToken($_POST['csrf_token']);


$id = (int)$_SESSION['id'];

$username = cleanInput(trim($_POST['username']));
$email = cleanInput(trim($_POST['email']));
$phone = cleanInput(trim($_POST['phone']));
$password = $_POST['password'];
if(!filter_var($email,FILTER_VALIDATE_EMAIL))
{
    die("Invalid Email");
}

if($password != "")
{if(strlen($password) < 8)
{
    die("Password must contain minimum 8 characters");
}
    $hashed_password =
    password_hash(
$password,
PASSWORD_BCRYPT,
['cost'=>12]
);

    $stmt = mysqli_prepare(
$con,
"UPDATE users
 SET USERNAME=?,
 EMAIL=?,
 phone=?,
 PASSWORD=?
 WHERE ID=?"
);


mysqli_stmt_bind_param(
$stmt,
"ssssi",
$username,
$email,
$phone,
$hashed_password,
$id
);


mysqli_stmt_execute($stmt);
}
else
{
   $stmt = mysqli_prepare(
$con,
"UPDATE users
 SET USERNAME=?,
 EMAIL=?,
 phone=?
 WHERE ID=?"
);


mysqli_stmt_bind_param(
$stmt,
"sssi",
$username,
$email,
$phone,
$id
);


mysqli_stmt_execute($stmt);
}
writeLog(
"audit.log",
"User ".$id." updated profile"
);
echo "Profile Updated";

echo "<br><br>";

echo "<a href='my_account.php'>Back To Account</a>";
?>