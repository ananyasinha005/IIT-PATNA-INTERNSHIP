<?php

require_once "logger.php";


writeLog(
"access.log",
"Opened page ".$_SERVER['PHP_SELF']
);

?>