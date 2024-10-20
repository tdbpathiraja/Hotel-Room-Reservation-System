<?php
include '../dbconnection.php';
require '../../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $booking_id = $_POST['booking_id'] ?? '';
    $new_status = $_POST['new_status'] ?? '';

    if (empty($booking_id) || empty($new_status)) {
        echo json_encode(['success' => false, 'message' => 'Missing required parameters']);
        exit;
    }

    $valid_statuses = ['Pending Payment', 'Pending Verification', 'Booked', 'Checked in', 'checked out'];
    if (!in_array($new_status, $valid_statuses)) {
        echo json_encode(['success' => false, 'message' => 'Invalid status']);
        exit;
    }

    $sql = "UPDATE booking_details SET booking_status = ? WHERE booking_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ss', $new_status, $booking_id);

    if ($stmt->execute()) {
       
        if ($new_status === 'Booked') {
            $update_payment_sql = "UPDATE booking_details SET advanced_payment = advanced_payment * 2 WHERE booking_id = ?";
            $update_payment_stmt = $conn->prepare($update_payment_sql);
            $update_payment_stmt->bind_param('s', $booking_id);
            $update_payment_stmt->execute();
            $update_payment_stmt->close();
        }

        if ($new_status === 'Booked' || $new_status === 'checked out') {
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

                if ($new_status === 'Booked') {
                    $mail->Subject = 'Booking Confirmation - Varsity Lodge';
                    $mail->Body = "
                    <!DOCTYPE html>
                    <html lang='en'>
                    <head>
                        <meta charset='UTF-8'>
                        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
                        <style>
                            body { font-family: 'Georgia', serif; color: #333; line-height: 1.6; }
                            .container { width: 90%; max-width: 600px; margin: 30px auto; background-color: #fff; padding: 20px; border-radius: 10px; box-shadow: 0 0 15px rgba(0, 0, 0, 0.1); border-top: 5px solid #5cb85c; border-bottom: 5px solid #5cb85c; }
                            .header, .footer { text-align: center; color: #5cb85c; font-size: 24px; font-weight: bold; margin-bottom: 20px; }
                            .footer { margin-top: 30px; font-size: 14px; }
                            h1 { font-size: 28px; margin-bottom: 20px; text-align: center; }
                            p { font-size: 16px; margin: 10px 0; }
                            .highlight { color: #5cb85c; font-weight: bold; }
                            .info { border: 1px solid #5cb85c; padding: 15px; border-radius: 5px; background-color: #f9f9f9; margin-top: 20px; }
                        </style>
                    </head>
                    <body>
                        <div class='container'>
                            <div class='header'>Varsity Lodge</div>
                            <h1>Booking Confirmed</h1>
                            <p>Dear $name,</p>
                            <p>We are pleased to inform you that your booking (ID: <strong>$booking_id</strong>) has been confirmed.</p>
                            <p>We look forward to welcoming you to Varsity Lodge. Should you have any inquiries, please feel free to contact us.</p>
                            <div class='info'>
                                <p><span class='highlight'>Check-In Time:</span> From 02:00 PM onwards</p>
                                <p><span class='highlight'>Check-Out Time:</span> By 12:00 PM on your departure date</p>
                            </div>
                            <div class='footer'>Thank you for choosing Varsity Lodge</div>
                        </div>
                    </body>
                    </html>
                    ";
                    $mail->AltBody = "Dear $name, Your booking with Booking ID: $booking_id has been confirmed. Check-In Time: From 11:00 AM onwards. Check-Out Time: By 12:00 PM on your departure date. We look forward to welcoming you to Varsity Lodge.";
                } elseif ($new_status === 'checked out') {
                    $mail->Subject = 'Thank You for Your Stay - Varsity Lodge';
                    $mail->Body = "
                    <!DOCTYPE html>
                    <html lang='en'>
                    <head>
                        <meta charset='UTF-8'>
                        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
                        <style>
                            body { font-family: 'Georgia', serif; color: #333; line-height: 1.6; }
                            .container { width: 90%; max-width: 600px; margin: 30px auto; background-color: #fff; padding: 20px; border-radius: 10px; box-shadow: 0 0 15px rgba(0, 0, 0, 0.1); border-top: 5px solid #5cb85c; border-bottom: 5px solid #5cb85c; }
                            .header, .footer { text-align: center; color: #5cb85c; font-size: 24px; font-weight: bold; margin-bottom: 20px; }
                            .footer { margin-top: 30px; font-size: 14px; }
                            h1 { font-size: 28px; margin-bottom: 20px; text-align: center; }
                            p { font-size: 16px; margin: 10px 0; }
                            .highlight { color: #5cb85c; font-weight: bold; }
                            .button { display: inline-block; padding: 10px 20px; background-color: #5cb85c; color: #ffffff; text-decoration: none; border-radius: 5px; font-weight: bold; margin-top: 20px; }
                        </style>
                    </head>
                    <body>
                        <div class='container'>
                            <div class='header'>Varsity Lodge</div>
                            <h1>Thank You for Your Stay</h1>
                            <p>Dear $name,</p>
                            <p>We hope you're enjoying your stay at Varsity Lodge. Thank you for choosing us for your accommodation needs.</p>
                            <p>We value your feedback and would appreciate if you could take a moment to share your experience with us.</p>
                            <p style='text-align: center;'>
                                <a href='https://varsitylodge.lk/my-account.php' class='button'>Leave a Review on Your Account</a>
                            </p>
                            <p>Your input helps us improve and provide better service to future guests.</p>
                            <div class='footer'>Enjoy the rest of your stay at Varsity Lodge</div>
                        </div>
                    </body>
                    </html>
                    ";
                    $mail->AltBody = "Dear $name, Thank you for staying at Varsity Lodge. We hope you're enjoying your stay. We'd appreciate if you could leave a review of your experience. You can do so at: https://www.varsitylodge.com/review?booking_id=$booking_id";
                }

                $mail->send();
            } catch (Exception $e) {
                echo json_encode(['success' => false, 'message' => "Booking status updated, but email could not be sent. Mailer Error: {$mail->ErrorInfo}"]);
                exit();
            }
        }
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Database error']);
    }
}
?>