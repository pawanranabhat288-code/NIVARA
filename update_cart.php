<?php
session_start();
require __DIR__ . '/includes/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: cart.php'); exit;
}

$id = intval($_POST['id'] ?? 0);
$action = $_POST['action'] ?? '';
$qty = isset($_POST['qty']) ? max(0,intval($_POST['qty'])) : null;

if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];

if ($id <= 0) {
    header('Location: cart.php'); exit;
}

// set explicit qty
if ($qty !== null) {
    if ($qty <= 0) {
        unset($_SESSION['cart'][$id]);
    } else {
        $_SESSION['cart'][$id] = min(99, $qty);
    }
    header('Location: cart.php'); exit;
}

// perform increase / decrease
if ($action === 'increase') {
    if (!isset($_SESSION['cart'][$id])) $_SESSION['cart'][$id] = 0;
    $_SESSION['cart'][$id] = min(99, $_SESSION['cart'][$id] + 1);
} elseif ($action === 'decrease') {
    if (isset($_SESSION['cart'][$id])) {
        $_SESSION['cart'][$id]--;
        if ($_SESSION['cart'][$id] <= 0) unset($_SESSION['cart'][$id]);
    }
}

header('Location: cart.php'); exit;
