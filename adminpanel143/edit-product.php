<?php
session_start();
require "../includes/db.php";

// Admin check
if (!isset($_SESSION['user']) || $_SESSION['user']['is_admin'] != 1) {
    header("Location: ../login.php");
    exit;
}

include "includes/admin_header.php";

if (!isset($_GET['id'])) {
    header("Location: list-products.php");
    exit;
}

$id = intval($_GET['id']);
$p = $mysqli->query("SELECT * FROM products WHERE id=$id")->fetch_assoc();

// Fetch all categories
$category_result = $mysqli->query("SELECT DISTINCT category FROM products ORDER BY category ASC");
$categories = [];
while ($row = $category_result->fetch_assoc()) {
    $categories[] = $row['category'];
}

if ($_SERVER['REQUEST_METHOD'] == "POST") {

    $name = $_POST['name'];
    $mrp = intval($_POST['mrp']);          // NEW
    $price = intval($_POST['price']);      // Selling price
    $description = $_POST['description'];
    $category = $_POST['category'];
    $featured = isset($_POST['featured']) ? 1 : 0;
    $stock = intval($_POST['stock']);

    // Image update
    if (!empty($_FILES['image']['name'])) {
        $image_name = time() . "_" . basename($_FILES['image']['name']);
        move_uploaded_file($_FILES['image']['tmp_name'], "../assets/uploads/" . $image_name);
    } else {
        $image_name = $p['image'];
    }

    // UPDATE (only mrp added)
    $stmt = $mysqli->prepare("
        UPDATE products 
        SET name=?, mrp=?, price=?, description=?, category=?, image=?, featured=?, stock=? 
        WHERE id=?
    ");
    $stmt->bind_param(
        "siisssiii",
        $name,
        $mrp,
        $price,
        $description,
        $category,
        $image_name,
        $featured,
        $stock,
        $id
    );
    $stmt->execute();

    header("Location: list-products.php");
    exit;
}
?>

<div class="admin-container">

    <h1 class="text-3xl font-bold mb-4">Edit Product</h1>

    <form method="POST" enctype="multipart/form-data"
          class="bg-white p-8 rounded-xl shadow max-w-xl space-y-4">

        <input type="text" name="name" class="input w-full"
               value="<?= htmlspecialchars($p['name']) ?>" required>

        <!-- MRP -->
        <input type="number" name="mrp" class="input w-full"
               value="<?= htmlspecialchars($p['mrp']) ?>"
               placeholder="MRP (Original Price)" required>

        <!-- SELLING PRICE -->
        <input type="number" name="price" class="input w-full"
               value="<?= htmlspecialchars($p['price']) ?>"
               placeholder="Selling Price (Rs.)" required>

        <select name="category" class="input w-full" required>
            <option value="">Select Category</option>
            <?php foreach($categories as $cat): ?>
                <option value="<?= htmlspecialchars($cat) ?>"
                    <?= $p['category'] == $cat ? "selected" : "" ?>>
                    <?= htmlspecialchars($cat) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <textarea name="description" rows="4" class="input w-full"><?= htmlspecialchars($p['description']) ?></textarea>

        <input type="number" name="stock" class="input w-full"
               value="<?= htmlspecialchars($p['stock']) ?>" required>

        <label class="block font-semibold mt-2">Current Image:</label>
        <img src="../assets/uploads/<?= $p['image'] ?>" width="120" class="rounded">

        <label class="block mt-2">Upload New Image (optional):</label>
        <input type="file" name="image">

        <label class="mt-4 flex items-center gap-2">
            <input type="checkbox" name="featured" class="accent-blue-500"
                   <?= $p['featured'] ? "checked" : "" ?>>
            <span>Mark as Featured Product</span>
        </label>

        <button type="submit" class="btn-primary mt-4 w-full py-3">
            Save Changes
        </button>

    </form>

</div>

<?php include "includes/admin_footer.php"; ?>
