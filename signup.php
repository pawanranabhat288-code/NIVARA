<?php
session_start();
require_once __DIR__ . '/includes/db.php';

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name  = $mysqli->real_escape_string(trim($_POST['name'] ?? ''));
    $email = $mysqli->real_escape_string(trim($_POST['email'] ?? ''));
    $pass  = $_POST['password'] ?? '';

    if (strlen($name) < 2) {
        $errors[] = 'Please provide a valid name.';
    }

    if (!preg_match('/^[a-zA-Z][a-zA-Z0-9._]*@gmail\.com$/', $email)) {
        $errors[] = 'Please provide a valid  email.';
    }

    if (strlen($pass) < 6) {
        $errors[] = 'Password must be at least 6 characters.';
    }

    if (empty($errors)) {
        $hash = password_hash($pass, PASSWORD_DEFAULT);
        $stmt = $mysqli->prepare("INSERT INTO users (name,email,password) VALUES (?,?,?)");
        $stmt->bind_param('sss', $name, $email, $hash);

        if ($stmt->execute()) {
            header('Location: login.php');
            exit;
        } else {
            $errors[] = 'Registration failed: email may already be used.';
        }
    }
}
?>

<?php include __DIR__ . '/includes/header.php'; ?>

<section class="max-w-md mx-auto px-6 py-20">
  <div class="bg-white p-8 rounded-xl shadow">
    <h2 class="text-2xl font-bold text-center text-[#795548] mb-4">Sign Up</h2>

    <?php if (!empty($errors)): ?>
      <?php foreach ($errors as $error): ?>
        <p class="text-red-600 mb-2"><?php echo htmlspecialchars($error); ?></p>
      <?php endforeach; ?>
    <?php endif; ?>

    <form method="post">
      <input name="name" type="text" class="w-full border p-3 rounded mb-3" placeholder="Full name" required>
      <input name="email" type="email" class="w-full border p-3 rounded mb-3" placeholder="Email" required>
      <input name="password" type="password" class="w-full border p-3 rounded mb-3" placeholder="Password" required>
      <button class="w-full bg-[#4CAF50] text-white py-3 rounded">Create Account</button>
    </form>

    <p class="mt-4 text-center">Already have an account? <a href="login.php" class="text-[#4CAF50]">Login</a></p>
  </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>