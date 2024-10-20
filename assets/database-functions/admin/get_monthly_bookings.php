<?php

include '../dbconnection.php';

function getMonthlyBookings($conn, $year) {
    $sql = "SELECT MONTH(check_in) as month, COUNT(*) as count 
            FROM booking_details 
            WHERE YEAR(check_in) = ? AND booking_status = 'Booked'
            GROUP BY MONTH(check_in)
            ORDER BY month ASC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $year);
    $stmt->execute();
    $result = $stmt->get_result();
    $monthlyData = array_fill(1, 12, 0);
    while ($row = $result->fetch_assoc()) {
        $monthlyData[$row['month']] = $row['count'];
    }
    return array_values($monthlyData);
}

if (isset($_GET['year'])) {
    $year = intval($_GET['year']);
    $monthlyBookings = getMonthlyBookings($conn, $year);
    echo json_encode($monthlyBookings);
}

$conn->close();
?>