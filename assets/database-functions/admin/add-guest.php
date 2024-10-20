<?php
include '../dbconnection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = $_POST['fullName'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $gender = $_POST['gender'];
    $birth_date = $_POST['birthDate'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    
    $profile_image = null;
    if (isset($_FILES['profileImage']) && $_FILES['profileImage']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = 'uploads/';
        $file_name = uniqid() . '_' . $_FILES['profileImage']['name'];
        $upload_path = $upload_dir . $file_name;

        if (move_uploaded_file($_FILES['profileImage']['tmp_name'], $upload_path)) {
            $profile_image = $upload_path;
        }
    }

    $sql = "INSERT INTO users (full_name, username, gender, email, password_hash, birth_date, profile_image) 
            VALUES (?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssss", $full_name, $username, $gender, $email, $password, $birth_date, $profile_image);

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => $conn->error]);
    }

    $stmt->close();
    $conn->close();
}
?>