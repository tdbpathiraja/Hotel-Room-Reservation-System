<?php
include '../dbconnection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $full_name = $_POST['fullName'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $gender = $_POST['gender'];
    $birth_date = $_POST['birthDate'];

    $stmt = $conn->prepare('SELECT username FROM users WHERE id = ?');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $stmt->bind_result($currentUsername);
    $stmt->fetch();
    $stmt->close();

    $stmt = $conn->prepare('SELECT id FROM users WHERE (username = ? OR email = ?) AND id != ?');
    $stmt->bind_param('ssi', $username, $email, $id);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        echo json_encode(['success' => false, 'message' => 'Username or email already exists.']);
        $stmt->close();
        exit();
    }
    $stmt->close();

    if (isset($_FILES['profileImage']) && $_FILES['profileImage']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = '../../img/visitor-profiles/';
        $file_name = uniqid() . '_' . $_FILES['profileImage']['name'];
        $upload_path = $upload_dir . $file_name;

        if (move_uploaded_file($_FILES['profileImage']['tmp_name'], $upload_path)) {
            $sql = "UPDATE users SET full_name = ?, username = ?, gender = ?, email = ?, birth_date = ?, profile_image = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssssssi", $full_name, $username, $gender, $email, $birth_date, $upload_path, $id);
        }
    } else {
        $sql = "UPDATE users SET full_name = ?, username = ?, gender = ?, email = ?, birth_date = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssi", $full_name, $username, $gender, $email, $birth_date, $id);
    }

    if ($stmt->execute()) {

        $stmt = $conn->prepare('UPDATE booking_details SET username = ?, name = ?, email = ? WHERE username = ?');
        $stmt->bind_param('ssss', $username, $full_name, $email, $currentUsername);
        $stmt->execute();
        $stmt->close();

        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => $conn->error]);
    }

    $stmt->close();
    $conn->close();
}
?>
