<?php
include '../dbconnection.php';

$id = $_POST['id'];


$query = "SELECT COUNT(*) as bookingCount FROM booking_details WHERE room_id = ? AND (check_out >= CURDATE() OR booking_status NOT IN ('checked out', 'reviewed'))";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();


if ($row['bookingCount'] == 0) {
    echo json_encode(['canDelete' => true]); 
} else {
    echo json_encode(['canDelete' => false]); 
}
?>


