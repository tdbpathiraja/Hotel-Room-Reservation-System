<?php
include '../dbconnection.php';

function deleteReservation($conn, $bookingId) {
    
    $selectSql = "SELECT * FROM booking_details WHERE booking_id = ?";
    $stmt = $conn->prepare($selectSql);
    $stmt->bind_param('s', $bookingId);
    $stmt->execute();
    $result = $stmt->get_result();
    $reservation = $result->fetch_assoc();

    if ($reservation) {
        
        $insertSql = "INSERT INTO cancel_log (booking_id, guest_name, username, contact_number, room_name, check_in, check_out, payment_method, booking_status, advanced_payment) 
                      VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $insertStmt = $conn->prepare($insertSql);
        $insertStmt->bind_param('sssssssssd', 
            $reservation['booking_id'], 
            $reservation['name'], 
            $reservation['username'], 
            $reservation['telephone'], 
            $reservation['room_name'], 
            $reservation['check_in'], 
            $reservation['check_out'], 
            $reservation['payment_method'], 
            $reservation['booking_status'], 
            $reservation['advanced_payment']
        );
        $insertStmt->execute();
    }

    
    $deleteSql = "DELETE FROM booking_details WHERE booking_id = ?";
    $deleteStmt = $conn->prepare($deleteSql);
    $deleteStmt->bind_param('s', $bookingId);
    $result = $deleteStmt->execute();

    return $result;
}

$bookingId = $_POST['booking_id'] ?? '';

$result = deleteReservation($conn, $bookingId);

header('Content-Type: application/json');
echo json_encode(['success' => $result]);