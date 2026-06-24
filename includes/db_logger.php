<?php

require_once "logger.php";

/*
|--------------------------------------------------------------------------
| Query Logging Wrapper
|--------------------------------------------------------------------------
| Logs every SQL query before execution.
| Never log passwords, tokens, or sensitive values.
*/

function logQuery($sql, $params = [])
{
    $user_id = $_SESSION['id'] ?? "Guest";

    $safe_params = [];

    foreach($params as $param)
    {
        $safe_params[] = substr(
            htmlspecialchars((string)$param),
            0,
            100
        );
    }

    writeLog(
        "query.log",
        "User: ".$user_id.
        " | SQL: ".$sql.
        " | Params: ".json_encode($safe_params)
    );
}