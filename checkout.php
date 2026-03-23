<?php
session_start();
require_once __DIR__ . '/includes/db.php';

if (empty($_SESSION['user'])) { 
    header('Location: login.php'); 
    exit; 
}

include __DIR__ . '/includes/header.php';

$cart = $_SESSION['cart'] ?? [];

if (empty($cart)) { 
    echo '<div class="p-12 text-center">Cart empty. <a class="text-green-600 font-semibold" href="products.php">Shop Now</a></div>'; 
    include __DIR__ . '/includes/footer.php'; 
    exit; 
}

$total = 0; 
$rows = [];

foreach ($cart as $id => $qty) {
    $stmt = $mysqli->prepare("SELECT id, price, name, stock FROM products WHERE id = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($p = $res->fetch_assoc()) {
        $rows[] = [
            'id' => $p['id'],
            'qty' => $qty,
            'price' => $p['price'],
            'name' => $p['name'],
            'stock' => $p['stock']
        ];
        $total += $p['price'] * $qty;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $payment_method = $_POST['payment_method'] ?? 'COD';

    $full_name = $_POST['full_name'];
    $phone     = $_POST['phone'];
    $email     = $_POST['email'];
    $city      = $_POST['city'];
    $address   = $_POST['address'];

    $status = "Pending";
    $payment_status = "Pending";

    $mysqli->begin_transaction();

    try {

        // CHECK STOCK
        foreach ($rows as $r) {
            if ($r['stock'] < $r['qty']) {
                throw new Exception("Insufficient stock for " . $r['name']);
            }
        }

        // INSERT ORDER
        $stmt = $mysqli->prepare(
            "INSERT INTO orders 
            (user_id,total,status,payment_method,payment_status,
            full_name,phone,email,city,address) 
            VALUES (?,?,?,?,?,?,?,?,?,?)"
        );

        $stmt->bind_param(
            "idssssssss",
            $_SESSION['user']['id'],
            $total,
            $status,
            $payment_method,
            $payment_status,
            $full_name,
            $phone,
            $email,
            $city,
            $address
        );

        $stmt->execute();
        $order_id = $stmt->insert_id;

        // INSERT ORDER ITEMS
        $stmt2 = $mysqli->prepare(
            "INSERT INTO order_items (order_id,product_id,qty,price) VALUES (?,?,?,?)"
        );

        foreach ($rows as $r) {
            $stmt2->bind_param(
                "iiii",
                $order_id,
                $r['id'],
                $r['qty'],
                $r['price']
            );
            $stmt2->execute();
        }

        $_SESSION['order_id'] = $order_id;
        $_SESSION['total'] = $total;

        // IF COD → Reduce stock immediately
        if ($payment_method == "COD") {

            foreach ($rows as $r) {

                $update_stock = $mysqli->prepare(
                    "UPDATE products 
                     SET stock = stock - ? 
                     WHERE id=? AND stock >= ?"
                );

                $update_stock->bind_param(
                    "iii",
                    $r['qty'],
                    $r['id'],
                    $r['qty']
                );

                $update_stock->execute();

                if ($update_stock->affected_rows == 0) {
                    throw new Exception("Stock update failed.");
                }
            }

            $update = $mysqli->prepare(
                "UPDATE orders 
                 SET status='Confirmed' 
                 WHERE id=?"
            );

            $update->bind_param("i", $order_id);
            $update->execute();

            $mysqli->commit();

            unset($_SESSION['cart']);
            header("Location: success.php?payment=Cash on Delivery");
            exit;
        }

        // IF eSewa
        if ($payment_method == "eSewa") {
            $mysqli->commit();
            header("Location: esewa_payment.php");
            exit;
        }

    } catch (Exception $e) {

        $mysqli->rollback();
        die("Error: " . $e->getMessage());
    }
}
?>

<section class="min-h-screen bg-gray-50 py-12">
<div class="max-w-6xl mx-auto grid md:grid-cols-2 gap-10 px-6">

<div class="bg-white p-8 rounded-2xl shadow-lg">
<h2 class="text-2xl font-bold mb-6 text-[#795548]">Order Summary</h2>

<?php foreach ($rows as $r): ?>
<div class="flex justify-between border-b py-3">
<div>
<p class="font-medium"><?php echo htmlspecialchars($r['name']); ?></p>
<p class="text-sm text-gray-500">Qty: <?php echo $r['qty']; ?></p>
</div>
<p class="font-semibold text-green-600">
Rs. <?php echo number_format($r['price'] * $r['qty'],0); ?>
</p>
</div>
<?php endforeach; ?>

<div class="flex justify-between mt-6 text-lg font-bold">
<span>Total:</span>
<span class="text-green-600">
Rs. <?php echo number_format($total,0); ?>
</span>
</div>
</div>

<div class="bg-white p-8 rounded-2xl shadow-lg">
<h2 class="text-2xl font-bold mb-6 text-[#795548]">Checkout Details</h2>

<form method="POST" class="space-y-4">

<input type="text" name="full_name" placeholder="Full Name" class="w-full border rounded-xl p-3" required>
<input type="text" name="phone" placeholder="Phone Number" class="w-full border rounded-xl p-3" required>
<input type="email" name="email" placeholder="Email Address" class="w-full border rounded-xl p-3" required>
<input type="text" name="city" placeholder="City" class="w-full border rounded-xl p-3" required>
<textarea name="address" placeholder="Full Address" class="w-full border rounded-xl p-3" required></textarea>

<label class="flex justify-between items-center border rounded-xl p-4 cursor-pointer">
<div class="flex items-center gap-3">
<input type="radio" name="payment_method" value="eSewa" required>
<span>Pay with eSewa</span>
</div>
</label>

<label class="flex justify-between items-center border rounded-xl p-4 cursor-pointer">
<div class="flex items-center gap-3">
<input type="radio" name="payment_method" value="COD" required>
<span>Cash on Delivery</span>
</div>
</label>

<button class="w-full bg-green-600 text-white py-3 rounded-xl mt-4">
Confirm & Place Order
</button>

</form>
</div>
</div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
