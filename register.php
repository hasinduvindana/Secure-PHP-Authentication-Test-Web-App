<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Secure PHP Authentication | Register</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
      /* Use the uploaded image as the background */
      body {
        background: url('assets/imgbg3.png') no-repeat center center fixed;
        background-size: cover;
        font-family: 'Poppins', sans-serif;
      }

      /* Apply backdrop filter to the form */
      .backdrop-blur-md {
        backdrop-filter: blur(10px);
      }
    </style>
</head>
<body class="flex items-center justify-center min-h-screen text-white">
    <div class="bg-black bg-opacity-60 backdrop-blur-md p-8 rounded-xl shadow-2xl max-w-md w-full">
        <h1 class="text-3xl font-bold text-center text-white mb-6">Register</h1>

        <?php if (isset($_GET['error'])): ?>
            <div class="bg-red-500 text-white p-3 rounded-lg mb-6">
                <?php echo htmlspecialchars($_GET['error']); ?>
            </div>
        <?php endif; ?>

        <form action="./src/auth/register.php" method="POST" class="space-y-6">
            <div>
                <label for="username" class="block text-sm font-medium text-gray-300">Username</label>
                <input type="text" id="username" name="username" required class="mt-2 w-full px-4 py-3 bg-gray-700 text-white border border-gray-600 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 placeholder-gray-400" placeholder="Enter your username">
            </div>

            <div>
                <label for="email" class="block text-sm font-medium text-gray-300">Email</label>
                <input type="email" id="email" name="email" required class="mt-2 w-full px-4 py-3 bg-gray-700 text-white border border-gray-600 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 placeholder-gray-400" placeholder="Enter your email">
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-gray-300">Password</label>
                <input type="password" id="password" name="password" required class="mt-2 w-full px-4 py-3 bg-gray-700 text-white border border-gray-600 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 placeholder-gray-400" placeholder="Enter your password">
            </div>

            <div>
                <input type="submit" value="Register" class="w-full py-3 bg-yellow-700 text-white font-semibold rounded-lg hover:bg-yellow-800 focus:outline-none focus:ring-2 focus:ring-yellow-500 cursor-pointer transition duration-300">
            </div>
        </form>
        <p class="text-sm text-center text-gray-400 mt-6">Already have an account? <a href="./login.php" class="text-blue-400 hover:text-blue-500 transition duration-200">Login here</a></p>
    </div>
</body>
</html>
