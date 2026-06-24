<?php
session_start();
require_once "includes/session.php";
require_once "includes/error_handler.php";
require_once "includes/security.php";
require_once "includes/auth.php";
require_once "includes/csrf.php";
require_once "includes/logger.php";
require_once "config/database.php";
require_once "config/config.php";
require_once "includes/db_logger.php";
require_once "includes/access_logger.php";
checkSessionTimeout();
requireLogin();

writeLog(
    "audit.log",
    "User ".$_SESSION['id']." viewed profile edit page"
);



$id = (int)$_SESSION['id'];

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



?>

<!DOCTYPE html>
<html>
<head>
<title>Edit Profile</title>
</head>
<body>

<h1>Edit Profile</h1>

<form action="update_profile.php" method="POST">
    <input type="hidden"
       name="csrf_token"
       value="<?php echo generateCSRFToken(); ?>">

Username:<br>
<input type="text"
name="username"
value="<?php echo htmlspecialchars($user['USERNAME']); ?>">
<br><br>

Email:<br>
<input type="text"
name="email"
value="<?php echo htmlspecialchars($user['EMAIL']); ?>">
<br><br>

Phone:<br>
<input type="text"
name="phone"
value="<?php echo htmlspecialchars($user['phone']); ?>">
<br><br>

New Password:<br>
<input type="password"
name="password">
<br><br>

<button type="submit">
Update Profile
</button>

</form>

</body>
</html>