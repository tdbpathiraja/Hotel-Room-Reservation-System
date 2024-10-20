<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include '../assets/database-functions/dbconnection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $pass = $_POST['safePassword'];
    $confirm_pass = $_POST['safeConfirmPassword'];

    
    if ($pass !== $confirm_pass) {
        die("Passwords do not match!");
    }

    
    $hashed_password = password_hash($pass, PASSWORD_DEFAULT);

    
    $stmt = $conn->prepare("INSERT INTO safe_password (password_hash) VALUES (?)");
    if (!$stmt) {
        die("Preparation failed: " . $conn->error);
    }
    
    $stmt->bind_param("s", $hashed_password);

    
    if ($stmt->execute()) {
        echo "Safe password stored successfully!";
    } else {
        die("Execution failed: " . $stmt->error);
    }

    
    $stmt->close();
    $conn->close();
} else {
    die("Invalid request method.");
}
?>
