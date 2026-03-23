<?php if (session_status() === PHP_SESSION_NONE) session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Nivara - Made in Nepal</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-[#FAF9F6]">

<!-- NAVBAR -->
<header class="w-full bg-white/90 backdrop-blur sticky top-0 shadow z-50">
    <div class="max-w-7xl mx-auto px-6 h-20 flex justify-between items-center">

        <a href="index.php" class="text-2xl font-extrabold text-[#795548]">Nivara</a>

        <nav class="hidden md:flex space-x-10 font-medium">
            <a href="index.php" class="hover:text-[#4CAF50]">Home</a>
            <a href="products.php" class="hover:text-[#4CAF50]">Shop</a>
            <a href="about.php" class="hover:text-[#4CAF50]">About</a>
            <a href="contact.php" class="hover:text-[#4CAF50]">Contact</a>

            <?php if (!empty($_SESSION['user'])): ?>
                <a href="my-orders.php" class="hover:text-[#4CAF50]">My Orders</a>
            <?php endif; ?>
        </nav>

        <?php
        $cart_count = 0;
        if (!empty($_SESSION['cart']) && is_array($_SESSION['cart'])) {
            foreach ($_SESSION['cart'] as $q) $cart_count += intval($q);
        }
        ?>

        <div class="flex items-center gap-4">

            <!-- Cart -->
            <a href="cart.php" class="relative p-2">
                <svg class="w-7 h-7 text-gray-800" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2 6h14"></path>
                </svg>
                <?php if($cart_count > 0): ?>
                    <span class="absolute -top-1 -right-0 inline-flex items-center justify-center px-2 py-1 text-xs font-semibold text-white bg-red-600 rounded-full">
                        <?php echo $cart_count; ?>
                    </span>
                <?php endif; ?>
            </a>

            <?php if (!empty($_SESSION['user'])): ?>

                <!-- Username -->
                <a href="profile.php" class="hidden md:inline-block font-medium">
                    <?php echo htmlspecialchars($_SESSION['user']['name'] ?? $_SESSION['user']['email']); ?>
                </a>

                <!-- My Orders (Mobile) -->
                <a href="my-orders.php" class="md:hidden text-sm hover:text-[#4CAF50]">
                    Orders
                </a>

                <a href="logout.php" class="ml-2 hover:text-red-500">
                    Logout
                </a>

            <?php else: ?>

                <a href="login.php" class="hover:text-[#4CAF50]">Login</a>

            <?php endif; ?>

            <!-- Mobile menu button -->
            <button id="mobileMenuBtn" class="md:hidden">
                <svg class="w-7 h-7 text-gray-800" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"></path>
                </svg>
            </button>

        </div>
    </div>
</header>

<div class="mt-4"></div>
