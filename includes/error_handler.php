<?php


require_once "includes/logger.php";


// Prevent displaying sensitive errors

ini_set(
    "display_errors",
    0
);


// Log errors

ini_set(
    "log_errors",
    1
);



set_error_handler(
function(
    $severity,
    $message,
    $file,
    $line
){

    writeLog(
        "error.log",
        "
        ERROR:
        ".$message."
        FILE:
        ".$file."
        LINE:
        ".$line
        );



    echo "
    <h3>
    Something went wrong.
    Please try again later.
    </h3>
    ";

    exit();

});




// Handle fatal errors

register_shutdown_function(
function(){

    $error = error_get_last();


    if($error)
    {

        writeLog(
            "error.log",
            "
            FATAL ERROR:
            ".$error['message']."
            FILE:
            ".$error['file']."
            LINE:
            ".$error['line']
        );


        echo "
        <h3>
        Something went wrong.
        Please try again later.
        </h3>
        ";

    }

});


?>