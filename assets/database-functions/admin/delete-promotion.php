<?php
include '../dbconnection.php';

$id = $_POST['id'];

$query = "SELECT image FROM promotions WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$promotion = $result->fetch_assoc();

$query = "DELETE FROM promotions WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    
    if ($promotion['image']) {
        $image_path = "../../img/promotions/" . $promotion['image'];
        if (file_exists($image_path)) {
            unlink($image_path);
        }
    }
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false]);
}
?>