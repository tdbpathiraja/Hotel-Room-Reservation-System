<?php
include '../dbconnection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $currentUsername = trim($_POST['current-username']);
    $username = trim($_POST['username']);
    $full_name = trim($_POST['full-name']);
    $email = trim($_POST['email']);

    if (empty($username) || empty($full_name) || empty($email)) {
        echo 'error: Please fill in all fields.';
        exit();
    }

    $stmt = $conn->prepare('SELECT username FROM users WHERE username = ? AND username != ?');
    $stmt->bind_param('ss', $username, $currentUsername);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        echo 'error: Username already exists.';
        $stmt->close();
        exit();
    }
    $stmt->close();

    $stmt = $conn->prepare('SELECT email FROM users WHERE email = ? AND username != ?');
    $stmt->bind_param('ss', $email, $currentUsername);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        echo 'error: Email already exists.';
        $stmt->close();
        exit();
    }
    $stmt->close();

    $stmt = $conn->prepare('UPDATE users SET username = ?, full_name = ?, email = ? WHERE username = ?');
    $stmt->bind_param('ssss', $username, $full_name, $email, $currentUsername);
    $stmt->execute();
    $stmt->close();

    $stmt = $conn->prepare('UPDATE booking_details SET username = ?, name = ?, email = ? WHERE username = ?');
    $stmt->bind_param('ssss', $username, $full_name, $email, $currentUsername);
    $stmt->execute();
    $stmt->close();

    echo 'success: Profile updated.';
}
?>
