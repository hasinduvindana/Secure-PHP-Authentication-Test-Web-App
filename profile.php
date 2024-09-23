<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Light theme background */
        body {
            background: linear-gradient(to right, #f3f4f6, #ffffff);
            font-family: 'Poppins', sans-serif;
            height: 100vh;
            margin: 0;
        }

        /* Full-screen layout */
        .container {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        /* Custom input and button styles */
        input, button {
            transition: all 0.3s ease;
        }

        button:hover {
            transform: translateY(-2px);
        }

        /* Add smooth shadow for form */
        .form-container {
            box-shadow: 0 10px 15px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>

<body class="bg-gray-100 text-gray-800">
    <div class="container mx-auto p-10 bg-white rounded-lg shadow-lg max-w-md w-full form-container">
        <h1 class="text-3xl font-bold text-center mb-6 text-gray-800">User Profile</h1>

        <?php
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        require './src/db/connection.php';

        $pdo = getDBConnection();
        $message = '';
        $error = '';

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

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $new_username = trim($_POST['username']);
            $new_email = trim($_POST['email']);

            if (empty($new_username) || empty($new_email)) {
                $error = 'Username and email cannot be empty.';
            } else {
                $stmt = $pdo->prepare('UPDATE users SET username = ?, email = ? WHERE id = ?');
                $stmt->execute([$new_username, $new_email, $user_id]);
                $_SESSION['username'] = $new_username;
                $message = 'Profile updated successfully!';
            }
        }

        if (isset($_POST['deleteAccount'])) {
            try {
                $pdo->beginTransaction();
                $stmt = $pdo->prepare('DELETE FROM user_logs WHERE user_id = ?');
                $stmt->execute([$user_id]);

                $stmt = $pdo->prepare('DELETE FROM failed_logins WHERE user_id = ?');
                $stmt->execute([$user_id]);

                $stmt = $pdo->prepare('DELETE FROM users WHERE id = ?');
                $stmt->execute([$user_id]);

                $pdo->commit();
                session_destroy();
                header('Location: ./login.php');
                exit();
            } catch (Exception $e) {
                $pdo->rollBack();
                $error = 'Failed to delete the account: ' . $e->getMessage();
            }
        }
        ?>

        <!-- Profile Update Form -->
        <form action="./profile.php" method="POST" class="space-y-6">
            <div>
                <label for="username" class="block text-sm font-medium text-gray-600">Username</label>
                <input type="text" id="username" name="username" required
                       value="<?php echo htmlspecialchars($user['username'] ?? ''); ?>"
                       class="mt-2 w-full px-4 py-3 bg-gray-200 text-gray-800 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 placeholder-gray-400">
            </div>

            <div>
                <label for="email" class="block text-sm font-medium text-gray-600">Email</label>
                <input type="email" id="email" name="email" required
                       value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>"
                       class="mt-2 w-full px-4 py-3 bg-gray-200 text-gray-800 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 placeholder-gray-400">
            </div>

            <div class="flex justify-between">
                <button type="submit" class="w-full py-3 bg-blue-500 hover:bg-blue-600 text-white font-semibold rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition duration-300">
                    Update Profile
                </button>
            </div>
        </form>

        <!-- Account Deletion Form -->
        <form action="./profile.php" method="POST" class="mt-6">
            <input type="hidden" name="deleteAccount" value="1">
            <button type="submit" class="w-full py-3 bg-red-500 hover:bg-red-600 text-white font-semibold rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 transition duration-300">
                Delete Account
            </button>
        </form>

        <div class="mt-6 text-center">
            <a href="./my_dashboard.php" class="text-gray-500 hover:text-blue-500 transition duration-200">Back to Dashboard</a>
        </div>
    </div>
</body>

</html>
