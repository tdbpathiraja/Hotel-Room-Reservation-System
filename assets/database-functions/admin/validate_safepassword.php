<?php

include '../dbconnection.php';


$response = ['success' => false];


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
   
    $enteredPassword = $_POST['password'] ?? '';

    
    $sql = "SELECT password_hash FROM safe_password LIMIT 1";
    $result = $conn->query($sql);

    
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $hashedPassword = $row['password_hash'];

        
        if (password_verify($enteredPassword, $hashedPassword)) {
            $response['success'] = true;
        }
    }
}


header('Content-Type: application/json');
echo json_encode($response);
?>
