<?php

/*
    SECURITY HEADER CONFIGURATION

    

*/




header("X-Frame-Options: DENY");



// Protection: MIME sniffing attacks

header("X-Content-Type-Options: nosniff");


// Controls information sent when leaving the website

header("Referrer-Policy: no-referrer");


// Controls which scripts/styles are allowed

header(
    "Content-Security-Policy: default-src 'self'; style-src 'self' https://cdn.jsdelivr.net; script-src 'self' https://cdn.jsdelivr.net;"
);

?>