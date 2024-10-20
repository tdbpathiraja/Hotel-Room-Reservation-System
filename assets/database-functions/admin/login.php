<?php
session_start();
include '../dbconnection.php';

$response = array();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $conn->real_escape_string($_POST['username']);
    $password = $conn->real_escape_string($_POST['password']);
    
    
    $sql = "SELECT * FROM super_admin WHERE username='$username'";
    $result = $conn->query($sql);
    
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        
        
        if ($user['lockout_time'] && new DateTime() < new DateTime($user['lockout_time'])) {
            $remainingTime = (new DateTime($user['lockout_time']))->getTimestamp() - (new DateTime())->getTimestamp();
            $minutesLeft = ceil($remainingTime / 60);
            $response['message'] = "Your account is locked. Try again in $minutesLeft minutes.";
            $response['status'] = 'locked';
        } else {
            
            if (password_verify($password, $user['password'])) {
                
                $conn->query("UPDATE super_admin SET failed_attempts = 0, lockout_time = NULL WHERE username='$username'");
                $_SESSION['loggedin'] = true;
                $_SESSION['username'] = $username;

                
                $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";

                
                $host = $_SERVER['HTTP_HOST'];

                
                $basePath = rtrim(dirname($_SERVER['PHP_SELF']), '/') . '/';

                
                $redirectUrl = $protocol . $host . $basePath . '../../../admin/index.php';

                $response['message'] = "Login successful.";
                $response['status'] = 'success';
                $response['redirect'] = $redirectUrl;
            } else {
                
                $failedAttempts = $user['failed_attempts'] + 1;

                
                if ($failedAttempts >= 5) {
                    $lockoutTime = (new DateTime())->modify('+10 minutes')->format('Y-m-d H:i:s');
                    $conn->query("UPDATE super_admin SET failed_attempts = $failedAttempts, lockout_time = '$lockoutTime' WHERE username='$username'");
                    $response['message'] = "Too many failed attempts. Your account is locked for 10 minutes.";
                    $response['status'] = 'locked';
                } else {
                    
                    $conn->query("UPDATE super_admin SET failed_attempts = $failedAttempts WHERE username='$username'");
                    $attemptsLeft = 5 - $failedAttempts;
                    $response['message'] = "Invalid username or password. You have $attemptsLeft attempts left.";
                    $response['status'] = 'error';
                }
            }
        }
    } else {
        
        $response['message'] = "Invalid username.";
        $response['status'] = 'error';
    }

    
    $conn->close();

    echo json_encode($response);
}
?>