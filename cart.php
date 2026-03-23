<?php
session_start();
require __DIR__ . '/includes/db.php';
include __DIR__ . '/includes/header.php';

$cart = $_SESSION['cart'] ?? [];
?>

<div class="max-w-5xl mx-auto px-6 py-12">
    <h2 class="text-3xl font-bold mb-6 text-[#795548]">Your Cart</h2>

    <?php if (empty($cart)): ?>
        <p class="text-gray-600">Your cart is empty.</p>
        <a href="products.php" class="mt-4 inline-block bg-green-600 text-white px-6 py-2 rounded">
            Shop products
        </a>

    <?php else: ?>

        <div class="space-y-6">

        <?php
        $total = 0;
        $ids = array_keys($cart);

        if (!empty($ids)) {
            $placeholders = implode(',', array_fill(0, count($ids), '?'));
            $types = str_repeat('i', count($ids));
            $stmt = $mysqli->prepare("SELECT id, name, price, image FROM products WHERE id IN ($placeholders)");
            $stmt->bind_param($types, ...$ids);
            $stmt->execute();
            $res = $stmt->get_result();
            $products = [];
            while ($row = $res->fetch_assoc()) {
                $products[$row['id']] = $row;
            }
        } else {
            $products = [];
        }

        foreach ($cart as $id => $qty):
            if (!isset($products[$id])) continue;

            $p = $products[$id];

            $img = "assets/uploads/" . $p['image'];
            if (empty($p['image']) || !file_exists($img)) {
                $img = "assets/images/default.jpg";
            }

            $subtotal = $p['price'] * $qty;
            $total += $subtotal;
        ?>

            <div class="flex items-center justify-between bg-white p-4 rounded-xl shadow">

                <div class="flex items-center gap-4">
                    <img src="<?php echo $img; ?>"
                         class="w-20 h-20 rounded-lg object-cover">

                    <div>
                        <h3 class="font-semibold">
                            <?php echo htmlspecialchars($p['name']); ?>
                        </h3>

                        <p class="text-gray-600">
                            Rs. <?php echo number_format($p['price'], 0); ?>
                        </p>

                        <!-- QUANTITY FORM -->
                        <form action="update_cart.php" method="post"
                              class="inline-flex items-center mt-2">

                            <input type="hidden" name="id" value="<?php echo $id; ?>">

                            <!-- Decrease -->
                            <button type="submit"
                                    name="action"
                                    value="decrease"
                                    class="px-3 py-1 border rounded-l bg-gray-100">
                                -
                            </button>

                            <!-- Quantity Input -->
                            <input type="number"
                                   name="qty"
                                   value="<?php echo $qty; ?>"
                                   min="1"
                                   class="w-16 text-center border-t border-b">

                            <!-- Increase -->
                            <button type="submit"
                                    name="action"
                                    value="increase"
                                    class="px-3 py-1 border rounded-r bg-gray-100">
                                +
                            </button>

                        </form>
                    </div>
                </div>

                <div class="text-right">
                    <p class="font-semibold">
                        Rs. <?php echo number_format($subtotal, 0); ?>
                    </p>

                    <form action="remove_from_cart.php" method="post" class="mt-2">
                        <input type="hidden" name="id" value="<?php echo $id; ?>">
                        <button type="submit" class="text-red-600 hover:underline">
                            Remove
                        </button>
                    </form>
                </div>
            </div>

        <?php endforeach; ?>

        </div>

        <div class="mt-8 text-right">
            <p class="text-xl font-bold">
                Total: Rs. <?php echo number_format($total, 0); ?>
            </p>

            <a href="checkout.php"
               class="mt-4 inline-block bg-green-600 text-white px-8 py-3 rounded-lg hover:bg-green-700">
               Proceed to Checkout
            </a>
        </div>

    <?php endif; ?>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
