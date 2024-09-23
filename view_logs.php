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
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>System Logs</title>
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
        <h2 class="text-5xl font-extrabold text-center mb-10 text-gray-800">System Logs</h2>

        <!-- Logs Table -->
        <div class="overflow-x-auto">
            <table class="min-w-full bg-gray-50 text-gray-800 rounded-lg shadow-lg">
                <thead>
                    <tr class="bg-gray-300 text-left text-xs uppercase tracking-wide font-semibold text-gray-700">
                        <th class="py-4 px-6">Username</th>
                        <th class="py-4 px-6">Action</th>
                        <th class="py-4 px-6">Timestamp</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($logs as $log): ?>
                    <tr class="bg-white hover:bg-gray-100 transition duration-300">
                        <td class="py-4 px-6"><?php echo htmlspecialchars($log['username']); ?></td>
                        <td class="py-4 px-6"><?php echo htmlspecialchars($log['action']); ?></td>
                        <td class="py-4 px-6"><?php echo htmlspecialchars($log['timestamp']); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Back to Dashboard Link -->
        <div class="mt-6 text-center">
            <a href="./dashboard.php" class="text-blue-600 hover:text-blue-500 transition duration-200">Back to Admin Dashboard</a>
        </div>
    </div>
</body>

</html>
