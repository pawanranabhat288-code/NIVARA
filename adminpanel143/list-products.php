<?php
session_start();
require "../includes/db.php";

// Admin check
if (!isset($_SESSION['user']) || $_SESSION['user']['is_admin'] != 1) {
    header("Location: ../login.php");
    exit;
}

include "includes/admin_header.php";

// Fetch all products
$res = $mysqli->query("SELECT * FROM products ORDER BY id DESC");
$total_products = $res->num_rows; // Count total products

// Calculate total stock and total value
$total_stock = 0;
$total_value = 0;

$res->data_seek(0); // reset pointer to start
while($row = $res->fetch_assoc()) {
    $total_stock += $row['stock'];
    $total_value += $row['price'] * $row['stock'];
}
$res->data_seek(0); // reset again for table loop
?>

<div class="admin-container">

    <h1 class="text-3xl font-bold mb-2">Manage Products</h1>

    <!-- TOTALS -->
    <div class="mb-6 text-gray-600 font-semibold flex flex-wrap gap-6">
        <span>Total Products: <?= $total_products ?></span>
        <span>Total Stock: <?= $total_stock ?></span>
        <span>Total Value: Rs. <?= number_format($total_value) ?></span>
    </div>

    <table class="admin-table w-full border-collapse">
        <thead>
            <tr class="bg-gray-100">
                <th class="p-3 border-b">Image</th>
                <th class="p-3 border-b">Name</th>
                <th class="p-3 border-b">Category</th>
                <th class="p-3 border-b">Price (Rs.)</th>
                <th class="p-3 border-b">Featured</th>
                <th class="p-3 border-b">Stock</th>
                <th class="p-3 border-b">Action</th>
            </tr>
        </thead>

        <tbody>
        <?php while ($p = $res->fetch_assoc()): ?>
            <tr class="hover:bg-gray-50 transition">
                <td class="p-3 border-b">
                    <img src="../assets/uploads/<?php echo $p['image']; ?>" alt="<?php echo htmlspecialchars($p['name']); ?>" width="60" class="rounded">
                </td>
                <td class="p-3 border-b"><?php echo htmlspecialchars($p['name']); ?></td>
                <td class="p-3 border-b"><?php echo htmlspecialchars($p['category']); ?></td>
                <td class="p-3 border-b">Rs. <?php echo number_format($p['price']); ?></td>
                <td class="p-3 border-b"><?php echo $p['featured'] ? "Yes" : "No"; ?></td>
                <td class="p-3 border-b">
                    <?php 
                        if($p['stock'] > 0) {
                            echo $p['stock'] . " in stock";
                        } else {
                            echo "<span class='text-red-500 font-semibold'>Out of stock</span>";
                        }
                    ?>
                </td>
                <td class="p-3 border-b flex gap-2">
                    <a class="btn-edit bg-blue-600 text-white px-3 py-1 rounded hover:bg-blue-700 transition"
                       href="edit-product.php?id=<?php echo $p['id']; ?>">Edit</a>
                    <a class="btn-delete bg-red-600 text-white px-3 py-1 rounded hover:bg-red-700 transition"
                       href="delete-product.php?id=<?php echo $p['id']; ?>" onclick="return confirm('Delete this product?');">
                       Delete
                    </a>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>

</div>

<?php include "includes/admin_footer.php"; ?>
