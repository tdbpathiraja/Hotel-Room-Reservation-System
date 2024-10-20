<?php
include '../dbconnection.php';


function deleteAllUsers($conn) {
    $sql = "DELETE FROM users";
    if ($conn->query($sql) === TRUE) {
        echo json_encode(['success' => true, 'message' => 'All user data deleted successfully.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error deleting user data: ' . $conn->error]);
    }
}


deleteAllUsers($conn);
?>
