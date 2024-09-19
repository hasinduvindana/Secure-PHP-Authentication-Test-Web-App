<?php
require './src/db/connection.php';
session_start();

// Check if the user is logged in and has the correct role (admin)
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] !== 1) {
    // If the user is not an admin, redirect to an error page or homepage
    header('Location: ./error.php');
    exit();
}

$pdo = getDBConnection();

// Fetch all logs
$stmt = $pdo->query('SELECT user_logs.id, users.username, user_logs.action, user_logs.timestamp 
                     FROM user_logs
                     JOIN users ON user_logs.user_id = users.id
                     ORDER BY user_logs.timestamp DESC');
$logs = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Logs</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            background: linear-gradient(to right, #0f0f0f, #3B82F6);
            font-family: 'Poppins', sans-serif;
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center bg-gradient-to-br from-gray-900 via-black to-gray-800">
    <div class="container mx-auto p-8 bg-gray-800 rounded-xl shadow-lg">
        <h2 class="text-4xl text-center text-white font-semibold mb-8">System Logs</h2>

        <div class="overflow-x-auto">
            <table class="min-w-full bg-gray-900 text-white rounded-lg shadow-lg">
                <thead>
                    <tr class="bg-blue-600 text-left text-sm uppercase font-medium">
                        <th class="py-3 px-6">Username</th>
                        <th class="py-3 px-6">Action</th>
                        <th class="py-3 px-6">Timestamp</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($logs as $log): ?>
                        <tr class="border-b border-gray-700 hover:bg-gray-700">
                            <td class="py-4 px-6"><?php echo htmlspecialchars($log['username']); ?></td>
                            <td class="py-4 px-6"><?php echo htmlspecialchars($log['action']); ?></td>
                            <td class="py-4 px-6"><?php echo htmlspecialchars($log['timestamp']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="mt-6 text-center">
            <a href="./dashboard.php" class="text-blue-400 hover:text-blue-300 transition duration-200">Back to Admin Dashboard</a>
        </div>
    </div>
</body>
</html>
