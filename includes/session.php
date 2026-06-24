<?php

/*
=================================================
SECURE SESSION MANAGEMENT
=================================================

Features:
- Secure session cookie settings
- HttpOnly protection
- SameSite protection
- Session timeout
- Session regeneration
- Prevent session hijacking

=================================================
*/


// Start session only if not already started

if(session_status() === PHP_SESSION_NONE)
{


    // Secure cookie configuration

    ini_set(
        "session.cookie_httponly",
        1
    );


    // Enable only on HTTPS
    // Change to 1 when deploying on HTTPS

    ini_set(
        "session.cookie_secure",
        0
    );


    // CSRF protection through SameSite

    ini_set(
        "session.cookie_samesite",
        "Strict"
    );


    // Prevent session fixation

    ini_set(
        "session.use_strict_mode",
        1
    ); 
    session_start(); 

}



// Session timeout duration

$timeout = 1800; 
// 30 minutes



function checkSessionTimeout()
{

    global $timeout;


    if(isset($_SESSION['last_activity']))
    {

        $inactive =
        time() - $_SESSION['last_activity'];


        if($inactive > $timeout)
        {

            session_unset();

            session_destroy();


            header(
                "Location: home.php?timeout=1"
            );

            exit();

        }

    }


    // Update activity time

    $_SESSION['last_activity'] = time();

}



// Regenerate session ID after login

function regenerateSession()
{

    session_regenerate_id(true);

}





?>