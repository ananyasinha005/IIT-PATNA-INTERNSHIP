<?php
$server="localhost";
$username="root";
$password="root";
$dbname="shopkart";

/*$con= mysqli_connect($server, $username, $password, $dbname );
if(!$con){
    echo "error has occured";
}*/
$con = mysqli_connect("127.0.0.1", "root", "root", "shopkart" ,3307);


$uname = $_POST['username'];
$email=$_POST['email'];
$passwd=$_POST['pass'];
$phone=$_POST['phone'];

$sql= "INSERT INTO `users`(`USERNAME`, `EMAIL`, `PASSWORD`,  `phone`) VALUES ('$uname', '$email',
'$passwd', '$phone')";
$result=mysqli_query($con, $sql);
if($result){
    echo "suceesfully registered";
    echo '<a href="home.php">Go to Home </a>';
}
else{
    echo "sign up failed!";
}

?>