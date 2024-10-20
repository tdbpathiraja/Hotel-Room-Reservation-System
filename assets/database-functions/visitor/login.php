<?php
include '../dbconnection.php';
require '../../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $rememberMe = isset($_POST['rememberMe']); 

   
    if (empty($username) || empty($password)) {
        echo json_encode(['status' => 'error', 'message' => 'Please fill in all fields.']);
        exit();
    }

   
    $username = filter_var($username, FILTER_SANITIZE_STRING);

    
    $stmt = $conn->prepare('SELECT email, password_hash FROM users WHERE username = ?');
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $stmt->bind_result($email, $password_hash);
    $stmt->fetch();

    if ($password_hash && password_verify($password, $password_hash)) {
        session_start();
        $_SESSION['loggedin'] = true;
        $_SESSION['username'] = $username;

        
        $cookieExpiration = $rememberMe ? time() + (30 * 24 * 60 * 60) : time() + (24 * 60 * 60); 

        // Set cookies
        setcookie('username', $username, $cookieExpiration, "/");

        
        $loginDate = date('Y-m-d'); 

        
        sendLoginEmail($email, $username, $loginDate);

        echo json_encode([
            'status' => 'success',
            'message' => 'Login successful! Redirecting...'
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Invalid username or password.'
        ]);
    }

    $stmt->close();
}

function sendLoginEmail($email, $username, $loginDate) {
    $mail = new PHPMailer(true);
    try {
        
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'varsitylodgelk@gmail.com';
        $mail->Password = 'bpqe tvas hgnw smlw';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        
        $mail->setFrom('hello@varsitylodge.com', 'Varsity Lodge');
        $mail->addAddress($email, $username);

        
        $mail->isHTML(true);
        $mail->Subject = 'Login Notification - Varsity Lodge';
        $mail->Body = "
        <!DOCTYPE html>
        <html lang='en'>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <style>
                body { font-family: 'Arial', sans-serif; background-color: #f4f4f4; color: #333; margin: 0; padding: 0; }
                .container { width: 90%; max-width: 600px; margin: 40px auto; background-color: #ffffff; padding: 30px; border-radius: 10px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); }
                .header { text-align: center; background-color: #3a3a3a; color: #ffffff; padding: 15px 0; border-radius: 10px 10px 0 0; }
                .header h2 { margin: 0; font-size: 28px; }
                .content { padding: 20px; text-align: center; }
                .content h1 { font-size: 22px; margin-bottom: 10px; color: #3a3a3a; }
                .content p { font-size: 16px; line-height: 1.6; margin: 0 0 20px; }
                .content .date { font-size: 18px; font-weight: bold; color: #d4af37; margin-bottom: 20px; }
                .button { display: inline-block; padding: 12px 25px; background-color: #d4af37; color: #ffffff; border-radius: 5px; text-decoration: none; font-size: 16px; margin-top: 20px; }
                .button:hover { background-color: #c79832; }
                .footer { text-align: center; margin-top: 30px; font-size: 12px; color: #777; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h2>Login Notification</h2>
                </div>
                <div class='content'>
                    <h1>Hello, $username</h1>
                    <p>We are happy to inform you that your account was accessed successfully.</p>
                    <p class='date'>Login Date: $loginDate</p>
                    <p>If this wasn't you, please secure your account immediately by changing your password.</p>
                    <a href='https://varsitylodge.lk/reset-password.php' class='button'>Secure Your Account</a>
                </div>
                <div class='footer'>
                    <p>&copy; " . date('Y') . " Varsity Lodge. All rights reserved.</p>
                </div>
            </div>
        </body>
        </html>
        ";
        $mail->AltBody = "Hello, $username. We are happy to inform you that your account was accessed successfully on $loginDate. If this wasn't you, please secure your account immediately.";

        $mail->send();
    } catch (Exception $e) {
        
    }
}
?>
