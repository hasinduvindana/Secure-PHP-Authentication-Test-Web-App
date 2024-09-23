<?php
require_once '../db/connection.php';

function logAction($user_id, $action) {
    $pdo = getDBConnection();
    $stmt = $pdo->prepare('INSERT INTO user_logs (user_id, action) VALUES (?, ?)');
    $stmt->execute([$user_id, $action]);
}
?>
