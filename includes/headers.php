<?php

/*
=================================================
SECURE HTTP HEADERS
=================================================

This file adds security headers to every page.

Protection:
- XSS attacks
- Clickjacking
- MIME sniffing
- Data leakage
- Unauthorized browser features

Include this file at the top of every PHP page.
=================================================
*/


// Prevent clickjacking
header(
    "X-Frame-Options: DENY"
);


// Prevent MIME type sniffing
header(
    "X-Content-Type-Options: nosniff"
);


// Control information sent in Referer header
header(
    "Referrer-Policy: strict-origin-when-cross-origin"
);


// Browser feature restrictions
header(
    "Permissions-Policy: geolocation=(), microphone=(), camera=()"
);


// Content Security Policy
header("Content-Security-Policy: default-src 'self'; img-src 'self' data:; style-src 'self' 'unsafe-inline'; script-src 'self'; object-src 'none'; frame-ancestors 'none';");


// HTTP Strict Transport Security
// Enable only when using HTTPS
if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on')
{
    header(
        "Strict-Transport-Security: max-age=31536000; includeSubDomains"
    );
}


// Disable browser caching for sensitive pages
header(
    "Cache-Control: no-store, no-cache, must-revalidate, max-age=0"
);

header(
    "Pragma: no-cache"
);

?>