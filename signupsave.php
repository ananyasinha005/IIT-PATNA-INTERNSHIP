<?php

require_once "includes/csrf.php";
require_once "includes/security.php";
require_once "config/database.php";
require_once "config/config.php";
require_once "includes/mailer.php";
require_once "includes/headers.php";
require_once "includes/session.php";
require_once "includes/logger.php";
require_once "includes/validator.php";

validateCSRFToken($_POST['csrf_token']);


// 1. INPUTS
$uname = cleanInput(trim($_POST['username']));
$email = cleanInput(trim($_POST['email']));
$passwd = $_POST['password'];
$phone = cleanInput(trim($_POST['phone']));
$role = "Customer";


// 2. VALIDATION
if(!validateEmail($email))
{
    die("Invalid email");
}

if(!validatePhone($phone))
{
    die("Invalid phone number");
}

if(!validatePassword($passwd))
{
    die("Password must be at least 8 characters");
}

if(
    !in_array(
        $role,
        [
            "Customer",
            "Seller"
        ]
    )
)
{
    die("Invalid role");
}


// 3. CHECK DUPLICATE EMAIL
$check = mysqli_prepare(
    $con,
    "SELECT id FROM users WHERE EMAIL=?"
);

mysqli_stmt_bind_param($check,"s",$email);
mysqli_stmt_execute($check);
mysqli_stmt_store_result($check);

if(mysqli_stmt_num_rows($check) > 0)
{
    die("Email already registered");
}


// 4. PASSWORD HASH
$hashed_password = password_hash(
    $passwd,
    PASSWORD_BCRYPT,
    ["cost"=>12]
);


// 5. INSERT USER
$stmt = mysqli_prepare(
    $con,
    "INSERT INTO users
    (USERNAME, EMAIL, PASSWORD, phone, role)
    VALUES (?, ?, ?, ?, ?)"
);

mysqli_stmt_bind_param(
    $stmt,
    "sssss",
    $uname,
    $email,
    $hashed_password,
    $phone,
    $role
);

$result = mysqli_stmt_execute($stmt);


// 6. OUTPUT + LOG
if($result)
{

    writeLog(
        "audit.log",
        "New user registered: $email"
    );


    // Send account creation email
    sendEmail(
        $email,
        "Welcome to ShopKart",
        "
        <h2>Welcome to ShopKart</h2>

        <p>Your account has been created successfully.</p>

        <p>Thank you for joining us.</p>
        "
    );


    echo "Successfully registered";
    echo '<a href="home.php">Go to Home</a>';

}
else
{
    echo mysqli_error($con);
}

?>