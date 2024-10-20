<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include '../assets/database-functions/dbconnection.php';

$sql = "DELETE FROM super_admin";

if ($conn->query($sql) === TRUE) {
    echo "All super admin records deleted successfully!";
} else {
    echo "Error deleting records: " . $conn->error;
}

$conn->close();
?>
