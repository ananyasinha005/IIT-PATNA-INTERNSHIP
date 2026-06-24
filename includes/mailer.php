<?php

function sendEmail($to,$subject,$message)
{

    // Development mode:
    // Email is not actually sent.
    // Only logged.

    if(function_exists("writeLog"))
    {
        writeLog(
            "security.log",
            "Email simulated. To: ".$to." Subject: ".$subject
        );
    }

    return true;

}

?>