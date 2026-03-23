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

// First delete order items
$stmt1 = $mysqli->prepare("DELETE FROM order_items WHERE order_id=?");
$stmt1->bind_param("i", $order_id);
$stmt1->execute();

// Then delete order
$stmt2 = $mysqli->prepare("DELETE FROM orders WHERE id=?");
$stmt2->bind_param("i", $order_id);
$stmt2->execute();

header("Location: manage-orders.php");
exit;
?>
