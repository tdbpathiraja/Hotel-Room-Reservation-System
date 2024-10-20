<?php
include '../dbconnection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullName = $_POST['fullName'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirmPassword'];
    $birthDate = $_POST['birthDate'];
    $gender = $_POST['gender'];
    $profileImage = $_FILES['profileImage'];

    if (empty($fullName) || empty($username) || empty($email) || empty($password) || empty($confirmPassword) || empty($birthDate) || empty($gender)) {
        echo '<p class="error">Please fill in all fields.</p>';
        exit();
    }

    if ($password !== $confirmPassword) {
        echo '<p class="error">Passwords do not match.</p>';
        exit();
    }

    $stmt = $conn->prepare('SELECT id FROM users WHERE username = ? OR email = ?');
    $stmt->bind_param('ss', $username, $email);
    $stmt->execute();
    if ($stmt->get_result()->fetch_assoc()) {
        echo '<p class="error">Username or email already exists.</p>';
        exit();
    }

    $birthDateObj = new DateTime($birthDate);
    $today = new DateTime();
    $age = $today->diff($birthDateObj)->y;

    if ($age < 18) {
        echo '<p class="error">You must be at least 18 years old to register.</p>';
        exit();
    }

    $imageFileName = '';
    if ($profileImage['error'] === UPLOAD_ERR_OK) {
        $imageFileName = basename($profileImage['name']);
        $targetPath = '../../img/visitor-profiles/' . $imageFileName;
        move_uploaded_file($profileImage['tmp_name'], $targetPath);
    }

    $passwordHash = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $conn->prepare('INSERT INTO users (full_name, username, email, password_hash, birth_date, gender, profile_image) VALUES (?, ?, ?, ?, ?, ?, ?)');
    $result = $stmt->bind_param('sssssss', $fullName, $username, $email, $passwordHash, $birthDate, $gender, $imageFileName) && $stmt->execute();

    if ($result) {
        echo '<p class="success">Signup successful! You can now <a href="javascript:void(0);" onclick="showLogin()">Login</a>.</p>';
    } else {
        echo '<p class="error">An error occurred. Please try again.</p>';
    }
}
?>
