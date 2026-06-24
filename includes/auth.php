<?php

require_once __DIR__ . "/../config/config.php";

function requireLogin()
{
    if(!isset($_SESSION['id']))
    {
        header("Location: home.php");
        exit();
    }
}

function requireRole($role)
{
    if(!isset($_SESSION['role']))
    {
        header("Location: home.php");
        exit();
    }

    if($_SESSION['role'] != $role)
    {
        header("Location: shopping.php");
        exit();
    }
}


?>