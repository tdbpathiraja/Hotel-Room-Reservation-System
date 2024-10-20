<?php
include '../dbconnection.php';
header('Content-Type: application/json');

$booking_id = $_POST['booking_id'] ?? '';
$room_name = $_POST['room-name'] ?? '';
$username = $_POST['username'] ?? '';
$name = $_POST['name'] ?? '';
$rating = $_POST['rating'] ?? 0;
$review_text = $_POST['review-text'] ?? '';
$total_rooms = $_POST['total_rooms'] ?? 0;
$current_review_count = $_POST['current_review_count'] ?? 0;

if ($booking_id && $room_name && $username && $rating) {
    // Log the data received
    error_log("Data received: Booking ID: $booking_id, Room Name: $room_name, Username: $username, Rating: $rating");

    $stmt = $conn->prepare('INSERT INTO reviews (room_name, username, name, rating, review_text) VALUES (?, ?, ?, ?, ?)');
    $stmt->bind_param('sssis', $room_name, $username, $name, $rating, $review_text);
    if ($stmt->execute()) {
        error_log("Review inserted successfully.");
    } else {
        error_log("Review insertion failed: " . $stmt->error);
    }
    $stmt->close();

    if ($current_review_count + 1 == $total_rooms) {
        $stmt = $conn->prepare('UPDATE booking_details SET booking_status = "Reviewed" WHERE booking_id = ?');
        $stmt->bind_param('s', $booking_id);
        if ($stmt->execute()) {
            error_log("Booking status updated successfully.");
        } else {
            error_log("Booking status update failed: " . $stmt->error);
        }
        $stmt->close();
    }

    echo json_encode(['status' => 'success']);
} else {
    error_log("Invalid input. Missing data: Booking ID: $booking_id, Room Name: $room_name, Username: $username, Rating: $rating");
    echo json_encode(['status' => 'error', 'message' => 'Invalid input']);
}

?>