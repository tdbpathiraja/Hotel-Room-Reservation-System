<?php

include '../dbconnection.php';
require '../../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $booking_id = $_POST['booking_id'];

    
    if (!isset($booking_id) || empty($booking_id)) {
        echo 'fail';
        exit;
    }

    
    $stmt = $conn->prepare('SELECT name, email FROM booking_details WHERE booking_id = ?');
    $stmt->bind_param('s', $booking_id);
    $stmt->execute();
    $stmt->bind_result($name, $email);
    $stmt->fetch();
    $stmt->close();

    
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
        $mail->addAddress($email, $name);

        $mail->isHTML(true);
        $mail->Subject = 'Booking Cancellation Notice';
        $mail->Body = "
        <!DOCTYPE html>
        <html lang='en'>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <style>
                body { font-family: 'Georgia', serif; color: #333; line-height: 1.6; }
                .container { width: 90%; max-width: 600px; margin: 30px auto; background-color: #fff; padding: 20px; border-radius: 10px; box-shadow: 0 0 15px rgba(0, 0, 0, 0.1); border-top: 5px solid #d9534f; border-bottom: 5px solid #d9534f; }
                .header, .footer { text-align: center; color: #d9534f; font-size: 24px; font-weight: bold; margin-bottom: 20px; }
                .footer { margin-top: 30px; font-size: 14px; }
                h1 { font-size: 28px; margin-bottom: 20px; text-align: center; }
                p { font-size: 16px; margin: 10px 0; }
                .notice { background-color: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin-top: 20px; }
                .policy-link { color: #d9534f; text-decoration: underline; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>Varsity Lodge</div>
                <h1>Booking Cancellation</h1>
                <p>Dear $name,</p>
                <p>We regret to inform you that your booking with Booking ID: <strong>$booking_id</strong> has been canceled.</p>
                <p class='notice'>Please note that we do not offer any refunds for cancellations. For more details, please review our <a href='https://yourwebsite.com/refund-policy' class='policy-link'>refund policy</a>.</p>
                <p>If you have any questions, feel free to contact our support team.</p>
                <div class='footer'>Thank you for choosing Varsity Lodge</div>
            </div>
        </body>
        </html>
        ";
        $mail->AltBody = "Dear $name, Your booking with Booking ID: $booking_id has been canceled. Please note that we do not offer any refunds for cancellations. For more details, please review our refund policy at https://yourwebsite.com/refund-policy.";

        $mail->send();
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => "Failed to send email. Mailer Error: {$mail->ErrorInfo}"]);
        exit();
    }

    
    $stmt = $conn->prepare('DELETE FROM booking_details WHERE booking_id = ?');
    $stmt->bind_param('s', $booking_id);

    if ($stmt->execute()) {
        echo 'success';
    } else {
        echo 'fail';
    }

    $stmt->close();
    $conn->close();
}
?>

