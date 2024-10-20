<?php
session_start();
include '../dbconnection.php';

if (!isset($_SESSION['username']) && !isset($_COOKIE['username'])) {
    die(json_encode(['success' => false, 'message' => 'Unauthorized access']));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['imageName'];
    $file = $_FILES['imageFile'];

    
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
    $maxSize = 5 * 1024 * 1024;

    if (!in_array($file['type'], $allowedTypes)) {
        die(json_encode(['success' => false, 'message' => 'Invalid file type. Only JPG, PNG, and GIF are allowed.']));
    }

    if ($file['size'] > $maxSize) {
        die(json_encode(['success' => false, 'message' => 'File is too large. Maximum size is 5MB.']));
    }

    
    $filename = uniqid() . '_' . $file['name'];
    $uploadPath = '../../../assets/img/gallery/' . $filename;

    if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
       
        $sql = "INSERT INTO gallery (image_url, title) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $filename, $title);

        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Image uploaded successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Database error: ' . $conn->error]);
        }

        $stmt->close();
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to upload file']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}

$conn->close();
?>