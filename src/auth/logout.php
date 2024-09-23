<?php
session_start();

// Logging the logout action
require_once '../db/connection.php';
$pdo = getDBConnection();

$stmt = $pdo->prepare('INSERT INTO user_logs (user_id, action) VALUES (?, ?)');
$stmt->execute([$_SESSION['user_id'], 'Logout']);

session_destroy();

header('Location: ../../login.php');
?>
