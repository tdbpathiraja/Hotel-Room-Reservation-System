<?php
session_start();
include '../dbconnection.php';

if (!isset($_SESSION['username']) && !isset($_COOKIE['username'])) {
    die(json_encode(['success' => false, 'message' => 'Unauthorized access']));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $id = $data['id'];

    $sql = "SELECT image_url FROM gallery WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        $imageUrl = $row['image_url'];
        $imagePath = '../../../assets/img/gallery/' . $imageUrl;

        if (file_exists($imagePath) && unlink($imagePath)) {
            
            $deleteSql = "DELETE FROM gallery WHERE id = ?";
            $deleteStmt = $conn->prepare($deleteSql);
            $deleteStmt->bind_param("i", $id);

            if ($deleteStmt->execute()) {
                echo json_encode(['success' => true, 'message' => 'Image deleted successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to delete database entry']);
            }

            $deleteStmt->close();
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to delete image file']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Image not found in database']);
    }

    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}

$conn->close();
?>