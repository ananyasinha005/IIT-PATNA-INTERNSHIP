<?php

require_once "includes/db_logger.php";
require_once "includes/csrf.php";
require_once "includes/error_handler.php";
require_once "includes/security.php";
require_once "includes/auth.php";
require_once "includes/headers.php";
require_once "includes/session.php";
require_once "includes/logger.php";
require_once "includes/access_logger.php";


require_once "config/database.php";
require_once "config/config.php";

checkSessionTimeout();

requireLogin();

requireRole("Admin");
writeLog(
    "audit.log",
    "Admin ".$_SESSION['id']." viewed user management"
);
logQuery(
    "SELECT * FROM users",
    []
);

$stmt = $con->prepare(
    "SELECT ID, USERNAME, EMAIL, role
     FROM users"
);

$stmt->execute();

$result = $stmt->get_result();
if(mysqli_num_rows($result) == 0)
{
    echo "No users found";
}

?>

<!DOCTYPE html>
<html>

<head>

<title>Manage Users | ShopKart</title>

<style>

body{
    background:#f1f5f9;
    font-family:'Poppins',sans-serif;
}


.header{

    background:#111827;
    color:white;
    padding:25px 40px;

    display:flex;
    justify-content:space-between;
    align-items:center;

    border-radius:0 0 20px 20px;

}


.header h1{
    color:white;
}


.header a{

    color:white;
    text-decoration:none;

}


.container{

    width:95%;
    max-width:1300px;
    margin:40px auto;

}


.card{

    background:white;
    padding:25px;

    border-radius:20px;

    box-shadow:0 8px 25px rgba(0,0,0,.08);

    overflow-x:auto;

}



table{

    width:100%;
    border-collapse:collapse;

}



th{

    background:#111827;
    color:white;
    padding:15px;

}



td{

    padding:15px;
    text-align:center;

    border-bottom:1px solid #eee;

}



tr:hover{

    background:#f8fafc;

}



select{

    padding:10px;

    border-radius:8px;

    border:1px solid #ddd;

}



button{

    border:none;

    padding:10px 18px;

    border-radius:8px;

    cursor:pointer;

    color:white;

}



.update{

    background:#2563eb;

}


.update:hover{

    background:#1d4ed8;

}



.delete{

    background:#dc2626;

}


.delete:hover{

    background:#991b1b;

}



.back{

    display:inline-block;

    margin-top:25px;

    background:#111827;

    color:white;

    padding:12px 20px;

    border-radius:10px;

    text-decoration:none;

}


</style>


</head>


<body>


<div class="header">

<h1>
👥 Manage Users
</h1>


<a href="admin_dashboard.php">
⬅ Dashboard
</a>


</div>



<div class="container">


<div class="card">



<table>


<tr>

<th>ID</th>

<th>Username</th>

<th>Email</th>

<th>Role</th>

<th>Delete</th>

</tr>



<?php

while($row=mysqli_fetch_assoc($result))

{

?>


<tr>


<td>
<?= htmlspecialchars($row['ID'],ENT_QUOTES,'UTF-8'); ?>
</td>



<td>
<?= htmlspecialchars($row['USERNAME'],ENT_QUOTES,'UTF-8'); ?>
</td>



<td>
<?= htmlspecialchars($row['EMAIL'],ENT_QUOTES,'UTF-8'); ?>
</td>




<td>


<form method="POST" action="update_role.php">


<input type="hidden"
name="user_id"
value="<?= $row['ID']; ?>">



<input type="hidden"
name="csrf_token"
value="<?= generateCSRFToken(); ?>">



<select name="role">


<option value="Customer"
<?= $row['role']=="Customer"?"selected":"" ?>>
Customer
</option>


<option value="Seller"
<?= $row['role']=="Seller"?"selected":"" ?>>
Seller
</option>


<option value="Admin"
<?= $row['role']=="Admin"?"selected":"" ?>>
Admin
</option>


</select>


<br><br>


<button class="update" type="submit">

Update Role

</button>


</form>



</td>





<td>


<form method="POST" action="delete_user.php">


<input type="hidden"
name="user_id"
value="<?= (int)$row['ID']; ?>">



<input type="hidden"
name="csrf_token"
value="<?= generateCSRFToken(); ?>">



<button class="delete"
type="submit"
onclick="return confirm('Delete this user?');">

🗑 Delete

</button>



</form>


</td>



</tr>



<?php

}

?>


</table>



</div>



<a class="back" href="admin_dashboard.php">

⬅ Back To Dashboard

</a>


</div>


</body>

</html>