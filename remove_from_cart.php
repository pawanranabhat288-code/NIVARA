<?php
session_start();
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: cart.php'); exit;
}

$id = intval($_POST['id'] ?? 0);
if ($id > 0 && isset($_SESSION['cart'][$id])) {
    unset($_SESSION['cart'][$id]);
}
header('Location: cart.php'); exit;
