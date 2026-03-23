<?php
session_start();
include "includes/header.php";

$payment = $_GET['payment'] ?? "Unknown";
?>

<section class="min-h-screen flex items-center justify-center bg-gray-50">

<div class="bg-white p-10 rounded-2xl shadow-lg text-center max-w-md">

<div class="text-green-600 text-6xl mb-4">
✓
</div>

<h2 class="text-3xl font-bold mb-2">
Payment Successful
</h2>

<p class="text-gray-600 mb-2">
Your order has been placed successfully.
</p>

<p class="text-gray-600 mb-6">
Payment Method: 
<span class="font-semibold text-green-600">
<?php echo htmlspecialchars($payment); ?>
</span>
</p>

<a href="index.php"
class="bg-green-600 text-white px-6 py-3 rounded-xl hover:bg-green-700">

Continue Shopping

</a>

</div>

</section>

<?php include "includes/footer.php"; ?>
