<?php
require "includes/db.php";
include "includes/header.php"; // your website header

$category_filter = isset($_GET['category']) ? $_GET['category'] : '';

if ($category_filter) {
    $stmt = $mysqli->prepare("SELECT id, name, price, mrp, category, image, stock FROM products WHERE category = ?");
    $stmt->bind_param("s", $category_filter);
} else {
    $stmt = $mysqli->prepare("SELECT id, name, price, mrp, category, image, stock FROM products");
}
$stmt->execute();
$result = $stmt->get_result();

// Fetch categories for filter buttons
$categories_result = $mysqli->query("SELECT DISTINCT category FROM products");
?>

<div class="container mx-auto p-6">

    <h1 class="text-3xl font-bold mb-6 text-gray-800">
        Products <?= $category_filter ? "in $category_filter" : "" ?>
    </h1>

    <!-- Category Filters -->
    <div class="flex flex-wrap gap-2 mb-6">
        <a href="products.php" class="px-3 py-1 bg-gray-200 rounded-full hover:bg-gray-300 transition">All</a>
        <?php while($cat = $categories_result->fetch_assoc()): ?>
            <a href="products.php?category=<?= urlencode($cat['category']) ?>" 
               class="px-3 py-1 bg-gray-200 rounded-full hover:bg-gray-300 transition">
               <?= htmlspecialchars($cat['category']) ?>
            </a>
        <?php endwhile; ?>
    </div>

    <!-- Products Grid -->
    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">

        <?php while($product = $result->fetch_assoc()): ?>
            <div class="bg-white rounded-lg shadow hover:shadow-lg transition overflow-hidden">

                <!-- Image & link to product.php -->
                <a href="product.php?id=<?= $product['id'] ?>">
                    <div class="w-full h-40 bg-gray-100 flex items-center justify-center overflow-hidden">
                        <img src="assets/uploads/<?= $product['image'] ?>" 
                             alt="<?= htmlspecialchars($product['name']) ?>" 
                             class="w-full h-full object-cover transition-transform hover:scale-105">
                    </div>
                </a>

                <!-- Product Info -->
                <div class="p-3 text-center">
                    <a href="product.php?id=<?= $product['id'] ?>" 
                       class="font-semibold text-sm text-gray-800 hover:text-blue-600 truncate block" 
                       title="<?= htmlspecialchars($product['name']) ?>">
                        <?= htmlspecialchars($product['name']) ?>
                    </a>

                    <span class="inline-block text-xs text-white bg-green-500 px-2 py-0.5 rounded-full mt-1">
                        <?= htmlspecialchars($product['category']) ?>
                    </span>

                    <!-- PRICE DISPLAY -->
                    <?php if (!empty($product['mrp']) && $product['mrp'] > $product['price']): ?>
                        <p class="text-gray-400 line-through text-xs mt-1">
                            Rs. <?= number_format($product['mrp']) ?>
                        </p>
                    <?php endif; ?>
                    <p class="text-green-700 font-bold text-sm mt-1">
                        Rs. <?= number_format($product['price']) ?>
                    </p>

                    <!-- STOCK DISPLAY -->
                    <?php if ($product['stock'] > 10): ?>
                        <p class="text-green-600 text-xs mt-1 font-semibold">✔ In Stock</p>
                    <?php elseif ($product['stock'] > 0): ?>
                        <p class="text-orange-500 text-xs mt-1 font-semibold">⚠ Only <?= $product['stock'] ?> left</p>
                    <?php else: ?>
                        <p class="text-red-600 text-xs mt-1 font-semibold">✖ Out of Stock</p>
                    <?php endif; ?>

                    <!-- ADD TO CART -->
                    <?php if ($product['stock'] > 0): ?>
                        <a href="add_to_cart.php?id=<?= $product['id'] ?>" 
                           class="mt-3 block bg-green-600 text-white text-center py-2 rounded-lg hover:bg-green-700">
                            Add to Cart
                        </a>
                    <?php else: ?>
                        <button class="mt-3 block bg-gray-300 text-gray-700 text-center py-2 rounded-lg cursor-not-allowed">
                            Out of Stock
                        </button>
                    <?php endif; ?>

                </div>

            </div>
        <?php endwhile; ?>

    </div>
</div>

<?php include "includes/footer.php"; // your website footer ?>
