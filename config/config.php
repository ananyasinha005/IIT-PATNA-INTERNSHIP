<?php


error_reporting(E_ALL);
ini_set('display_errors',1);



/*
    WEBSITE CONFIGURATION FILE

    Stores global settings
*/


define(
    "SITE_NAME",
    "ShopKart"
);



define(
    "SESSION_TIMEOUT",
    1200
);
// 20 minutes



define(
    "MAX_LOGIN_ATTEMPTS",
    5
);



define(
    "MAX_UPLOAD_SIZE",
    2*1024*1024
);
// 2 MB


?>