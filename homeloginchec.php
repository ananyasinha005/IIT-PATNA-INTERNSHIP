<?php //home login save this is
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
//require_once "includes/error_handler.php";
require_once "includes/mailer.php";
require_once "includes/csrf.php";
require_once "includes/headers.php";
require_once "includes/session.php";
require_once "includes/logger.php";
require_once "includes/rate_limit.php";
require_once "includes/validator.php";
require_once "includes/db_logger.php";
require_once "includes/access_logger.php";




require_once "includes/security.php";
require_once "config/database.php";
require_once "config/config.php";
validateCSRFToken($_POST['csrf_token']);



checkLoginRateLimit($_SERVER['REMOTE_ADDR']);
$email = cleanInput(trim($_POST['email']));
$username = cleanInput(trim($_POST['username']));
$password = $_POST['password'];

logQuery(
    "SELECT * FROM users WHERE EMAIL=? OR USERNAME=?",
    [$email, $username]
);

$sql = "SELECT * FROM users WHERE EMAIL=? OR USERNAME=?";


$stmt = $con->prepare($sql);

$stmt->bind_param("ss", $email, $username);

$stmt->execute();
$result = $stmt->get_result();




if($result->num_rows > 0)
{
    $row = $result->fetch_assoc();


    if(isAccountLocked($row))
    {
        die("Account temporarily locked. Try again later.");
    }


    // check password
   if(password_verify($password, $row['PASSWORD'])){
    resetFailedAttempts(
    $con,
    $row['ID']
);


writeLog(
    "audit.log",
    "User ".$row['ID']." successful login"
);
sendEmail(
    $row['EMAIL'],
    "New Login Alert - ShopKart",
    "
    <h2>New Login Detected</h2>

    <p>Hello ".$row['USERNAME'].",</p>

    <p>Your ShopKart account was logged in successfully.</p>

    <p>
    IP Address:
    ".$_SERVER['REMOTE_ADDR']."
    </p>

    <p>
    If this was not you, please reset your password immediately.
    </p>
    "
);
        session_regenerate_id(true);
        $_SESSION['id']=$row['ID'];
        $_SESSION['role']=$row['role'];
        $_SESSION['last_activity'] = time();

        if($row['role']=="Admin")
{
    header("Location: admin_dashboard.php");
exit();
}
elseif($row['role']=="Seller")
{
   header("Location: seller_dashboard.php");
exit();

}
else
{
    header("Location: shopping.php");
exit();
}
}
else
{
    increaseFailedAttempts(
    $con,
    $row['ID']
);

    writeLog(
        "security.log",
        "Failed login attempt for ".$email." IP: ".$_SERVER['REMOTE_ADDR']
    );


    echo "Invalid email, username, or password";

}
}
else
{
    writeLog(
        "security.log",
        "Unknown account login attempt. Email=".$email.
        " IP=".$_SERVER['REMOTE_ADDR']
    );
    echo "Invalid email, username, or password";
}

    


?>