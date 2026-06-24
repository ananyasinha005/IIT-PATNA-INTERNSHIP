
<?php





require_once "includes/error_handler.php";
require_once "includes/security.php";
require_once "includes/auth.php";
require_once "includes/headers.php";
require_once "includes/session.php";

require_once "includes/access_logger.php";


require_once "config/database.php";
require_once "config/config.php";

checkSessionTimeout();

requireLogin();

requireRole("Admin");
writeLog(
    "audit.log",
    "Admin ".$_SESSION['id']." opened dashboard"
);
// ================================
// ADMIN DASHBOARD STATISTICS
// These queries show overall system data
// ================================


// Total Users
$stmt = $con->prepare(
    "SELECT COUNT(*) AS total FROM users"
);

$stmt->execute();

$total_users = $stmt
->get_result()
->fetch_assoc()['total'];


// Total Sellers
$stmt = $con->prepare(
    "SELECT COUNT(*) AS total 
     FROM users 
     WHERE role='Seller'"
);

$stmt->execute();

$total_sellers = $stmt
->get_result()
->fetch_assoc()['total'];


// Total Products
$stmt = $con->prepare(
    "SELECT COUNT(*) AS total FROM products"
);

$stmt->execute();

$total_products = $stmt
->get_result()
->fetch_assoc()['total'];


// Total Orders
$stmt = $con->prepare(
    "SELECT COUNT(*) AS total FROM orders"
);

$stmt->execute();

$total_orders = $stmt
->get_result()
->fetch_assoc()['total'];


// Total Revenue
$stmt = $con->prepare(
    "SELECT SUM(total_amount) AS revenue
     FROM orders
     WHERE status!='Cancelled'"
);

$stmt->execute();

$revenue = $stmt
->get_result()
->fetch_assoc()['revenue'] ?? 0;


// Pending Orders
$stmt = $con->prepare(
    "SELECT COUNT(*) AS total
     FROM orders
     WHERE status='Pending'"
);

$stmt->execute();

$pending_orders = $stmt
->get_result()
->fetch_assoc()['total'];
// ======================================
// RECENT ORDERS
// Shows latest 5 orders for admin
// ======================================

$stmt = $con->prepare(
"SELECT 
orders.id,
orders.total_amount,
orders.status,
orders.created_at,
users.USERNAME

FROM orders

JOIN users
ON orders.user_id = users.ID

ORDER BY orders.created_at DESC

LIMIT 5
"
);


$stmt->execute();


$recent_orders = $stmt->get_result();
// ======================================
// RECENT REGISTERED USERS
// Shows latest 5 users
// ======================================

$stmt = $con->prepare(
"SELECT 
USERNAME,
EMAIL,
role,
created_at

FROM users

ORDER BY ID DESC

LIMIT 5
"
);


$stmt->execute();


$recent_users = $stmt->get_result();
?>

<?php





require_once "includes/error_handler.php";
require_once "includes/security.php";
require_once "includes/auth.php";
require_once "includes/headers.php";
require_once "includes/session.php";

require_once "includes/access_logger.php";


require_once "config/database.php";
require_once "config/config.php";

checkSessionTimeout();

requireLogin();

requireRole("Admin");
writeLog(
    "audit.log",
    "Admin ".$_SESSION['id']." opened dashboard"
);
// ================================
// ADMIN DASHBOARD STATISTICS
// These queries show overall system data
// ================================


// Total Users
$stmt = $con->prepare(
    "SELECT COUNT(*) AS total FROM users"
);

$stmt->execute();

$total_users = $stmt
->get_result()
->fetch_assoc()['total'];


// Total Sellers
$stmt = $con->prepare(
    "SELECT COUNT(*) AS total 
     FROM users 
     WHERE role='Seller'"
);

$stmt->execute();

$total_sellers = $stmt
->get_result()
->fetch_assoc()['total'];


// Total Products
$stmt = $con->prepare(
    "SELECT COUNT(*) AS total FROM products"
);

$stmt->execute();

$total_products = $stmt
->get_result()
->fetch_assoc()['total'];


// Total Orders
$stmt = $con->prepare(
    "SELECT COUNT(*) AS total FROM orders"
);

$stmt->execute();

$total_orders = $stmt
->get_result()
->fetch_assoc()['total'];


// Total Revenue
$stmt = $con->prepare(
    "SELECT SUM(total_amount) AS revenue
     FROM orders
     WHERE status!='Cancelled'"
);

$stmt->execute();

$revenue = $stmt
->get_result()
->fetch_assoc()['revenue'] ?? 0;


// Pending Orders
$stmt = $con->prepare(
    "SELECT COUNT(*) AS total
     FROM orders
     WHERE status='Pending'"
);

$stmt->execute();

$pending_orders = $stmt
->get_result()
->fetch_assoc()['total'];
// ======================================
// RECENT ORDERS
// Shows latest 5 orders for admin
// ======================================

$stmt = $con->prepare(
"SELECT 
orders.id,
orders.total_amount,
orders.status,
orders.created_at,
users.USERNAME

FROM orders

JOIN users
ON orders.user_id = users.ID

ORDER BY orders.created_at DESC

LIMIT 5
"
);


$stmt->execute();


$recent_orders = $stmt->get_result();
// ======================================
// RECENT REGISTERED USERS
// Shows latest 5 users
// ======================================

$stmt = $con->prepare(
"SELECT 
USERNAME,
EMAIL,
role,
created_at

FROM users

ORDER BY ID DESC

LIMIT 5
"
);


$stmt->execute();


$recent_users = $stmt->get_result();
?>
<!DOCTYPE html>
<html>

<head>

<title>ShopKart Admin Dashboard</title>

<link rel="stylesheet" href="style.css">


<style>


body{

background:#f1f5f9;

}


/* Admin Header */

.admin-header{

background:#111827;

color:white;

padding:25px 40px;

display:flex;

justify-content:space-between;

align-items:center;

border-radius:0 0 20px 20px;

}


.admin-header h1{

color:white;

font-size:32px;

}



.admin-header a{

color:white;

margin-left:20px;

}



/* Dashboard */

.dashboard{

width:90%;

max-width:1200px;

margin:30px auto;

}



/* Cards */


.stats{

display:grid;

grid-template-columns:
repeat(auto-fit,minmax(250px,1fr));

gap:25px;

}



.stat-card{

background:white;

padding:25px;

border-radius:20px;

box-shadow:0 8px 25px rgba(0,0,0,.08);

transition:.3s;

}



.stat-card:hover{

transform:translateY(-8px);

}



.stat-card h3{

color:#64748b;

font-size:18px;

}



.stat-card h2{

font-size:35px;

color:#2563eb;

}




/* Management */


.management{

margin-top:40px;

background:white;

padding:25px;

border-radius:20px;

}



.management a{

display:inline-block;

background:#111827;

color:white;

padding:12px 20px;

border-radius:10px;

margin:10px;

}



.management a:hover{

background:#2563eb;

text-decoration:none;

}



/* Tables */

.section{

margin-top:40px;

background:white;

padding:25px;

border-radius:20px;

box-shadow:0 5px 20px rgba(0,0,0,.08);

}



</style>


</head>


<body>


<div class="admin-header">


<h1>
🛒 ShopKart Admin
</h1>


<div>

<a href="my_account.php">
👤 Account
</a>


<a href="logout.php">
Logout
</a>


</div>


</div>





<div class="dashboard">



<h1>
Dashboard Overview
</h1>


<br>



<div class="stats">


<div class="stat-card">

<h3>
👥 Total Users
</h3>

<h2>
<?= $total_users ?>
</h2>

</div>



<div class="stat-card">

<h3>
🏪 Sellers
</h3>

<h2>
<?= $total_sellers ?>
</h2>

</div>




<div class="stat-card">

<h3>
📦 Products
</h3>

<h2>
<?= $total_products ?>
</h2>

</div>




<div class="stat-card">

<h3>
🛒 Orders
</h3>

<h2>
<?= $total_orders ?>
</h2>

</div>




<div class="stat-card">

<h3>
💰 Revenue
</h3>

<h2>
₹<?= $revenue ?>
</h2>

</div>




<div class="stat-card">

<h3>
⏳ Pending Orders
</h3>

<h2>
<?= $pending_orders ?>
</h2>

</div>


</div>






<div class="management">


<h2>
Management
</h2>


<a href="admin_users.php">
👥 Manage Users
</a>


<a href="admin_products.php">
📦 Manage Products
</a>


<a href="admin_orders.php">
🛒 Manage Orders
</a>


<a href="admin_logs.php">
📜 Logs
</a>


<a href="admin_reports.php">
📊 Reports
</a>


</div>






<div class="section">


<h2>
Recent Orders
</h2>


<table>


<tr>

<th>ID</th>

<th>Customer</th>

<th>Amount</th>

<th>Status</th>

<th>Date</th>

</tr>



<?php while($order=mysqli_fetch_assoc($recent_orders)){ ?>


<tr>


<td>
<?=htmlspecialchars($order['id']);?>
</td>


<td>
<?=htmlspecialchars($order['USERNAME']);?>
</td>


<td>
₹<?=htmlspecialchars($order['total_amount']);?>
</td>


<td>
<?=htmlspecialchars($order['status']);?>
</td>


<td>
<?=htmlspecialchars($order['created_at']);?>
</td>


</tr>


<?php } ?>


</table>


</div>






<div class="section">


<h2>
Recent Users
</h2>



<table>


<tr>

<th>
Username
</th>

<th>
Email
</th>

<th>
Role
</th>

<th>
Created
</th>


</tr>



<?php while($user=mysqli_fetch_assoc($recent_users)){ ?>


<tr>


<td>
<?=htmlspecialchars($user['USERNAME']);?>
</td>


<td>
<?=htmlspecialchars($user['EMAIL']);?>
</td>


<td>
<?=htmlspecialchars($user['role']);?>
</td>


<td>
<?=htmlspecialchars($user['created_at']);?>
</td>


</tr>


<?php } ?>


</table>


</div>



</div>


</body>

</html>