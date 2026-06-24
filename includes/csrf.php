<?php

function generateCSRFToken()
{
    if(!isset($_SESSION['csrf_token']))
    {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}


function validateCSRFToken($token)
{
    if(
        !isset($_SESSION['csrf_token']) ||
        empty($token) ||
        !hash_equals($_SESSION['csrf_token'], $token)
    )
    {
        die("Invalid request");
    }


    // Token rotation after successful validation
    unset($_SESSION['csrf_token']);
}

?>