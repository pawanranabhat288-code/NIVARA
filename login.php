<?php
ob_start();
session_start();
require_once __DIR__ . '/includes/db.php'; // must define $mysqli

$err = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email = trim($_POST['email'] ?? '');
    $pass  = $_POST['password'] ?? '';
    $remember = !empty($_POST['remember']);

    // Prepare select query
    $stmt = $mysqli->prepare("SELECT id, name, email, password, is_admin FROM users WHERE email = ? LIMIT 1");
    
    if (!$stmt) {
        die("Query error: " . $mysqli->error);
    }

    $stmt->bind_param('s', $email);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($user = $res->fetch_assoc()) {

        // verify password
        if (password_verify($pass, $user['password'])) {

            // Store session
            $_SESSION['user'] = [
                'id'       => (int)$user['id'],
                'name'     => $user['name'],
                'email'    => $user['email'],
                'is_admin' => (int)$user['is_admin'],
            ];

            // Remember me feature
            if ($remember) {
                $token = bin2hex(random_bytes(16));

                // Ensure DB has column remember_token
                $update = $mysqli->prepare("UPDATE users SET remember_token = ? WHERE id = ?");
                $update->bind_param("si", $token, $user['id']);
                $update->execute();

                setcookie(
                    "rememberme",
                    $user['id'] . ":" . $token,
                    time() + (86400 * 7), // 7 days
                    "/",
                    "",
                    false,
                    true
                );
            }

            // Redirect based on role
            if ($user['is_admin']) {
                header("Location: /nivara/adminpanel143/dashboard.php");
            } else {
                header("Location: /nivara/index.php");
            }
            exit;
        } else {
            $err = "Invalid credentials.";
        }

    } else {
        $err = "Invalid credentials.";
    }

}
?>

<?php include __DIR__ . '/includes/header.php'; ?>

<section class="max-w-md mx-auto px-6 py-20">
  <div class="bg-white p-8 rounded-xl shadow">
    <h2 class="text-2xl font-bold text-center text-[#795548] mb-4">Login</h2>

    <?php if ($err): ?>
      <p class="text-red-600 mb-3"><?= htmlspecialchars($err); ?></p>
    <?php endif; ?>

    <form method="post">
      <input name="email" type="email" class="w-full border p-3 rounded mb-3" placeholder="Email" required>
      <input name="password" type="password" class="w-full border p-3 rounded mb-3" placeholder="Password" required>

      <label class="flex items-center mb-3">
        <input type="checkbox" name="remember" class="mr-2"> Remember Me
      </label>

      <button class="w-full bg-[#4CAF50] text-white py-3 rounded">Login</button>
    </form>

    <p class="mt-4 text-center">Don't have an account?
        <a href="signup.php" class="text-[#4CAF50]">Sign up</a>
    </p>
  </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
