<?php
session_start();
require "../includes/db.php";

// Admin check
if (!isset($_SESSION['user']) || $_SESSION['user']['is_admin'] != 1) {
    header("Location: ../login.php");
    exit;
}

include "includes/admin_header.php";

// Fetch all users
$res = $mysqli->query("SELECT * FROM users ORDER BY id DESC");
$total_users = $res->num_rows; // Count total users
?>

<div class="admin-container">

    <h1 class="text-3xl font-bold mb-2">Manage Users</h1>
    <p class="mb-6 text-gray-600 font-semibold">Total Users: <?= $total_users ?></p>

    <table class="admin-table w-full border-collapse">
        <thead>
            <tr class="bg-gray-100">
                <th class="p-3 border-b">ID</th>
                <th class="p-3 border-b">Name</th>
                <th class="p-3 border-b">Email</th>
                <th class="p-3 border-b">Role</th>
                <th class="p-3 border-b">Registered At</th>
                <th class="p-3 border-b">Action</th>
            </tr>
        </thead>

        <tbody>
        <?php while ($user = $res->fetch_assoc()): ?>
            <tr class="hover:bg-gray-50 transition">
                <td class="p-3 border-b"><?= $user['id'] ?></td>
                <td class="p-3 border-b"><?= htmlspecialchars($user['name']) ?></td>
                <td class="p-3 border-b"><?= htmlspecialchars($user['email']) ?></td>
                <td class="p-3 border-b">
                    <?= $user['is_admin'] ? "Admin" : "User" ?>
                </td>
                <td class="p-3 border-b">
                    <?= date("Y-m-d H:i", strtotime($user['created_at'])) ?>
                </td>
                <td class="p-3 border-b flex gap-2">
                    <a class="btn-edit bg-blue-600 text-white px-3 py-1 rounded hover:bg-blue-700 transition"
                       href="edit-user.php?id=<?= $user['id'] ?>">Edit</a>
                    <a class="btn-delete bg-red-600 text-white px-3 py-1 rounded hover:bg-red-700 transition"
                       href="delete-user.php?id=<?= $user['id'] ?>" onclick="return confirm('Delete this user?');">
                       Delete
                    </a>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>

</div>

<?php include "includes/admin_footer.php"; ?>
