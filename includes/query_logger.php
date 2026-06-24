<?php

/*
=================================================
DATABASE QUERY LOGGER

Logs:
- Time
- User ID
- Query
- Parameters

Never logs:
- Passwords
- Tokens
- Sensitive information

=================================================
*/


function logQuery($query, $params = [])
{

    $user =
    isset($_SESSION['id'])
    ? $_SESSION['id']
    : "Guest";


    // Remove sensitive data
    $safe_params = [];

    foreach($params as $key=>$value)
    {

        if(
            stripos($key,"password") !== false ||
            stripos($key,"token") !== false
        )
        {
            $safe_params[$key] = "***";
        }
        else
        {
            $safe_params[$key] = $value;
        }

    }


    $log = 
    date("Y-m-d H:i:s")
    ." | User: ".$user
    ." | Query: ".$query
    ." | Params: "
    .json_encode($safe_params)
    .PHP_EOL;


    file_put_contents(
        "logs/query.log",
        $log,
        FILE_APPEND
    );

}

?>