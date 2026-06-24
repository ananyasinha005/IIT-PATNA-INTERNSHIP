<?php


require_once "includes/error_handler.php";
require_once "includes/security.php";
require_once "includes/auth.php";
require_once "includes/headers.php";
require_once "includes/session.php";
require_once "includes/access_logger.php";
require_once "includes/logger.php";
require_once "includes/db_logger.php";
require_once "includes/csrf.php";

require_once "config/database.php";
require_once "config/config.php";

checkSessionTimeout();

requireLogin();

requireRole("Admin");

?>

<h1>📊 Admin Reports</h1>

<div>

<a href="export_users.php">
👥 Export Users CSV
</a>

<br><br>

<a href="export_products.php">
📦 Export Products CSV
</a>

<br><br>

<a href="export_orders.php">
🛒 Export Orders CSV
</a>

<br><br>

<a href="export_inventory.php">
📋 Export Inventory CSV
</a>

<br><br>

<a href="admin_dashboard.php">
⬅ Back Dashboard
</a>

</div>