<?php
require_once "includes/error_handler.php";
require_once "includes/security.php";
require_once "includes/headers.php";
require_once "includes/session.php";
require_once "includes/logger.php";

writeLog(
    "audit.log",
    "User ".($_SESSION['id'] ?? 'Unknown')." logged out"
);
/* Remove all session variables */
$_SESSION = [];

/* Delete session cookie */
writeLog(
    "audit.log",
    "User ".($_SESSION['id'] ?? 'Unknown')." logged out"
);

$_SESSION = [];

if (ini_get("session.use_cookies"))
{
    $params = session_get_cookie_params();

    setcookie(
        session_name(),
        '',
        [
            'expires' => time() - 42000,
            'path' => $params["path"],
            'domain' => $params["domain"],
            'secure' => $params["secure"],
            'httponly' => $params["httponly"],
            'samesite' => 'Strict'
        ]
    );
}

session_destroy();

header("Location: home.php");
exit();

/* Destroy session */
session_destroy();


header("Location: home.php");
exit();

?>