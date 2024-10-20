<?php
include '../dbconnection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];

    $sql = "SELECT profile_image, username FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if (!$user) {
        echo json_encode(['success' => false, 'message' => 'User not found.']);
        $stmt->close();
        $conn->close();
        exit();
    }

    $conn->begin_transaction();

    try {
        
        $sql = "DELETE FROM users WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);

        if (!$stmt->execute()) {
            throw new Exception($conn->error);
        }

        
        $sql = "DELETE FROM booking_details WHERE username = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $user['username']);

        if (!$stmt->execute()) {
            throw new Exception($conn->error);
        }

        
        if ($user['profile_image']) {
            
            $upload_dir = '../../img/visitor-profiles/';
            $file_path = $upload_dir . basename($user['profile_image']);

            if (file_exists($file_path)) {
                unlink($file_path);
            }
        }

        
        $conn->commit();
        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        
        $conn->rollback();
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }

    $stmt->close();
    $conn->close();
}
?>
