<?php
require "includes/db.php";
include "includes/header.php";

if (!isset($_GET['id'])) {
    echo "<p class='text-red-500 p-6'>Product not found.</p>";
    include "includes/footer.php";
    exit;
}

$id = intval($_GET['id']);

$stmt = $mysqli->prepare("SELECT * FROM products WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$product = $stmt->get_result()->fetch_assoc();

if (!$product) {
    echo "<p class='text-red-500 p-6'>Product not found.</p>";
    include "includes/footer.php";
    exit;
}
?>

<div class="container mx-auto p-6">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 bg-white p-6 rounded-xl shadow-lg">

        <!-- Product Image -->
        <div class="flex justify-center">
            <img src="assets/uploads/<?= $product['image'] ?>" 
                 alt="<?= htmlspecialchars($product['name']) ?>" 
                 class="w-full h-auto rounded-lg">
        </div>

        <!-- Product Details -->
        <div>
            <h1 class="text-3xl font-bold text-gray-800 mb-2"><?= htmlspecialchars($product['name']) ?></h1>
            <span class="inline-block text-xs text-white bg-green-500 px-2 py-0.5 rounded-full mb-4"><?= htmlspecialchars($product['category']) ?></span>
            
            <!-- PRICE DISPLAY -->
            <?php if (!empty($product['mrp']) && $product['mrp'] > $product['price']): ?>
                <p class="text-gray-400 line-through text-xl mb-1">Rs. <?= number_format($product['mrp']) ?></p>
            <?php endif; ?>
            <p class="text-green-700 font-bold text-2xl mb-4">Rs. <?= number_format($product['price']) ?></p>
            
            <!-- DB Description -->
            <h2 class="text-lg font-semibold mb-2">Product Description:</h2>
            <p class="text-gray-700 mb-4"><?= nl2br(htmlspecialchars($product['description'])) ?></p>

            <!-- FIXED PRODUCT DETAILS SECTION -->
            <h2 class="text-lg font-semibold mb-2">Product Details:</h2>
            <ul class="list-disc list-inside text-gray-700 mb-4">
                <li>Handcrafted in Nepal</li>
                <li>100% authentic materials</li>
                <li>Supports local artisans</li>
                <li>Free shipping on orders over NPR 2000</li>
            </ul>

            <!-- STOCK -->
            <?php if ($product['stock'] > 10): ?>
                <p class="text-green-600 font-semibold mb-2">✔ In Stock</p>
            <?php elseif ($product['stock'] > 0): ?>
                <p class="text-orange-500 font-semibold mb-2">⚠ Only <?= $product['stock'] ?> left</p>
            <?php else: ?>
                <p class="text-red-600 font-semibold mb-2">✖ Out of Stock</p>
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
</div>

<?php include "includes/footer.php"; ?>
