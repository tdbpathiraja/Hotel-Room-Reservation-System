<?php
include '../../dbconnection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['booking_id'])) {
    $booking_id = $_POST['booking_id'];
    
    
    $stmt = $conn->prepare("DELETE FROM booking_details WHERE booking_id = ? AND booking_status = 'Pending Payment'");
    $stmt->bind_param('s', $booking_id);
    $result = $stmt->execute();
    
    if ($result) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Failed to delete booking']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request']);
}
?>