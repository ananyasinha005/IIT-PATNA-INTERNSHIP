<?php

/*
    DATABASE CONNECTION FILE
*/


$host = "127.0.0.1";
$username = "root";
$password = "root";
$database = "shopkart";
$port = 3307;


$con = mysqli_connect(
    $host,
    $username,
    $password,
    $database,
    $port
);


// Check connection

if(!$con)
{
    die("Database connection failed");
}


// Character encoding protection

mysqli_set_charset($con,"utf8mb4");


?>