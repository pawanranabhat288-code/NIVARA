<?php
session_start();
require "../includes/db.php";

// Admin check
if (!isset($_SESSION['user']) || $_SESSION['user']['is_admin'] != 1) {
    header("Location: ../login.php"); // path relative to admin folder
    exit;
}

include "includes/admin_header.php";

// Counts
$product_count = $mysqli->query("SELECT COUNT(*) AS c FROM products")->fetch_assoc()['c'];
$user_count = $mysqli->query("SELECT COUNT(*) AS c FROM users")->fetch_assoc()['c'];
$order_count = $mysqli->query("SELECT COUNT(*) AS c FROM orders")->fetch_assoc()['c'];

$message_count = $mysqli->query("SELECT COUNT(*) AS c FROM contact_messages")->fetch_assoc()['c'];
?>

<h1 class="text-4xl font-bold text-gray-800 mb-6">Dashboard Overview</h1>

<!-- ANALYTIC CARDS -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-10">

    <div class="p-8 bg-white shadow-xl rounded-xl border-b-4 border-blue-500">
        <h2 class="text-gray-600 font-semibold">Total Products</h2>
        <p class="text-5xl font-bold text-blue-600 mt-3"><?= $product_count ?></p>
    </div>

    <div class="p-8 bg-white shadow-xl rounded-xl border-b-4 border-green-500">
        <h2 class="text-gray-600 font-semibold">Total Users</h2>
        <p class="text-5xl font-bold text-green-600 mt-3"><?= $user_count ?></p>
    </div>

    <div class="p-8 bg-white shadow-xl rounded-xl border-b-4 border-orange-500">
        <h2 class="text-gray-600 font-semibold">Orders</h2>
        <p class="text-5xl font-bold text-orange-600 mt-3"><?= $order_count ?></p>
    </div>

    <!-- ✅ NEW: Messages Card -->
    <div class="p-8 bg-white shadow-xl rounded-xl border-b-4 border-pink-500">
        <h2 class="text-gray-600 font-semibold">Messages</h2>
        <p class="text-5xl font-bold text-pink-600 mt-3"><?= $message_count ?></p>
    </div>

</div>

<!-- QUICK ACTION GRID -->
<h2 class="text-3xl font-bold text-gray-700 mb-4">Quick Actions</h2>

<div class="grid grid-cols-1 md:grid-cols-5 gap-8">

    <a href="add-product.php" 
       class="p-8 bg-blue-600 text-white rounded-xl shadow-lg hover:bg-blue-700 text-center font-semibold text-xl">
        ➕ Add Product
    </a>

    <a href="list-products.php" 
       class="p-8 bg-purple-600 text-white rounded-xl shadow-lg hover:bg-purple-700 text-center font-semibold text-xl">
        📦 View Products
    </a>

    <a href="users.php" 
       class="p-8 bg-green-600 text-white rounded-xl shadow-lg hover:bg-green-700 text-center font-semibold text-xl">
        👤 Manage Users
    </a>

    <a href="manage-orders.php" 
       class="p-8 bg-orange-600 text-white rounded-xl shadow-lg hover:bg-orange-700 text-center font-semibold text-xl">
        🧾 Manage Orders
    </a>

    <a href="messages.php" 
       class="p-8 bg-pink-600 text-white rounded-xl shadow-lg hover:bg-pink-700 text-center font-semibold text-xl">
        💬 Reviews / Messages
    </a>

</div>

<!-- END -->
<?php include "includes/admin_footer.php"; ?>