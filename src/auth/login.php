<?php
session_start();
require_once '../db/connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $block_duration = 1; // in minutes
    $max_attempts = 10;

    $pdo = getDBConnection();

    // Fetch user from the database
    $stmt = $pdo->prepare('SELECT * FROM users WHERE email = ?');
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user) {
        // Debug password hash and entered password
        var_dump($user['password_hash']); // Show the password hash from the database
        var_dump($password); // Show the entered password

        $user_id = $user['id'];

        // Check failed login attempts
        $stmt = $pdo->prepare('SELECT * FROM failed_logins WHERE user_id = ?');
        $stmt->execute([$user_id]);
        $failed_login = $stmt->fetch();        

        // Verify the password
        if (password_verify($password, $user['password_hash'])) {
            // Successful login
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role_id'] = $user['role_id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['last_activity'] = time(); // Store the current time as last activity

            // Reset failed attempts on successful login
            $stmt = $pdo->prepare('DELETE FROM failed_logins WHERE user_id = ?');
            $stmt->execute([$user_id]);

            // Logging the login action
            $stmt = $pdo->prepare('INSERT INTO user_logs (user_id, action) VALUES (?, ?)');
            $stmt->execute([$user['id'], 'Login']);

            header('Location: ../../');
            exit();
        } else {
            // Failed login attempt
            if ($failed_login) {
                $failed_attempts++;
                $stmt = $pdo->prepare('UPDATE failed_logins SET failed_attempts = ?, last_failed_at = NOW() WHERE user_id = ?');
                $stmt->execute([$failed_attempts, $user_id]);
            } else {
                $stmt = $pdo->prepare('INSERT INTO failed_logins (user_id, failed_attempts, last_failed_at) VALUES (?, 1, NOW())');
                $stmt->execute([$user_id]);
            }

            if ($failed_attempts >= $max_attempts) {
                header("Location: ../../login.php?error=Account%20is%20blocked%20for%2015%20minutes%20due%20to%20multiple%20failed%20login%20attempts");
            } else {
                header('Location: ../../login.php?error=Invalid%20email%20or%20password');
            }
            exit();
        }
    } else {
        // If email is not found, show an error (to prevent user enumeration, show a generic message)
        header('Location: ../../login.php?error=Invalid%20email%20or%20password');
        exit();
    }
}

?>
