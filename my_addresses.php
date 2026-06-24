<?php

require_once "includes/error_handler.php";
require_once "includes/security.php";
require_once "includes/auth.php";
require_once "includes/session.php";
require_once "includes/csrf.php";
require_once "includes/logger.php";

require_once "config/database.php";
require_once "config/config.php";

checkSessionTimeout();
requireLogin();

$user_id = (int)$_SESSION['id'];

$stmt = $con->prepare(
"
SELECT *
FROM addresses
WHERE user_id=?
ORDER BY id DESC
"
);

$stmt->bind_param("i",$user_id);
$stmt->execute();

$result = $stmt->get_result();

?>

<h1>My Addresses</h1>

<form method="POST" action="save_address.php">

<input type="hidden"
name="csrf_token"
value="<?php echo generateCSRFToken(); ?>">

<input type="text"
name="full_name"
placeholder="Full Name"
required>

<br><br>

<input type="text"
name="phone"
placeholder="Phone"
required>

<br><br>

<textarea
name="address_line"
placeholder="Address"
required></textarea>

<br><br>

<input type="text"
name="city"
placeholder="City"
required>

<br><br>

<input type="text"
name="state"
placeholder="State"
required>

<br><br>

<input type="text"
name="pincode"
placeholder="Pincode"
required>

<br><br>

<button type="submit">
Add Address
</button>

</form>

<hr>

<h2>Saved Addresses</h2>

<?php

while($row=mysqli_fetch_assoc($result))
{
?>

<div style="border:1px solid #ccc;padding:10px;margin:10px;">

<b>
<?= htmlspecialchars($row['full_name']); ?>
</b>

<br>

<?= htmlspecialchars($row['phone']); ?>

<br>

<?= htmlspecialchars($row['address_line']); ?>

<br>

<?= htmlspecialchars($row['city']); ?>

<br>

<?= htmlspecialchars($row['state']); ?>

<br>

<?= htmlspecialchars($row['pincode']); ?>

</div>

<?php
}
?>