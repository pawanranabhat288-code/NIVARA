<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nivara Admin Panel</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100">

<!-- TOP NAVBAR -->
<header class="fixed top-0 left-0 w-full bg-white shadow z-50">
    <div class="px-6 py-4 flex justify-between items-center">

        <h1 class="text-2xl font-bold text-gray-700">Nivara Admin Panel</h1>

        <div class="flex items-center gap-6">
            <p class="font-semibold text-gray-700">
                Logged in as: <span class="text-blue-600"><?= $_SESSION['user']['name'] ?></span>
            </p>

            <a href="../logout.php" 
               class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600">
                Logout
            </a>
        </div>

    </div>
</header>

<!-- SIDEBAR -->
<aside class="fixed top-20 left-0 w-60 bg-gray-900 text-white h-full shadow-xl">

    <nav class="mt-6">

        <a href="dashboard.php" class="block px-6 py-3 hover:bg-gray-700">📊 Dashboard</a>
        <a href="add-product.php" class="block px-6 py-3 hover:bg-gray-700">➕ Add Product</a>
        <a href="list-products.php" class="block px-6 py-3 hover:bg-gray-700">📦 Products</a>
        <a href="users.php" class="block px-6 py-3 hover:bg-gray-700">👤 Users</a>
        <a href="manage-orders.php" class="block px-6 py-3 hover:bg-gray-700">🧾 Orders</a>
        <a href="messages.php" class="block px-6 py-3 hover:bg-gray-700">💬 Reviews / Messages</a>


    </nav>
</aside>

<!-- MAIN CONTENT WRAPPER -->
<div class="ml-60 mt-24 px-10 pb-10">
