<?php
session_start();
require_once __DIR__ . '/includes/db.php';

$success = "";
$error = "";

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $message = trim($_POST['message']);

    $stmt = $mysqli->prepare(
        "INSERT INTO contact_messages (name, email, message) VALUES (?, ?, ?)"
    );

    $stmt->bind_param("sss", $name, $email, $message);

    if ($stmt->execute()) {
        $success = "Message sent successfully!";
    } else {
        $error = "Failed to send message.";
    }
}
?>

<?php include __DIR__ . '/includes/header.php'; ?>

<section class="max-w-3xl mx-auto px-6 py-12">
  <h1 class="text-2xl font-bold text-[#795548]">Contact</h1>

  <div class="mt-6 bg-white p-6 rounded-xl shadow">
    <p class="text-gray-700">Email: <a href="mailto:support@nivara.com" class="text-[#4CAF50]">support@nivara.com</a></p>
    <p class="text-gray-700 mt-2">Phone: +977 9800000000</p>
    <p class="text-gray-700 mt-2">Address: Gaindakot, Nepal</p>
  </div>

  <div class="mt-8">
    <form method="post" class="bg-white p-6 rounded-xl shadow">

      <!-- SUCCESS / ERROR -->
      <?php if (!empty($success)) { ?>
        <p class="text-green-600 mb-3"><?php echo $success; ?></p>
      <?php } ?>

      <?php if (!empty($error)) { ?>
        <p class="text-red-600 mb-3"><?php echo $error; ?></p>
      <?php } ?>

      <input name="name" class="w-full border p-3 rounded mb-3" placeholder="Your name" required>

      <input name="email" type="email" class="w-full border p-3 rounded mb-3" placeholder="Your email" required>

      <textarea name="message" class="w-full border p-3 rounded mb-3" placeholder="Message" required></textarea>

      <button class="bg-[#4CAF50] text-white px-6 py-2 rounded hover:bg-green-600 transition">
        Send Message
      </button>

    </form>
  </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>