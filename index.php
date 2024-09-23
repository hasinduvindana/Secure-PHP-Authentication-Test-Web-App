<?php
session_start(); // Start the session to track user login status

// Check if the user is logged in
$isLoggedIn = isset($_SESSION['user_id']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PHP Authentication</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .mynav {
            border-radius: 10px;
            box-shadow: 0px 20px 60px rgba(0, 0, 255, 0.9); /* Increased shadow for mynav */
            background: rgba(0, 0, 0, 0.7); /* Semi-transparent background color */
            color: white;
            padding: 10px;
            width: 100%;
        }

        .container {
            margin-top: 50px;
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1); /* Increased shadow for container */
            padding: 30px;
            background-color: rgba(200, 200, 255, 0.3);
            text-align: center;
        }

        .bg-image {
            background-size: cover;
            background-position: center;
            height: 100vh;
            transition: background-image 2s ease-in-out;
        }

        h1, p {
            font-family: 'Arial', sans-serif;
            font-weight: bold;
            color: white; /* Text color similar to the example */
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5); /* Adding text shadow for better visibility */
        }

        .btn-primary {
            background-color: #4b5d67; /* Button background color similar to the example */
            border: none; /* Remove border */
            color: white; /* Button text color */
            padding: 10px 20px; /* Button padding */
            font-size: 18px; /* Button font size */
            border-radius: 25px; /* Rounded corners */
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2); /* Button shadow */
            transition: background-color 0.3s ease; /* Smooth transition for background color */
        }

        .btn-primary:hover {
            background-color: #2c3e50; /* Darker background color on hover */
        }
    </style>
</head>
<body>
    <div class="bg-image" id="bgImage">
        <div class="mynav shadow-sm" style="background:transparent; color:white;text:bold; padding:10px;width:100%;">
            <h1 class="text-center">PHP Authentication  -  BY Hasindu</h1>
        </div>

        <div class="container mt-5" style="padding-top:80px;">
            <?php if ($isLoggedIn): ?>
                <!-- Content for logged-in users -->
                <h1>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>
                <p>You are now logged in.</p>
                <div class="mt-4">
                    <a href="./src/auth/logout.php" class="btn btn-primary">Logout</a>
                    
                    <?php if ($_SESSION['role_id'] == 1): ?>
                        <!-- Admin dashboard -->
                        <a href="./dashboard.php" class="btn btn-primary">Go to Admin Dashboard</a>
                    <?php else: ?>
                        <!-- User dashboard -->
                        <a href="./my_dashboard.php" class="btn btn-primary">Go to User Dashboard</a>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <!-- Content for guests -->
                <h1>Welcome to MY PHP Authentication Test Site</h1>
                <p><i>I developed this for Software Security Module</i></p>
                <p>of my Higher National Diploma...</p><br>
                <div>
                    <a href="register.php" class="btn btn-primary">Sign Up</a>
                    <p>If you already have an account, please <a href="login.php">Sign In</a>.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
        const images = ["assets/img1.jpg", "assets/img2.jpg", "assets/img3.jpg"]; // Update image paths
        let currentIndex = 0;

        function changeBackground() {
            document.getElementById('bgImage').style.backgroundImage = `url('${images[currentIndex]}')`;
            currentIndex = (currentIndex + 1) % images.length;
            setTimeout(changeBackground, 4000); // Change every 4 seconds
        }

        changeBackground();
    </script>
</body>
</html>
