<?php
session_start();
require_once __DIR__ . '/includes/db.php';

if (empty($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user']['id'];
$success = "";
$error = "";

// Get current user data
$stmt = $mysqli->prepare("SELECT name, email FROM users WHERE id=?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

// UPDATE PROFILE
if (isset($_POST['update_profile'])) {

    $name = trim($_POST['name']);
    $email = trim($_POST['email']);

    $stmt = $mysqli->prepare("UPDATE users SET name=?, email=? WHERE id=?");
    $stmt->bind_param("ssi", $name, $email, $user_id);

    if ($stmt->execute()) {
        $_SESSION['user']['name'] = $name;
        $_SESSION['user']['email'] = $email;
        $success = "Profile updated successfully!";
    } else {
        $error = "Failed to update profile.";
    }
}

// CHANGE PASSWORD
if (isset($_POST['change_password'])) {

    $old = $_POST['old_password'];
    $new = $_POST['new_password'];

    $stmt = $mysqli->prepare("SELECT password FROM users WHERE id=?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $res = $stmt->get_result()->fetch_assoc();

    if (password_verify($old, $res['password'])) {

        $new_hash = password_hash($new, PASSWORD_DEFAULT);

        $stmt = $mysqli->prepare("UPDATE users SET password=? WHERE id=?");
        $stmt->bind_param("si", $new_hash, $user_id);
        $stmt->execute();

        $success = "Password changed successfully!";
    } else {
        $error = "Old password is incorrect!";
    }
}
?>

<?php include __DIR__ . '/includes/header.php'; ?>

<section class="max-w-4xl mx-auto px-6 py-12">

<h2 class="text-3xl font-bold text-[#795548] mb-6">My Profile</h2>

<?php if ($success) echo "<p class='text-green-600 mb-4'>$success</p>"; ?>
<?php if ($error) echo "<p class='text-red-600 mb-4'>$error</p>"; ?>

<div class="grid md:grid-cols-2 gap-8">

<!-- UPDATE PROFILE -->
<div class="bg-white p-6 rounded-xl shadow">
<h3 class="text-xl font-bold mb-4">Edit Profile</h3>

<form method="post">
<input type="text" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" class="w-full border p-3 rounded mb-3" required>

<input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" class="w-full border p-3 rounded mb-3" required>

<button name="update_profile" class="bg-[#4CAF50] text-white px-6 py-2 rounded">
Update Profile
</button>
</form>

</div>

<!-- CHANGE PASSWORD -->
<div class="bg-white p-6 rounded-xl shadow">
<h3 class="text-xl font-bold mb-4">Change Password</h3>

<form method="post">
<input type="password" name="old_password" placeholder="Old Password" class="w-full border p-3 rounded mb-3" required>

<input type="password" name="new_password" placeholder="New Password" class="w-full border p-3 rounded mb-3" required>

<button name="change_password" class="bg-[#795548] text-white px-6 py-2 rounded">
Change Password
</button>
</form>

</div>

</div>

</section>

<?php include __DIR__ . '/includes/footer.php'; ?>