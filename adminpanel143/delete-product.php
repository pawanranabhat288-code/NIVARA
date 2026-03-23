<?php
session_start();
if ($_SESSION['role'] !== "admin") {
    header("Location: login.php");
    exit;
}

require "../includes/db.php";

if (!isset($_GET['id'])) {
    header("Location: list-products.php");
    exit;
}

$id = intval($_GET['id']);

$mysqli->query("DELETE FROM products WHERE id=$id");

header("Location: list-products.php");
exit;
