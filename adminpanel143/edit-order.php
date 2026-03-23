<?php
session_start();
require "../includes/db.php";

if (!isset($_SESSION['user']) || $_SESSION['user']['is_admin'] != 1) {
    header("Location: ../login.php");
    exit;
}

if (!isset($_GET['id'])) {
    header("Location: manage-orders.php");
    exit;
}

$order_id = intval($_GET['id']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $status = $_POST['status'];

    $stmt = $mysqli->prepare("UPDATE orders SET status=? WHERE id=?");
    $stmt->bind_param("si", $status, $order_id);
    $stmt->execute();

    header("Location: manage-orders.php");
    exit;
}

// Get order info
$stmt = $mysqli->prepare("SELECT id, status FROM orders WHERE id=?");
$stmt->bind_param("i", $order_id);
$stmt->execute();
$result = $stmt->get_result();
$order = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html>
<head>
<title>Update Order</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">

<div class="bg-white p-8 rounded-2xl shadow-lg w-full max-w-md">

<h2 class="text-2xl font-bold mb-6">Update Order #<?= $order['id'] ?></h2>

<form method="POST" class="space-y-4">

<select name="status" class="w-full border p-3 rounded-xl">
<option value="Pending" <?= $order['status']=="Pending"?'selected':'' ?>>Pending</option>
<option value="Confirmed" <?= $order['status']=="Confirmed"?'selected':'' ?>>Confirmed</option>
<option value="Completed" <?= $order['status']=="Completed"?'selected':'' ?>>Completed</option>
<option value="Cancelled" <?= $order['status']=="Cancelled"?'selected':'' ?>>Cancelled</option>
</select>

<button class="w-full bg-blue-600 text-white py-3 rounded-xl">
Update Status
</button>

</form>

</div>

</body>
</html>
