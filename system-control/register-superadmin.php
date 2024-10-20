<?php
include '../assets/database-functions/dbconnection.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);


if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user = $_POST['username'];
    $pass = $_POST['password'];
    $confirm_pass = $_POST['confirmPassword'];

    if ($pass !== $confirm_pass) {
        die("Passwords do not match!");
    }

    $hashed_password = password_hash($pass, PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO super_admin (username, password) VALUES (?, ?)");
    if (!$stmt) {
        die("Preparation failed: " . $conn->error);
    }
    
    $stmt->bind_param("ss", $user, $hashed_password);

    if ($stmt->execute()) {
        echo "User registered successfully!";
    } else {
        die("Execution failed: " . $stmt->error);
    }

    $stmt->close();
    $conn->close();
} else {
    die("Invalid request method.");
}
?>

