<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/db.php';

function is_logged_in() {
    return !empty($_SESSION['user']);
}

function is_admin() {
    return !empty($_SESSION['user']) && !empty($_SESSION['user']['is_admin']);
}

function require_login() {
    if(!is_logged_in()){
        header('Location: /nivara/login.php');
        exit;
    }
}

function require_admin() {
    if(!is_admin()){
        header('Location: /nivara/login.php');
        exit;
    }
}
?>
