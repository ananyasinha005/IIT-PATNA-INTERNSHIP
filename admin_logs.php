<?php

require_once "includes/error_handler.php";
require_once "includes/security.php";
require_once "includes/auth.php";
require_once "includes/headers.php";
require_once "includes/session.php";
require_once "includes/logger.php";
require_once "includes/db_logger.php"; 
require_once "config/database.php";
require_once "config/config.php";
require_once "includes/csrf.php";
require_once "includes/access_logger.php";

checkSessionTimeout();

requireLogin();

requireRole("Admin");
require_once "includes/logger.php";


$log_type = $_GET['log'] ?? "audit.log";


writeLog(
    "audit.log",
    "Admin ".$_SESSION['id']." viewed ".$log_type
);


$allowed_logs = [
    "audit.log",
    "security.log",
    "query.log",
    "error.log",
    "access.log"
];


if(!in_array($log_type,$allowed_logs))
{
    die("Invalid log file");
}



$path = "logs/".$log_type;


if(file_exists($path))
{
    $logs = file_get_contents(
    $path,
    false,
    null,
    max(0, filesize($path)-10000)
);
}
else
{
    $logs = "No logs available";
}


?>


<!DOCTYPE html>
<html>

<head>

<title>Admin Log Viewer</title>

<style>

body{
    font-family:Arial;
    padding:20px;
    background:#f5f5f5;
}


.box{

background:white;
padding:20px;
border-radius:10px;

}


pre{

white-space:pre-wrap;
background:#111;
color:#00ff00;
padding:20px;

}

a{

margin-right:15px;

}

</style>

</head>


<body>


<h1>Admin Log Viewer</h1>


<a href="admin_dashboard.php">
⬅ Dashboard
</a>


<br><br>


<a href="admin_logs.php?log=audit.log">
Audit Logs
</a>


<a href="admin_logs.php?log=security.log">
Security Logs
</a>

<a href="admin_logs.php?log=error.log">
Error Logs
</a>


<a href="admin_logs.php?log=access.log">
Access Logs
</a>
<a href="admin_logs.php?log=query.log">
Query Logs
</a>


<br><br>


<div class="box">


<h2>
<?php echo htmlspecialchars($log_type); ?>
</h2>


<pre>

<?php

echo htmlspecialchars($logs);

?>

</pre>


</div>



</body>

</html>