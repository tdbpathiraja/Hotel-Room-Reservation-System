<?php
session_start();


if (!isset($_SESSION['username']) && !isset($_COOKIE['username'])) {
    header('Location: ../../../visitor-login.php');
    exit();
}

include '../dbconnection.php';


$username = $_SESSION['username'] ?? $_COOKIE['username'];

$stmt = $conn->prepare('SELECT profile_image FROM users WHERE username = ?');
$stmt->bind_param('s', $username);
$stmt->execute();
$stmt->bind_result($profile_image);
$stmt->fetch();
$stmt->close();

$profileImagePath = '../../img/visitor-profiles/';

$conn->begin_transaction();

try {
    
    $stmt = $conn->prepare('DELETE FROM booking_details WHERE username = ?');
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $stmt->close();

    
    $stmt = $conn->prepare('DELETE FROM users WHERE username = ?');
    $stmt->bind_param('s', $username);
    $result = $stmt->execute();
    $stmt->close();

    if ($result) {
        
        if (!empty($profile_image) && file_exists($profileImagePath . $profile_image)) {
            unlink($profileImagePath . $profile_image);
        }

        
        setcookie('username', '', time() - 3600, '/');
        setcookie('password', '', time() - 3600, '/');
        unset($_SESSION['username']);

        $conn->commit();

        header('Location: ../../../visitor-login.php');
        exit();
    } else {
        throw new Exception('An error occurred while deleting the user record.');
    }
} catch (Exception $e) {
    $conn->rollback();
    echo '<p class="error">An error occurred while deleting your account. Please try again later.</p>';
}
?>
