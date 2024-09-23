<?php
require_once __DIR__ . '/../../src/db/connection.php';



if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    
    // Set the role_id based on the username condition
    if (preg_match('/^admin.*@%$/', $username)) {
        $role_id = 1; // Admin role
    } else {
        $role_id = 2; // Default role: user
    }

    // Check if the email or username already exists
    $pdo = getDBConnection();
    $stmt = $pdo->prepare('SELECT * FROM users WHERE email = ? OR username = ?');
    $stmt->execute([$email, $username]);
    $existingUser = $stmt->fetch();

    if ($existingUser) {
        // If the username or email exists, redirect back to the registration page with an error message
        if ($existingUser['email'] === $email) {
            header('Location: ../../register.php?error=This%20email%20is%20already%20used');
        } elseif ($existingUser['username'] === $username) {
            header('Location: ../../register.php?error=This%20username%20is%20already%20used');
        }
        exit();
    }

    // Check if the password is secure (minimum 8 characters, at least one number and one letter)
    if (!preg_match('/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{8,}$/', $password)) {
        header('Location: ../../register.php?error=Password%20must%20be%20at%20least%208%20characters%20long%20and%20include%20both%20letters%20and%20numbers');
        exit();
    }

    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Insert user into the database
    $stmt = $pdo->prepare('INSERT INTO users (username, email, password_hash, role_id) VALUES (?, ?, ?, ?)');
    $stmt->execute([$username, $email, $hashed_password, $role_id]);

    header('Location: ../../login.php');
    exit();
}
?>
