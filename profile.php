<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Custom background gradient */
        body {
            background: linear-gradient(to right, #0f0f0f, #3B82F6);
            font-family: 'Poppins', sans-serif;
        }
    </style>
</head>
<body class="flex items-center justify-center min-h-screen bg-[radial-gradient(ellipse_at_top_right,_var(--tw-gradient-stops))] from-black via-gray-900 to-gray-700 text-white">
    <div class="bg-gray-800 backdrop-blur-md p-8 rounded-xl shadow-2xl max-w-md w-full">
        <h1 class="text-3xl font-bold text-center text-yellow-400 mb-6">User Profile</h1>

        <?php
// Check if the session is not already started before starting one
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require './src/db/connection.php';

// Get database connection
$pdo = getDBConnection();

// Initialize variables
$message = '';
$error = '';

// Fetch user data
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $stmt = $pdo->prepare('SELECT username, email FROM users WHERE id = ?');
    $stmt->execute([$user_id]);
    $user = $stmt->fetch();

    if (!$user) {
        $error = 'User not found.';
    }
} else {
    $error = 'You are not logged in.';
}

// Handle form submission for updating the profile
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_username = trim($_POST['username']);
    $new_email = trim($_POST['email']);

    if (empty($new_username) || empty($new_email)) {
        $error = 'Username and email cannot be empty.';
    } else {
        // Update user data
        $stmt = $pdo->prepare('UPDATE users SET username = ?, email = ? WHERE id = ?');
        $stmt->execute([$new_username, $new_email, $user_id]);

        $_SESSION['username'] = $new_username;
        $message = 'Profile updated successfully!';
    }
}

// Handle account deletion
if (isset($_POST['deleteAccount'])) {
    try {
        // Begin a transaction
        $pdo->beginTransaction();

        // Delete from user_logs table
        $stmt = $pdo->prepare('DELETE FROM user_logs WHERE user_id = ?');
        $stmt->execute([$user_id]);

        // Delete from failed_logins table
        $stmt = $pdo->prepare('DELETE FROM failed_logins WHERE user_id = ?');
        $stmt->execute([$user_id]);

        // Delete the user from the users table
        $stmt = $pdo->prepare('DELETE FROM users WHERE id = ?');
        $stmt->execute([$user_id]);

        // Commit the transaction
        $pdo->commit();

        // Destroy session and redirect
        session_destroy();
        header('Location: ./login.php');
        exit();
    } catch (Exception $e) {
        // Roll back the transaction in case of error
        $pdo->rollBack();
        $error = 'Failed to delete the account: ' . $e->getMessage();
    }
}
?>



        <!-- Profile Update Form -->
        <form action="./profile.php" method="POST" class="space-y-6">
            <div>
                <label for="username" class="block text-sm font-medium text-gray-300">Username</label>
                <input type="text" id="username" name="username" required
                       value="<?php echo htmlspecialchars($user['username'] ?? ''); ?>"
                       class="mt-2 w-full px-4 py-3 bg-gray-700 text-white border border-gray-600 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-yellow-400 focus:border-yellow-400 transition duration-200 placeholder-gray-400">
            </div>

            <div>
                <label for="email" class="block text-sm font-medium text-gray-300">Email</label>
                <input type="email" id="email" name="email" required
                       value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>"
                       class="mt-2 w-full px-4 py-3 bg-gray-700 text-white border border-gray-600 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-yellow-400 focus:border-yellow-400 transition duration-200 placeholder-gray-400">
            </div>

            <div class="flex justify-between">
                <button type="submit" class="w-full py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition duration-300">
                    Update Profile
                </button>
            </div>
        </form>

        <!-- Account Deletion Form -->
        <form action="./profile.php" method="POST" class="mt-6">
            <input type="hidden" name="deleteAccount" value="1">
            <button type="submit" class="w-full py-3 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 transition duration-300">
                Delete Account
            </button>
        </form>

        <div class="mt-6 text-center">
            <a href="./my_dashboard.php" class="text-gray-400 hover:text-yellow-400 transition duration-200">Back to Dashboard</a>
        </div>
    </div>
</body>
</html>
