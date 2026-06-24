<?php 
require_once "includes/csrf.php";
require_once "includes/headers.php";
require_once "includes/session.php";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<link rel="stylesheet" href="style.css">
<body>
    <div class=" im">
    <img src="a1.jpg" >
    </div> 
    <div class="cont">
        <h3>SHOPKART</h3>
        
    </div>
    <div class="fm">
    <form  class="login-form" action="signupsave.php" method= POST>
    <input type="hidden"
       name="csrf_token"
       value="<?= generateCSRFToken(); ?>">
        <label for="email">Email</label>
        <input 
type="email"
id="email"
name="email"
maxlength="100"
placeholder="Enter email"
required>
        
        
        <label for="username">Create Username</label>
        <input 
type="text" 
id="username" 
name="username"
maxlength="50"
placeholder="Enter username"
required>
        
        
        <label for="password">Password</label>
        <input 
type="password" 
id="password" 
name="password" 
placeholder="Enter password"
minlength="8"
required>

        <label for="phone">Phone no.</label>
        <input type="tel" 
id="phone" 
name="phone" 
pattern="[0-9]{10}"
maxlength="10"
placeholder="Enter phone no."
required> 

        <select name="role" required>
    <option value="Customer">Customer</option>
    
</select>





        
        <button type="submit" name="sign">Sign In</button>

</form>
</div>


    
</body>
</html>
