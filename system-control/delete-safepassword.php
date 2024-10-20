<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

include '../assets/database-functions/dbconnection.php';

$sql = "DELETE FROM safe_password";

if ($conn->query($sql) === TRUE) {
    echo "All safe password records deleted successfully!";
} else {
    echo "Error deleting records: " . $conn->error;
}

$conn->close();
?>
