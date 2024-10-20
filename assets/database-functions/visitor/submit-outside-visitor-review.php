<?php
include '../dbconnection.php'; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $room_name = $_POST['room_name'];
    $rating = $_POST['rating'];
    $review_text = $_POST['review_text'];
    $username = "not registered user"; 

    $sql = "INSERT INTO reviews (room_name, username, name, rating, review_text) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssds", $room_name, $username, $name, $rating, $review_text);

    if ($stmt->execute()) {
        
        echo "<script>
            alert('Review submitted successfully!');
            window.location.href = '../../../send-review.php';
        </script>";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>