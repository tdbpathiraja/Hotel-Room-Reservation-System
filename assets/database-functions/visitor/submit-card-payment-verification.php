<?php

session_start();
include '../dbconnection.php';
require '../../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

header('Content-Type: application/json');

// Function to send payment confirmation email to the user
function sendPaymentConfirmationEmail($name, $email, $bookingId) {
    $mail = new PHPMailer(true);
    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'varsitylodgelk@gmail.com';
        $mail->Password = 'bpqe tvas hgnw smlw';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Recipients
        $mail->setFrom('hello@varsitylodge.com', 'Varsity Lodge');
        $mail->addAddress($email, $name);

        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Card Payment Verification Received';
        $mail->Body = "
        <!DOCTYPE html>
        <html lang='en'>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <style>
                body { font-family: 'Georgia', serif; color: #333; line-height: 1.6; }
                .container { width: 90%; max-width: 600px; margin: 30px auto; background-color: #fff; padding: 20px; border-radius: 10px; box-shadow: 0 0 15px rgba(0, 0, 0, 0.1); border-top: 5px solid #d4af37; border-bottom: 5px solid #d4af37; }
                .header, .footer { text-align: center; color: #d4af37; font-size: 24px; font-weight: bold; margin-bottom: 20px; }
                .footer { margin-top: 30px; font-size: 14px; }
                h1 { font-size: 28px; margin-bottom: 20px; text-align: center; }
                p { font-size: 16px; margin: 10px 0; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>Varsity Lodge</div>
                <h1>Card Payment Verification Received</h1>
                <p>Dear $name,</p>
                <p>We have received your card payment verification for Booking ID: <strong>$bookingId</strong>.</p>
                <p>Your booking status is now <strong>Pending Verification</strong>. Our team will verify the payment and confirm your booking shortly.</p>
                <p>We appreciate your prompt action and look forward to welcoming you to Varsity Lodge.</p>
                <div class='footer'>Thank you for choosing Varsity Lodge</div>
            </div>
        </body>
        </html>
        ";
        $mail->AltBody = "Dear $name, We have received your card payment verification for Booking ID: $bookingId. Your booking status is now Pending Verification. We will verify the payment and confirm your booking shortly. Thank you for choosing Varsity Lodge.";

        $mail->send();
        return true;
    } catch (Exception $e) {
        return false;
    }
}

// Function to send payment verification notification email to the admin
function sendAdminNotificationEmail($name, $email, $bookingId) {
    $mail = new PHPMailer(true);
    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'varsitylodgelk@gmail.com';
        $mail->Password = 'bpqe tvas hgnw smlw';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Recipients
        $mail->setFrom('hello@varsitylodge.com', 'Varsity Lodge');
        $mail->addAddress('thisalgamage2727@gmail.com', 'Admin'); // Admin email address

        // Content
        $mail->isHTML(true);
        $mail->Subject = 'New Card Payment Verification Submitted';
        $mail->Body = "
        <!DOCTYPE html>
        <html lang='en'>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <style>
                body { font-family: 'Georgia', serif; color: #333; line-height: 1.6; }
                .container { width: 90%; max-width: 600px; margin: 30px auto; background-color: #fff; padding: 20px; border-radius: 10px; box-shadow: 0 0 15px rgba(0, 0, 0, 0.1); border-top: 5px solid #d4af37; border-bottom: 5px solid #d4af37; }
                .header, .footer { text-align: center; color: #d4af37; font-size: 24px; font-weight: bold; margin-bottom: 20px; }
                .footer { margin-top: 30px; font-size: 14px; }
                h1 { font-size: 28px; margin-bottom: 20px; text-align: center; }
                p { font-size: 16px; margin: 10px 0; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>Varsity Lodge Admin Notification</div>
                <h1>New Card Payment Verification Submitted</h1>
                <p>A new card payment verification has been submitted for Booking ID: <strong>$bookingId</strong>.</p>
                <p>User: <strong>$name</strong> (Email: $email)</p>
                <p>Please verify the payment and update the booking status accordingly.</p>
                <div class='footer'>Varsity Lodge Admin</div>
            </div>
        </body>
        </html>
        ";
        $mail->AltBody = "A new card payment verification has been submitted for Booking ID: $bookingId. User: $name (Email: $email). Please verify the payment and update the booking status accordingly.";

        $mail->send();
        return true;
    } catch (Exception $e) {
        return false;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $booking_id = $_POST['booking_id'] ?? '';

    if (isset($_FILES['payment-confirmation']) && $_FILES['payment-confirmation']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = '../../img/payments/banktransfers/';
        $fileName = basename($_FILES['payment-confirmation']['name']);
        $uploadFile = $uploadDir . $fileName;
        
        // Validate file extension
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $allowedExtensions = ['jpg', 'jpeg', 'png'];

        if (!in_array($fileExtension, $allowedExtensions)) {
            echo json_encode(['success' => false, 'message' => "Only JPG, JPEG, and PNG file formats are supported."]);
            exit;
        }

        // Move file to the designated directory if validation passes
        if (move_uploaded_file($_FILES['payment-confirmation']['tmp_name'], $uploadFile)) {
            $stmt = $conn->prepare('UPDATE booking_details SET payment_slip_img = ?, booking_status = "Pending Verification" WHERE booking_id = ?');
            $stmt->bind_param('ss', $fileName, $booking_id);
            
            if ($stmt->execute()) {
                // Fetch user email and name
                $stmt = $conn->prepare('SELECT name, email FROM booking_details WHERE booking_id = ?');
                $stmt->bind_param('s', $booking_id);
                $stmt->execute();
                $stmt->bind_result($name, $email);
                $stmt->fetch();
                $stmt->close();

                // Send email to the user
                $userEmailSent = sendPaymentConfirmationEmail($name, $email, $booking_id);
                
                // Send email to the admin
                $adminEmailSent = sendAdminNotificationEmail($name, $email, $booking_id);

                if ($userEmailSent && $adminEmailSent) {
                    echo json_encode(['success' => true, 'message' => "Card payment verification uploaded successfully. We'll verify your payment shortly."]);
                } else {
                    echo json_encode(['success' => false, 'message' => "Card payment verification uploaded, but there was an issue sending the confirmation emails."]);
                }
            } else {
                echo json_encode(['success' => false, 'message' => "Failed to update the database."]);
            }
            
            $stmt->close();
        } else {
            echo json_encode(['success' => false, 'message' => "Failed to move uploaded file."]);
        }
    } else {
        echo json_encode(['success' => false, 'message' => "No file uploaded or upload error."]);
    }
    
    $conn->close();
} else {
    echo json_encode(['success' => false, 'message' => "Invalid request method."]);
}
?>

