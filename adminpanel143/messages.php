<?php
session_start();
require_once "../includes/db.php";

// Check admin
if (!isset($_SESSION['user']) || $_SESSION['user']['is_admin'] != 1) {
    header("Location: ../login.php");
    exit;
}

$result = $mysqli->query("SELECT * FROM contact_messages ORDER BY id DESC");
?>

<h2>Contact Messages</h2>

<table border="1" cellpadding="10">

<tr>
<th>Name</th>
<th>Email</th>
<th>Message</th>
<th>Date</th>
</tr>

<?php while ($row = $result->fetch_assoc()) { ?>

<tr>

<td><?php echo htmlspecialchars($row['name']); ?></td>

<td><?php echo htmlspecialchars($row['email']); ?></td>

<td><?php echo htmlspecialchars($row['message']); ?></td>

<td><?php echo $row['created_at']; ?></td>

</tr>

<?php } ?>

</table>