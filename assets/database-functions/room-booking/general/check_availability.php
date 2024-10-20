<?php
include "../../dbconnection.php";

if (isset($_POST['check_in']) && isset($_POST['check_out']) && isset($_POST['adult_count'])) {
    $checkIn = $_POST['check_in'];
    $checkOut = $_POST['check_out'];
    $adultCount = intval($_POST['adult_count']);
    $childCount = isset($_POST['child_count']) ? intval($_POST['child_count']) : 0;
    $totalGuestCount = $adultCount + $childCount; 

    $current_date = date('Y-m-d');
    $promo_sql = "SELECT * FROM promotions WHERE start_date <= '$current_date' AND end_date >= '$current_date'";
    $promo_result = $conn->query($promo_sql);

    $active_promotions = [];
    while ($promo_row = $promo_result->fetch_assoc()) {
        $promo_rooms = explode(',', $promo_row['promo_applied_rooms']);
        $active_promotions[] = [
            'discount_percentage' => (float) $promo_row['discount_percentage'],
            'rooms' => $promo_rooms,
        ];
    }

    $stmt = $conn->prepare(
        "SELECT RoomID, RoomName, Capacity, AdultCount, ChildCount, PriceLKR, RoomCardImg
         FROM roomdetails
         WHERE RoomID NOT IN (
             SELECT DISTINCT SUBSTRING_INDEX(SUBSTRING_INDEX(room_id, ',', numbers.n), ',', -1) as id
             FROM booking_details
             CROSS JOIN (
                 SELECT 1 AS n UNION SELECT 2 UNION SELECT 3 UNION SELECT 4
             ) numbers
             WHERE (? <= check_out AND ? >= check_in)
               AND CHAR_LENGTH(room_id) - CHAR_LENGTH(REPLACE(room_id, ',', '')) >= numbers.n - 1
         )"
    );
    $stmt->bind_param("ss", $checkIn, $checkOut);
    $stmt->execute();
    $result = $stmt->get_result();

    $availableRooms = [];

    while ($row = $result->fetch_assoc()) {
        $roomID = $row['RoomID'];
        $finalPriceLKR = $row['PriceLKR'];

        foreach ($active_promotions as $promo) {
            if (in_array($roomID, $promo['rooms'])) {
                $finalPriceLKR = $finalPriceLKR * (1 - ($promo['discount_percentage'] / 100));
                break;
            }
        }

        $row['PriceLKR'] = $finalPriceLKR;
        $availableRooms[] = $row;
    }

    $matchingCombinations = [];
    $totalRooms = count($availableRooms);

    for ($i = 1; $i < (1 << $totalRooms); $i++) {
        $combination = [];
        $totalCapacity = 0;

        for ($j = 0; $j < $totalRooms; $j++) {
            if ($i & (1 << $j)) {
                $combination[] = $availableRooms[$j];
                $totalCapacity += $availableRooms[$j]['Capacity'];
            }
        }

        if ($totalCapacity >= $totalGuestCount) {
            $matchingCombinations[] = $combination;
        }
    }

    if (count($matchingCombinations) > 0) {
        
        usort($matchingCombinations, function($a, $b) {
            return count($a) <=> count($b);
        });

        
        echo json_encode(["availableRooms" => $matchingCombinations[0]]);
    } else {
        
        echo json_encode(["availableRooms" => [], "message" => "No rooms available for the selected dates. Please try different dates."]);
    }

    $stmt->close();
}

$conn->close();
?>
