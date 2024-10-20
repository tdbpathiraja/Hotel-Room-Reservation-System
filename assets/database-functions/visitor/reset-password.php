<?php

include '../dbconnection.php';
session_start(); 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $username = $_SESSION['username'] ?? $_COOKIE['username'];
    $new_password = $_POST['new-password'];

    
    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

    $stmt = $conn->prepare('UPDATE users SET password_hash = ? WHERE username = ?');
    $stmt->bind_param('ss', $hashed_password, $username);
    $stmt->execute();
    $stmt->close();

    echo "Password has been reset successfully.";
    header('Location: ../../../my-account.php');
    exit();
}
?>
