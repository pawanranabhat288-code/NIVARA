<?php
session_start();
require __DIR__ . '/includes/db.php';

// require id
if (!isset($_GET['id'])) {
    header('Location: products.php');
    exit;
}

$id = intval($_GET['id']);
$qty = isset($_GET['qty']) ? max(1,intval($_GET['qty'])) : 1;

// init cart
if (!isset($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// increment or set
if (isset($_SESSION['cart'][$id])) {
    $_SESSION['cart'][$id] += $qty;
} else {
    $_SESSION['cart'][$id] = $qty;
}

// optional: limit quantity (example max 99)
if ($_SESSION['cart'][$id] > 99) $_SESSION['cart'][$id] = 99;

// go back to referer or products page
$back = $_SERVER['HTTP_REFERER'] ?? 'products.php';
header('Location: ' . $back);
exit;
