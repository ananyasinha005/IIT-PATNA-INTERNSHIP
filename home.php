<?php




require_once "includes/logger.php";
//require_once "includes/error_handler.php";
require_once "includes/session.php";
require_once "includes/csrf.php";
require_once "includes/security.php";
require_once "config/config.php";
require_once "includes/headers.php";

require_once "includes/access_logger.php";

checkSessionTimeout();
if(isset($_SESSION['id']))
{
    writeLog(
        "audit.log",
        "User ".$_SESSION['id']." opened login page"
    );
}
else
{
    writeLog(
        "access.log",
        "Anonymous visitor opened login page. IP=".$_SERVER['REMOTE_ADDR']
    );
}
 if(isset($_SESSION['role']))
{

    if($_SESSION['role']=="Admin")
    {
        header("Location: admin_dashboard.php");
        exit();
    }
    elseif($_SESSION['role']=="Seller")
    {
        header("Location: seller_dashboard.php");
        exit();
    }
    else
    {
        header("Location: shopping.php");
        exit();
    }

}






?>
<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="style.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ShopKart Login</title>
</head>
<body>
    <div class=" im">
    <img src="a1.jpg" >
    </div> 
    <div class="cont">
        <h3>SHOPKART</h3>
        
    </div>
    <?php
echo "<pre>";
print_r($_SESSION);
echo "</pre>";
?>
   <form class="login-form" action="homeloginchec.php" method="POST">
    <input type="hidden"
       name="csrf_token"
       value="<?php echo generateCSRFToken(); ?>">
         <div class="form-group">
            <label for="email">Email id</label>
            <input type="email"
id="email"
name="email"
maxlength="100"
autocomplete="email"
placeholder="Enter Email ID"
required>
        </div>
        
        <!-- Username/Email Field -->
        <div class="form-group">
            <label for="username">Username</label>
            <input 
type="text" 
id="username" 
name="username" 
autocomplete="username"
placeholder="Enter username" 
required>
        </div>

        <!-- Password Field -->
        <div class="form-group">
            <label for="password">Password</label>
            <input type="password"
       id="password"
       name="password"
       autocomplete="current-password"
       placeholder="Enter password"
       required>
        </div>

        <!-- Submit Button -->
        <button type="submit">Log In</button>
        <p class="signup-link">
    Don't have an account?
    <a href="signup.php">Sign Up</a>
    <p>
<a href="forgot_password.php">
Forgot Password?
</a>
</p>
</p>
        
    
    </form>
    
</body>
</html>
