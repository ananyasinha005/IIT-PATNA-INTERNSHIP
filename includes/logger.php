<?php

function writeLog($file,$message)
{

$date = date("Y-m-d H:i:s");

$data = "[".$date."] ";

if(isset($_SESSION['id']))
{
    $data .= "USER: ".$_SESSION['id']." ";
}

$data .= $message."\n";


file_put_contents(
"logs/".$file,
$data,
FILE_APPEND
);

}

?>