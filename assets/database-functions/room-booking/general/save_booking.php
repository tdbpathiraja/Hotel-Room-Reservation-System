<?php
session_start();
include '../../dbconnection.php';
require '../../../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$mail = new PHPMailer();
echo "PHPMailer loaded successfully!";

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $roomIDs = $_POST['room'] ?? []; 
    $username = $_POST['username'] ?? '';
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $telephone = $_POST['telephone'] ?? '';
    $checkIn = $_POST['check-in'] ?? '';
    $checkOut = $_POST['check-out'] ?? '';
    $adultCount = intval($_POST['adult-count'] ?? 0);
    $childCount = intval($_POST['child-count'] ?? 0);
    $guestCount = $adultCount + $childCount;
    $paymentMethod = $_POST['payment-method'] ?? '';
    $specialNotes = $_POST['special-notes'] ?? '';
    $mealPlan = $_POST['meal-plan'] ?? '';
    $advancePayment = floatval($_POST['advance_payment'] ?? 0.00);

    if (empty($roomIDs)) {
        $_SESSION['message'] = "Please select at least one room.";
        header("Location: ../../../../booking-room.php");
        exit();
    }

    
    $roomIDsStr = implode(",", $roomIDs);

    
    $bookingID = uniqid('VL_');

    
    $roomNames = [];
    foreach ($roomIDs as $roomID) {
        $stmt = $conn->prepare("SELECT RoomName FROM roomdetails WHERE RoomID = ?");
        $stmt->bind_param('i', $roomID);
        $stmt->execute();
        $stmt->bind_result($roomName);
        $stmt->fetch();
        $stmt->close();
        
        if (!empty($roomName)) {
            $roomNames[] = $roomName;
        }
    }

    if (empty($roomNames)) {
        $_SESSION['message'] = "Invalid room selection. Please try again.";
        header("Location: ../../../../booking-room.php");
        exit();
    }

    
    $roomNamesStr = implode(", ", $roomNames);

    
    $sql = "INSERT INTO booking_details (
        booking_id, room_id, room_name, username, name, email, telephone, check_in, check_out,
        guest_count, adult_count, child_count, payment_method, special_notes, meal_plan, advanced_payment
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die("Error in preparing statement: " . $conn->error);
    }

    
    $stmt->bind_param(
        'sssssssssiiisssd',
        $bookingID, $roomIDsStr, $roomNamesStr, $username, $name, $email, $telephone,
        $checkIn, $checkOut, $guestCount, $adultCount, $childCount, $paymentMethod, $specialNotes, $mealPlan, $advancePayment
    );

    if ($stmt->execute()) {
        $_SESSION['message'] = "Booking placed successfully!";

        
        function sendEmail($to, $subject, $body, $altBody) {
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
                $mail->addAddress($to);

                
                $mail->isHTML(true);
                $mail->Subject = $subject;
                $mail->Body = $body;
                $mail->AltBody = $altBody;

                $mail->send();
                return true;
            } catch (Exception $e) {
                return "Mailer Error: {$mail->ErrorInfo}";
            }
        }

        // Send email to the user
        $userEmailBody = "
        <!DOCTYPE html>
        <html lang='en'>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <style>
        body {
            font-family: 'Georgia', serif;
            background-color: #f9f9f9;
            color: #333;
            line-height: 1.6;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 90%;
            max-width: 600px;
            margin: 30px auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
            border-top: 5px solid #d4af37;
            border-bottom: 5px solid #d4af37;
        }
        .header, .footer {
            text-align: center;
            color: #d4af37;
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 20px;
        }
        .footer {
            margin-top: 30px;
            font-size: 14px;
        }
        h1 {
            color: #333;
            font-size: 28px;
            margin-bottom: 20px;
            text-align: center;
        }
        p {
            font-size: 16px;
            margin: 10px 0;
        }
        .details {
            margin-top: 20px;
            background-color: #f4f4f4;
            padding: 15px;
            border-radius: 8px;
        }
        .details p {
            font-size: 16px;
            line-height: 1.6;
            color: #555;
        }
        .details p strong {
            color: #333;
        }
        .cta-button {
            display: inline-block;
            padding: 10px 20px;
            margin-top: 20px;
            font-size: 16px;
            color: #fff;
            background-color: #d4af37;
            text-decoration: none;
            border-radius: 5px;
            text-align: center;
        }
        .cta-button:hover {
            background-color: #b3942c;
        }
        .payment-info {
            margin-top: 30px;
            background-color: #fff5e5;
            padding: 15px;
            border-radius: 8px;
            border: 1px solid #d4af37;
        }
        .payment-info p {
            font-size: 15px;
            color: #555;
            margin: 0 0 10px;
        }
        .payment-info p strong {
            color: #d44;
        }
    </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>Varsity Lodge</div>
                <h1>Booking Confirmation</h1>
                <p>Dear $name,</p>
                <p>We are delighted to confirm your booking with the following details:</p>
                <div class='details'>
                    <p><strong>Booking ID:</strong> $bookingID</p>
                    <p><strong>Room Names:</strong> $roomNamesStr</p>
                    <p><strong>Check-In Date:</strong> $checkIn</p>
                    <p><strong>Check-Out Date:</strong> $checkOut</p>
                    <p><strong>Guest Count:</strong> $guestCount</p>
                    <p><strong>Selected Payment Method:</strong> $paymentMethod</p>
                    <p><strong>Meal Plan:</strong> $mealPlan</p><br>
                </div>
                <div class='payment-info'>
                    <p><strong>Important:</strong> Please pay the advanced payment for your selected room within the next 3 days. You can make the payment through your account on our website.</p>
                    <p>If the payment is not received by the deadline, your booking will be automatically canceled by our system.</p>
                </div>
                <a href='https://varsitylodge.lk/my-account.php' class='cta-button'>View Your Booking</a>
                <p>We look forward to welcoming you to Varsity Lodge. Should you have any inquiries, please feel free to contact us.</p>
                <div class='footer'>Thank you for choosing Varsity Lodge</div>
            </div>
        </body>
        </html>
        ";

        $userEmailAltBody = "Dear $name, Your booking has been successfully placed. Booking ID: $bookingID, Check-In Date: $checkIn, Check-Out Date: $checkOut, Meal Plan: $mealPlan. Thank you for choosing our service.";

        $userEmailSent = sendEmail($email, 'Booking Confirmation', $userEmailBody, $userEmailAltBody);

        // Send email to the admin
        $adminEmail = 'tdbpathiraja@gmail.com';
        $adminEmailSubject = 'New Booking Notification';
        $adminEmailBody = "
        <h1>New Booking Notification</h1>
        <p>A new booking has been placed with the following details:</p>
        <ul>
            <li><strong>Guest Name:</strong> $name</li>
            <li><strong>Email:</strong> $email</li>
            <li><strong>Telephone:</strong> $telephone</li>
            <li><strong>Check-In Date:</strong> $checkIn</li>
            <li><strong>Check-Out Date:</strong> $checkOut</li>
            <li><strong>Guest Count:</strong> $guestCount</li>
            <li><strong>Payment Method:</strong> $paymentMethod</li>
            <li><strong>Meal Plan:</strong> $mealPlan</li>
            <li><strong>Room Names:</strong> $roomNamesStr</li>
        </ul>
        <p>Please review and process this booking as necessary.</p>
        ";

        $adminEmailAltBody = "New booking placed by $name. Check-in: $checkIn, Check-out: $checkOut. Room(s): $roomNamesStr. Please review and process.";

        $adminEmailSent = sendEmail($adminEmail, $adminEmailSubject, $adminEmailBody, $adminEmailAltBody);

        if ($userEmailSent !== true || $adminEmailSent !== true) {
            $_SESSION['message'] = "Booking placed successfully, but there were issues sending confirmation emails. User email status: $userEmailSent. Admin email status: $adminEmailSent.";
        }
    } else {
        $_SESSION['message'] = "Error: Could not place booking. Please try again.";
    }

    $stmt->close();
    $conn->close();

    header("Location: ../../../../booking-room.php");
    exit();
}
?>
