<?php
session_start();
require_once __DIR__ . '/includes/db.php';

if (empty($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

include __DIR__ . '/includes/header.php';

$user_id = $_SESSION['user']['id'];

$stmt = $mysqli->prepare("
    SELECT * FROM orders 
    WHERE user_id=? 
    ORDER BY id DESC
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$orders = $stmt->get_result();
?>

<section class="min-h-screen bg-gray-50 py-12">
<div class="max-w-6xl mx-auto px-6">

<h2 class="text-3xl font-bold mb-8 text-[#795548]">
My Orders
</h2>

<?php if ($orders->num_rows == 0): ?>
    <div class="bg-white p-8 rounded-xl shadow text-center">
        You have not placed any orders yet.
    </div>
<?php endif; ?>

<?php while ($order = $orders->fetch_assoc()): ?>

<div class="bg-white rounded-2xl shadow-lg mb-8 p-6">

    <div class="flex justify-between mb-4 flex-wrap gap-4">
        <div>
            <p class="font-semibold">Order ID: #<?php echo $order['id']; ?></p>
            <p class="text-sm text-gray-500">
                Date: <?php echo date("Y-m-d H:i", strtotime($order['created_at'])); ?>
            </p>
        </div>

        <div class="text-right">
            <p class="font-bold text-green-600">
                Rs. <?php echo number_format($order['total'],0); ?>
            </p>

            <p class="text-sm">
                Status:
                <span class="font-semibold">
                    <?php echo htmlspecialchars($order['status']); ?>
                </span>
            </p>

            <p class="text-sm">
                Payment:
                <span class="font-semibold">
                    <?php echo htmlspecialchars($order['payment_status']); ?>
                </span>
            </p>
        </div>
    </div>

    <hr class="my-4">

    <?php
    $items = $mysqli->prepare("
        SELECT oi.qty, oi.price, p.name, p.image 
        FROM order_items oi
        JOIN products p ON oi.product_id = p.id
        WHERE oi.order_id=?
    ");
    $items->bind_param("i", $order['id']);
    $items->execute();
    $result_items = $items->get_result();
    ?>

    <?php while ($item = $result_items->fetch_assoc()): ?>

        <?php
        $image = trim($item['image']);
        $imagePath = "assets/uploads/" . $image;
        ?>

        <div class="flex justify-between items-center border-b py-4">

            <div class="flex items-center gap-4">

                <img src="<?php echo $imagePath; ?>" 
                     class="w-16 h-16 object-cover rounded-lg border"
                     onerror="this.src='assets/images/no-image.png'">

                <div>
                    <p class="font-medium">
                        <?php echo htmlspecialchars($item['name']); ?>
                    </p>
                    <p class="text-sm text-gray-500">
                        Qty: <?php echo $item['qty']; ?>
                    </p>
                </div>
            </div>

            <p class="font-semibold text-green-600">
                Rs. <?php echo number_format($item['price'] * $item['qty'],0); ?>
            </p>

        </div>

    <?php endwhile; ?>

</div>

<?php endwhile; ?>

</div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
