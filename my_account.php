<?php

require_once "includes/error_handler.php";
require_once "includes/logger.php";
require_once "includes/db_logger.php";
require_once "includes/security.php";
require_once "includes/auth.php";
require_once "config/database.php";
require_once "config/config.php";
require_once "includes/headers.php";
require_once "includes/session.php";
require_once "includes/access_logger.php";
checkSessionTimeout();
requireLogin();

writeLog(
    "audit.log",
    "User ".$_SESSION['id']." viewed account page"
);



$id = $_SESSION['id'];
logQuery(
    "SELECT * FROM users WHERE ID=?",
    [$id]
);
$stmt = $con->prepare(
    "SELECT *
     FROM users
     WHERE ID=?"
);

$stmt->bind_param(
    "i",
    $id
);

$stmt->execute();

$user = $stmt->get_result()->fetch_assoc();
if(!$user)
{
    session_destroy();
    header("Location: home.php");
    exit();
}
?>

<!DOCTYPE html>
<html>

<head>

<title>My Account | ShopKart</title>

<style>

body{

    background:#f1f5f9;
    font-family:'Poppins',sans-serif;

}



.header{

    background:#111827;
    color:white;

    padding:25px 40px;

    display:flex;
    justify-content:space-between;
    align-items:center;

    border-radius:0 0 20px 20px;

}


.header h1{

    color:white;

}


.header a{

    color:white;
    text-decoration:none;

}



.container{

    width:90%;
    max-width:700px;

    margin:50px auto;

}



.profile-card{

    background:white;

    padding:35px;

    border-radius:20px;

    box-shadow:0 8px 25px rgba(0,0,0,.08);

}



.profile-card h2{

    margin-bottom:25px;

}



.info{

    background:#f8fafc;

    padding:15px;

    margin:12px 0;

    border-radius:10px;

    font-size:17px;

}



.buttons a{

    display:block;

    margin:15px 0;

    padding:14px;

    text-align:center;

    border-radius:10px;

    text-decoration:none;

    color:white;

    background:#2563eb;

}



.buttons a:hover{

    background:#1d4ed8;

}



.logout{

    background:#dc2626 !important;

}


.logout:hover{

    background:#991b1b !important;

}


.back{

    background:#111827 !important;

}



</style>


</head>


<body>


<div class="header">


<h1>
👤 My Account
</h1>




</div>



<div class="container">


<div class="profile-card">


<h2>
Profile Details
</h2>



<div class="info">

<b>Username:</b>

<?= htmlspecialchars($user['USERNAME']); ?>

</div>



<div class="info">

<b>Email:</b>

<?= htmlspecialchars($user['EMAIL']); ?>

</div>




<div class="info">

<b>Phone:</b>

<?= htmlspecialchars($user['phone']); ?>

</div>




<div class="info">

<b>Role:</b>

<?= htmlspecialchars($user['role']); ?>

</div>




<div class="buttons">


<a href="edit_profile.php">

✏️ Edit Profile

</a>



<a href="my_addresses.php">

📍 Manage Addresses

</a>



<a class="back" href="my_orders.php">

📦 My Orders

</a>



<a class="logout" href="logout.php">

🚪 Logout

</a>



</div>


</div>


</div>


</body>

</html>