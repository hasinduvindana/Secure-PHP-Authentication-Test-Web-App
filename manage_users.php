<?php
require './src/db/connection.php';
session_start();

// Check if the user is logged in and has the correct role (admin)
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] !== 1) {
    header('Location: ./error.html');
    exit();
}

$pdo = getDBConnection();

// Fetch all users with their roles
$stmt = $pdo->query('SELECT users.id, username, email, role_name
                     FROM users
                     JOIN roles ON users.role_id = roles.id');
$users = $stmt->fetchAll();

// Fetch roles for adding/editing users
$stmt_roles = $pdo->query('SELECT id, role_name FROM roles');
$roles = $stmt_roles->fetchAll();

// Handle Add User form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_user'])) {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role_id = $_POST['role_id'];

    // Insert the new user
    $stmt = $pdo->prepare('INSERT INTO users (username, email, password, role_id) VALUES (?, ?, ?, ?)');
    $stmt->execute([$username, $email, $password, $role_id]);

    header('Location: manage_users.php');
    exit();
}

// Handle Edit User form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_user'])) {
    $user_id = $_POST['user_id'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $role_id = $_POST['role_id'];

    // Update the user
    $stmt = $pdo->prepare('UPDATE users SET username = ?, email = ?, role_id = ? WHERE id = ?');
    $stmt->execute([$username, $email, $role_id, $user_id]);

    header('Location: manage_users.php');
    exit();
}

// Handle delete user request
if (isset($_GET['delete_id'])) {
    $user_id = $_GET['delete_id'];

    // First, delete user-related logs from user_logs and failed_logins
    $stmt = $pdo->prepare('DELETE FROM user_logs WHERE user_id = ?');
    $stmt->execute([$user_id]);

    $stmt = $pdo->prepare('DELETE FROM failed_logins WHERE user_id = ?');
    $stmt->execute([$user_id]);

    // Finally, delete the user
    $stmt = $pdo->prepare('DELETE FROM users WHERE id = ?');
    $stmt->execute([$user_id]);

    header('Location: manage_users.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            background: linear-gradient(to right, #f3f4f6, #ffffff);
            font-family: 'Poppins', sans-serif;
            height: 100vh;
            margin: 0;
        }

        /* Full-screen container */
        .container {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        /* Custom table styles */
        table {
            border-collapse: separate;
            border-spacing: 0 15px;
        }

        th:first-child,
        td:first-child {
            border-top-left-radius: 10px;
            border-bottom-left-radius: 10px;
        }

        th:last-child,
        td:last-child {
            border-top-right-radius: 10px;
            border-bottom-right-radius: 10px;
        }
    </style>
</head>

<body class="bg-gray-100 text-gray-800">
    <div class="container mx-auto p-10 bg-white rounded-lg shadow-lg max-w-6xl">
        <h2 class="text-5xl font-extrabold text-center mb-10 text-gray-800">Manage Users</h2>

        <!-- Users Table -->
        <div class="overflow-x-auto">
            <table class="min-w-full bg-gray-50 text-gray-800 rounded-lg shadow-lg">
                <thead>
                    <tr class="bg-gray-300 text-left text-xs uppercase tracking-wide font-semibold text-gray-700">
                        <th class="py-4 px-6">Username</th>
                        <th class="py-4 px-6">Email</th>
                        <th class="py-4 px-6">Role</th>
                        <th class="py-4 px-6">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                    <tr class="bg-white hover:bg-gray-100 transition duration-300">
                        <td class="py-4 px-6"><?php echo htmlspecialchars($user['username']); ?></td>
                        <td class="py-4 px-6"><?php echo htmlspecialchars($user['email']); ?></td>
                        <td class="py-4 px-6"><?php echo htmlspecialchars($user['role_name']); ?></td>
                        <td class="py-4 px-6">
                            <a href="manage_users.php?edit_id=<?php echo $user['id']; ?>" class="text-blue-600 hover:text-blue-500 transition duration-300">Edit</a> |
                            <a href="manage_users.php?delete_id=<?php echo $user['id']; ?>" onclick="return confirm('Are you sure?');" class="text-red-600 hover:text-red-500 transition duration-300">Delete</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Add User Form -->
        <h3 class="text-3xl font-semibold text-center mt-12 text-gray-800">Add User</h3>
        <form action="manage_users.php" method="POST" class="grid grid-cols-1 gap-6 mt-8">
            <input type="text" name="username" placeholder="Username" required class="p-4 rounded-md bg-gray-200 text-gray-900 shadow-sm focus:ring focus:ring-blue-400">
            <input type="email" name="email" placeholder="Email" required class="p-4 rounded-md bg-gray-200 text-gray-900 shadow-sm focus:ring focus:ring-blue-400">
            <input type="password" name="password" placeholder="Password" required class="p-4 rounded-md bg-gray-200 text-gray-900 shadow-sm focus:ring focus:ring-blue-400">

            <select name="role_id" required class="p-4 rounded-md bg-gray-200 text-gray-900 shadow-sm focus:ring focus:ring-blue-400">
                <?php foreach ($roles as $role): ?>
                <option value="<?php echo $role['id']; ?>"><?php echo $role['role_name']; ?></option>
                <?php endforeach; ?>
            </select>

            <button type="submit" name="add_user" class="bg-gradient-to-r from-blue-400 to-purple-400 hover:from-blue-500 hover:to-purple-500 text-white font-semibold py-3 rounded-md shadow-md transition duration-300 focus:ring-4 focus:ring-blue-300">Add User</button>
        </form>

        <!-- Edit User Form (Only shown when edit_id is set in the URL) -->
        <?php if (isset($_GET['edit_id'])): ?>
        <?php
            $edit_id = $_GET['edit_id'];
            $stmt_edit = $pdo->prepare('SELECT * FROM users WHERE id = ?');
            $stmt_edit->execute([$edit_id]);
            $edit_user = $stmt_edit->fetch();
        ?>
        <h3 class="text-3xl font-semibold text-center mt-12 text-gray-800">Edit User</h3>
        <form action="manage_users.php" method="POST" class="grid grid-cols-1 gap-6 mt-8">
            <input type="hidden" name="user_id" value="<?php echo $edit_user['id']; ?>">
            <input type="text" name="username" value="<?php echo htmlspecialchars($edit_user['username']); ?>" required class="p-4 rounded-md bg-gray-200 text-gray-900 shadow-sm focus:ring focus:ring-blue-400">
            <input type="email" name="email" value="<?php echo htmlspecialchars($edit_user['email']); ?>" required class="p-4 rounded-md bg-gray-200 text-gray-900 shadow-sm focus:ring focus:ring-blue-400">

            <select name="role_id" required class="p-4 rounded-md bg-gray-200 text-gray-900 shadow-sm focus:ring focus:ring-blue-400">
                <?php foreach ($roles as $role): ?>
                <option value="<?php echo $role['id']; ?>" <?php if ($role['id'] == $edit_user['role_id']) echo 'selected'; ?>>
                    <?php echo $role['role_name']; ?>
                </option>
                <?php endforeach; ?>
            </select>

            <button type="submit" name="edit_user" class="bg-gradient-to-r from-green-400 to-teal-400 hover:from-green-500 hover:to-teal-500 text-white font-semibold py-3 rounded-md shadow-md transition duration-300 focus:ring-4 focus:ring-green-300">Update User</button>
        </form>
        <?php endif; ?>
    </div>
</body>

</html>

