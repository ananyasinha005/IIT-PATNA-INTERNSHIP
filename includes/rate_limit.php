<?php


function isAccountLocked($user)
{

    if($user['locked_until'] != NULL)
    {

        if(strtotime($user['locked_until']) > time())
        {
            return true;
        }

    }

    return false;

}




function increaseFailedAttempts($con,$id)
{

    // Increase failed login count

    mysqli_query(
        $con,
        "UPDATE users
         SET failed_attempts = failed_attempts + 1
         WHERE ID='$id'"
    );


    // Check attempts

    $result = mysqli_query(
        $con,
        "SELECT failed_attempts
         FROM users
         WHERE ID='$id'"
    );


    $row = mysqli_fetch_assoc($result);



    // Lock account after 5 failed attempts

    if($row['failed_attempts'] >= 5)
    {

        mysqli_query(
            $con,
            "UPDATE users
             SET locked_until = DATE_ADD(NOW(), INTERVAL 15 MINUTE)
             WHERE ID='$id'"
        );

    }

}





function resetFailedAttempts($con,$id)
{

    mysqli_query(
        $con,
        "UPDATE users
         SET failed_attempts=0,
         locked_until=NULL
         WHERE ID='$id'"
    );

}
function checkLoginRateLimit($ip)
{
    /*
    Basic login rate limiter placeholder.

    Later we can improve this using:
    - database tracking
    - IP based attempts
    - progressive delays
    */

    return true;
}

?>