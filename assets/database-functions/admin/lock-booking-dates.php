<?php
session_start();
include '../dbconnection.php';

function lockDates($conn, $roomId, $checkIn, $checkOut) {
    $bookingId = 'LOCK_' . uniqid();
    $sql = "INSERT INTO booking_details (booking_id, room_id, room_name, username, name, email, telephone, check_in, check_out, guest_count, adult_count, child_count, payment_method, booking_status)
            SELECT ?, ?, RoomName, 'SYSTEM', 'LOCKED', 'LOCKED', 'LOCKED', ?, ?, 0, 0, 0, 'SYSTEM', 'Booked'
            FROM roomdetails WHERE RoomID = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sissi", $bookingId, $roomId, $checkIn, $checkOut, $roomId);
    
    return $stmt->execute();
}

function getLockedDates($conn) {
    $sql = "SELECT booking_id, room_name, check_in, check_out FROM booking_details WHERE username = 'SYSTEM' AND name = 'LOCKED' ORDER BY check_in DESC";
    $result = $conn->query($sql);
    return $result->fetch_all(MYSQLI_ASSOC);
}

function deleteLockedDates($conn, $bookingId) {
    $sql = "DELETE FROM booking_details WHERE booking_id = ? AND username = 'SYSTEM' AND name = 'LOCKED'";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $bookingId);
    
    return $stmt->execute();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];
    
    switch ($action) {
        case 'lock':
            $roomId = $_POST['roomId'];
            $checkIn = $_POST['checkIn'];
            $checkOut = $_POST['checkOut'];
            $result = lockDates($conn, $roomId, $checkIn, $checkOut);
            echo json_encode(['success' => $result]);
            break;
        
        case 'getlocked':
            $lockedDates = getLockedDates($conn);
            echo json_encode($lockedDates);
            break;
        
        case 'delete':
            $bookingId = $_POST['bookingId'];
            $result = deleteLockedDates($conn, $bookingId);
            echo json_encode(['success' => $result]);
            break;
        
        default:
            echo json_encode(['error' => 'Invalid action']);
    }
    
    exit;
}