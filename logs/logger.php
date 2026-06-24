<?php

function writeLog($file, $message)
{
    $folder = "logs/";

    if(!is_dir($folder))
    {
        mkdir($folder,0777,true);
    }


    $time = date("Y-m-d H:i:s");

    $user = isset($_SESSION['id'])
            ? $_SESSION['id']
            : "Guest";


    $ip = $_SERVER['REMOTE_ADDR'] ?? "Unknown";


    $log = 
    "[".$time."] ".
    "USER: ".$user." ".
    "IP: ".$ip." ".
    $message.
    PHP_EOL;


    file_put_contents(
        $folder.$file,
        $log,
        FILE_APPEND
    );
}



// Database query logger

function logQuery($query)
{

    $clean_query = preg_replace(
        '/\s+/',
        ' ',
        trim($query)
    );


    writeLog(
        "query.log",
        "QUERY: ".$clean_query
    );

}

?>