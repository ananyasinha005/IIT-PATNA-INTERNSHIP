<?php


function cleanInput($data)
{
    return htmlspecialchars(
        trim($data),
        ENT_QUOTES,
        'UTF-8'
    );
}



function validateEmail($email)
{
    return filter_var(
        $email,
        FILTER_VALIDATE_EMAIL
    );
}



function validatePhone($phone)
{
    return preg_match(
        "/^[0-9]{10}$/",
        $phone
    );
}



function validatePassword($password)
{
    return strlen($password) >= 8;
}



function validateNumber($number)
{
    return is_numeric($number);
}


?>