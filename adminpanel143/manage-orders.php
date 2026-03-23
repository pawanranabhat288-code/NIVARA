<?php
session_start();
require "../includes/db.php";

if (!isset($_SESSION['user']) || $_SESSION['user']['is_admin'] != 1) {
    header("Location: ../login.php");
    exit;
}

include "includes/admin_header.php";

// Correct column name: total
$res = $mysqli->query("
    SELECT o.id AS order_id, o.user_id, o.total, o.status, o.created_at,
           o.full_name, o.phone, o.city,
           u.email AS user_email
    FROM orders o
    JOIN users u ON o.user_id = u.id
    ORDER BY o.id DESC
");

$total_orders = $res->num_rows;
?>

<div class="admin-container">

<h1 class="text-3xl font-bold mb-2">Manage Orders</h1>
<p class="mb-6 text-gray-600 font-semibold">Total Orders: <?= $total_orders ?></p>

<table class="admin-table w-full border-collapse">
<thead>
<tr class="bg-gray-100">
<th class="p-3 border-b">Order ID</th>
<th class="p-3 border-b">Customer</th>
<th class="p-3 border-b">Products</th>
<th class="p-3 border-b">Total</th>
<th class="p-3 border-b">Status</th>
<th class="p-3 border-b">Date</th>
<th class="p-3 border-b">Action</th>
</tr>
</thead>

<tbody>
<?php while ($order = $res->fetch_assoc()): ?>

<?php
// Correct table name: order_items
$products_res = $mysqli->query("
    SELECT p.name, oi.qty
    FROM order_items oi
    JOIN products p ON oi.product_id = p.id
    WHERE oi.order_id = {$order['order_id']}
");

$products_list = [];
while($prod = $products_res->fetch_assoc()) {
    $products_list[] = htmlspecialchars($prod['name']) . " x " . $prod['qty'];
}
?>

<tr class="hover:bg-gray-50 transition">
<td class="p-3 border-b"><?= $order['order_id'] ?></td>

<td class="p-3 border-b">
<strong><?= htmlspecialchars($order['full_name']) ?></strong><br>
<span class="text-sm text-gray-500"><?= htmlspecialchars($order['user_email']) ?></span><br>
<span class="text-sm text-gray-500"><?= htmlspecialchars($order['phone']) ?></span><br>
<span class="text-sm text-gray-500"><?= htmlspecialchars($order['city']) ?></span>
</td>

<td class="p-3 border-b"><?= implode(", ", $products_list) ?></td>

<td class="p-3 border-b">Rs. <?= number_format($order['total']) ?></td>

<td class="p-3 border-b">
<span class="<?= $order['status'] == 'Pending' ? 'text-yellow-600' : 'text-green-600' ?>">
<?= htmlspecialchars($order['status']) ?>
</span>
</td>

<td class="p-3 border-b">
<?= date("Y-m-d H:i", strtotime($order['created_at'])) ?>
</td>

<td class="p-3 border-b flex gap-2">
<a class="bg-blue-600 text-white px-3 py-1 rounded"
href="edit-order.php?id=<?= $order['order_id'] ?>">
Update
</a>

<a class="bg-red-600 text-white px-3 py-1 rounded"
href="delete-order.php?id=<?= $order['order_id'] ?>"
onclick="return confirm('Delete this order?');">
Delete
</a>
</td>

</tr>

<?php endwhile; ?>
</tbody>
</table>
</div>

<?php include "includes/admin_footer.php"; ?>
