<?php
include '../dbconnection.php';

if (isset($_GET['booking_id'])) {
    $booking_id = $_GET['booking_id'];

    // Fetch the specific canceled booking details from the database
    $sql = "SELECT * FROM cancel_log WHERE booking_id = ? LIMIT 1";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("s", $booking_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $booking = $result->fetch_assoc();
            
            // Return the booking details as a JSON response
            echo json_encode([
                'success' => true,
                'booking' => $booking
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Booking not found']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to prepare SQL statement']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid booking ID']);
}
?>
