<?php
include '../dbconnection.php';

$id = $_POST['id'];

$query = "SELECT COUNT(*) as bookingCount FROM booking_details WHERE room_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

if ($row['bookingCount'] > 0) {
    
    echo json_encode(['success' => false, 'message' => 'You cannot delete the room. Ongoing bookings exist.']);
    exit;
}

$query = "SELECT RoomCardImg, CoverImage FROM roomdetails WHERE RoomID = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$room = $result->fetch_assoc();

$query = "DELETE FROM roomdetails WHERE RoomID = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    
    if ($room['RoomCardImg']) {
        $image_path = "../../img/gallery/rooms/" . $room['RoomCardImg'];
        if (file_exists($image_path)) {
            unlink($image_path);
        }
    }

    
    if ($room['CoverImage']) {
        $cover_images = explode(',', $room['CoverImage']);
        foreach ($cover_images as $cover_image) {
            $image_path = "../../img/gallery/rooms/" . $cover_image;
            if (file_exists($image_path)) {
                unlink($image_path);
            }
        }
    }

    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to delete room']);
}
?>
