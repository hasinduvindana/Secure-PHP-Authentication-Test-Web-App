<?php
session_start();

function checkAuthorization($required_role) {
    if (!isset($_SESSION['role_id']) || $_SESSION['role_id'] !== $required_role) {
        header('Location: /public/login.php');
        exit();
    }
}
?>
