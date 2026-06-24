<?php

require_once "config/database.php";


$user_id=$_GET['id'];
$token=$_GET['token'];

?>


<form method="POST" action="update_password.php">


<input type="hidden"
name="user_id"
value="<?php echo $user_id; ?>">


<input type="hidden"
name="token"
value="<?php echo $token; ?>">



<input 
type="password"
name="password"
placeholder="New password"
required>


<button>
Change Password
</button>


</form>