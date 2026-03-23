<?php
session_start();
require "../includes/db.php";

// ADMIN CHECK
if (!isset($_SESSION['user']) || $_SESSION['user']['is_admin'] != 1) {
    header("Location: ../login.php");
    exit;
}

include __DIR__ . "/includes/admin_header.php";

// Fetch existing categories dynamically
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
    $featured = isset($_POST['featured']) ? 1 : 0;
    $stock = intval($_POST['stock']);

    // Use new category if entered
    $category = !empty($_POST['new_category']) ? $_POST['new_category'] : $_POST['category'];

    // image
    $image_name = time() . "_" . basename($_FILES['image']['name']);
    $target_path = "../assets/uploads/" . $image_name;
    move_uploaded_file($_FILES['image']['tmp_name'], $target_path);

    // insert (ONLY CHANGE IS mrp ADDED)
    $stmt = $mysqli->prepare("
        INSERT INTO products (name, mrp, price, description, category, image, featured, stock) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->bind_param(
        "siisssii",
        $name,
        $mrp,
        $price,
        $description,
        $category,
        $image_name,
        $featured,
        $stock
    );
    $stmt->execute();

    header("Location: dashboard.php");
    exit;
}
?>

<div class="p-8">
    <h1 class="text-4xl font-bold text-gray-800 mb-8">Add New Product</h1>

    <form method="POST" enctype="multipart/form-data"
          class="bg-white p-8 rounded-xl shadow-lg max-w-2xl mx-auto space-y-4">

        <input type="text" name="name"
               class="w-full p-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
               placeholder="Product Name" required>

        <!-- MRP -->
        <input type="number" name="mrp"
               class="w-full p-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
               placeholder="MRP (Original Price)" required>

        <!-- SELLING PRICE -->
        <input type="number" name="price"
               class="w-full p-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
               placeholder="Selling Price (Rs.)" required>

        <!-- CATEGORY -->
        <label class="block font-semibold mt-2">Category:</label>
        <select id="categorySelect" name="category"
                class="w-full p-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                required>
            <option value="">Select Category</option>
            <?php foreach ($categories as $cat): ?>
                <option value="<?= htmlspecialchars($cat) ?>"><?= htmlspecialchars($cat) ?></option>
            <?php endforeach; ?>
            <option value="__new">-- Add New Category --</option>
        </select>

        <input type="text" id="newCategory" name="new_category"
               placeholder="Enter new category"
               class="w-full p-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 mt-2 hidden">

        <textarea name="description" rows="4"
                  class="w-full p-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                  placeholder="Product Description" required></textarea>

        <input type="number" name="stock"
               class="w-full p-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
               placeholder="Stock Quantity" required>

        <label class="block font-semibold mt-2">Upload Product Image:</label>
        <input type="file" name="image" class="border p-2 rounded-lg w-full" required>

        <label class="mt-4 flex items-center gap-2">
            <input type="checkbox" name="featured" class="accent-blue-500">
            <span>Mark as Featured Product</span>
        </label>

        <button type="submit"
                class="w-full bg-blue-600 text-white py-3 rounded-xl shadow hover:bg-blue-700 font-bold text-lg mt-4">
            Add Product
        </button>

    </form>
</div>

<script>
document.getElementById('categorySelect').addEventListener('change', function () {
    var newCatInput = document.getElementById('newCategory');
    if (this.value === '__new') {
        newCatInput.style.display = 'block';
        newCatInput.required = true;
    } else {
        newCatInput.style.display = 'none';
        newCatInput.required = false;
    }
});
</script>

<?php include __DIR__ . "/includes/admin_footer.php"; ?>
