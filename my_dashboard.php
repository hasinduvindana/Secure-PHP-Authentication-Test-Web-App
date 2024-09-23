<?php
session_start();

// Set the session timeout duration (10 minutes)
$session_timeout = 10 * 60; // 10 minutes in seconds

// Check if the session has expired
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > $session_timeout) {
    // Logging the logout action before destroying session
    require_once '../db/connection.php';
    $pdo = getDBConnection();
    $stmt = $pdo->prepare('INSERT INTO user_logs (user_id, action) VALUES (?, ?)');
    $stmt->execute([$_SESSION['user_id'], 'Logout due to inactivity']);

    // Session expired, destroy it and redirect to login page
    session_unset();
    session_destroy();
    header('Location: ../../login.php?error=Session%20expired%20due%20to%20inactivity');
    exit();
}

// Update last activity time
$_SESSION['last_activity'] = time();

// Check if the user is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] !== 2) {
    header('Location: ../../login.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            background: url('assets/imgbg2.png') no-repeat center center fixed;
            background-size: cover;
            font-family: 'Poppins', sans-serif;
        }
    </style>
</head>

<body class="flex items-center justify-center min-h-screen text-white">
    <div class="container mx-auto px-6 py-12 flex flex-col items-center justify-center">
        <div class="bg-white shadow-lg rounded-xl p-10 text-gray-900 w-full max-w-lg relative transform -translate-y-12">
            <div class="absolute -top-12 left-1/2 transform -translate-x-1/2 bg-gradient-to-r from-purple-600 to-pink-500 rounded-full p-4">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-12 h-12 text-white">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
            </div>

            <h1 class="text-3xl font-bold text-center mb-6 text-gray-800">Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>
            <p class="text-center text-gray-500 mb-8">This is your dashboard. Manage your account and settings here.</p>

            <div class="space-y-6">
                <a href="./profile.php" class="block w-full py-4 bg-indigo-600 text-white font-semibold rounded-lg text-center hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition duration-300">
                    <div class="flex items-center justify-center space-x-2">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-6 h-6">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 13.318a4 4 0 015.658 0l5.121-5.122a4 4 0 115.656 5.657l-5.122 5.121a4 4 0 01-5.657 0l-5.121-5.122a4 4 0 010-5.657z" />
                        </svg>
                        <span>View Profile</span>
                    </div>
                </a>

                <a href="./settings.php" class="block w-full py-4 bg-purple-600 text-white font-semibold rounded-lg text-center hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-purple-500 transition duration-300">
                    <div class="flex items-center justify-center space-x-2">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-6 h-6">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.518a2 2 0 10-2.848 2.847 9.959 9.959 0 01-5.66 1.66C5.358 20.024 2 16.66 2 12.015S5.358 4 9.92 4a9.96 9.96 0 015.66 1.66 2 2 0 102.847 2.848 9.96 9.96 0 011.661 5.66 9.96 9.96 0 01-1.66 5.66z" />
                        </svg>
                        <span>Account Settings</span>
                    </div>
                </a>

                <a href="./src/auth/logout.php" class="block w-full py-4 bg-red-600 text-white font-semibold rounded-lg text-center hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 transition duration-300">
                    <div class="flex items-center justify-center space-x-2">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-6 h-6">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12H3m12 0l-4 4m4-4l-4-4m12 4v-4a2 2 0 00-2-2h-3.586a1 1 0 01-.707-.293l-3.414-3.414A1 1 0 0011.586 3H9.414a1 1 0 00-.707.293L5.293 6.414A1 1 0 005 7.414V9.586a1 1 0 01-.293.707l-3.414 3.414A1 1 0 001 15v6a2 2 0 002 2h16a2 2 0 002-2v-6a2 2 0 00-2-2h-4z" />
                        </svg>
                        <span>Logout</span>
                    </div>
                </a>
            </div>
        </div>
    </div>
</body>

</html>

