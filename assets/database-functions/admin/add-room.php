<?php
include '../dbconnection.php';

$id = $_POST['RoomID'] ?? null;
$roomName = $_POST['RoomName'];
$description = $_POST['Description'];
$priceLKR = $_POST['PriceLKR'];
$size = $_POST['Size'];
$acAvailable = isset($_POST['ACAvailable']) ? 1 : 0;
$capacity = $_POST['Capacity'];
$adultCount = $_POST['AdultCount'];
$childCount = $_POST['ChildCount'];
$bedType = $_POST['BedType'];
$facilities = $_POST['Facilities'];

$target_dir = "../../img/gallery/rooms/";
$roomCardImg = $_FILES['RoomCardImg'] ?? null;
$coverImages = $_FILES['CoverImage'] ?? null;


function deleteImageFile($imageName, $targetDir) {
    if ($imageName) {
        $imagePath = $targetDir . $imageName;
        if (file_exists($imagePath)) {
            unlink($imagePath);
        }
    }
}


$newRoomCardImgName = null;
if ($roomCardImg && $roomCardImg['name']) {
    $newRoomCardImgName = uniqid() . "_" . basename($roomCardImg["name"]);
    $target_file = $target_dir . $newRoomCardImgName;
    
    if (!move_uploaded_file($roomCardImg["tmp_name"], $target_file)) {
        echo json_encode(['success' => false, 'message' => 'Failed to upload room card image']);
        exit;
    }
}


$newCoverImageNames = [];
if ($coverImages && !empty($coverImages['name'][0])) {
    foreach ($coverImages['name'] as $key => $value) {
        $newImageName = uniqid() . "_" . basename($coverImages["name"][$key]);
        $target_file = $target_dir . $newImageName;
        
        if (move_uploaded_file($coverImages["tmp_name"][$key], $target_file)) {
            $newCoverImageNames[] = $newImageName;
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to upload cover image']);
            exit;
        }
    }
}

if ($id) {
    
    $query = "SELECT RoomCardImg, CoverImage FROM roomdetails WHERE RoomID = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $currentRoom = $result->fetch_assoc();

    
    $query = "UPDATE roomdetails SET RoomName = ?, Description = ?, PriceLKR = ?, Size = ?, ACAvailable = ?, 
              Capacity = ?, AdultCount = ?, ChildCount = ?, BedType = ?, Facilities = ?";
    $params = [$roomName, $description, $priceLKR, $size, $acAvailable, $capacity, $adultCount, $childCount, $bedType, $facilities];
    $types = "ssdsiiiiss";

    if ($newRoomCardImgName) {
        $query .= ", RoomCardImg = ?";
        $params[] = $newRoomCardImgName;
        $types .= "s";
    }

    if (!empty($newCoverImageNames)) {
        $query .= ", CoverImage = ?";
        $newCoverImagesString = implode(',', $newCoverImageNames);
        $params[] = $newCoverImagesString;
        $types .= "s";
    }

    $query .= " WHERE RoomID = ?";
    $params[] = $id;
    $types .= "i";

    $stmt = $conn->prepare($query);
    $stmt->bind_param($types, ...$params);

    if ($stmt->execute()) {
        if ($newRoomCardImgName && $currentRoom['RoomCardImg']) {
            deleteImageFile($currentRoom['RoomCardImg'], $target_dir);
        }
        if (!empty($newCoverImageNames) && $currentRoom['CoverImage']) {
            $oldCoverImages = explode(',', $currentRoom['CoverImage']);
            foreach ($oldCoverImages as $oldImage) {
                deleteImageFile($oldImage, $target_dir);
            }
        }

        
        $updateBookingQuery = "UPDATE booking_details SET room_name = ? WHERE room_id = ?";
        $stmtBooking = $conn->prepare($updateBookingQuery);
        $stmtBooking->bind_param("si", $roomName, $id);
        
        if ($stmtBooking->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update room name in bookings', 'error' => $stmtBooking->error]);
        }
    } else {
        if ($newRoomCardImgName) {
            deleteImageFile($newRoomCardImgName, $target_dir);
        }
        foreach ($newCoverImageNames as $newImage) {
            deleteImageFile($newImage, $target_dir);
        }
        echo json_encode(['success' => false, 'message' => 'Failed to update room']);
    }
} else {
    
    $query = "INSERT INTO roomdetails (RoomName, Description, PriceLKR, Size, ACAvailable, Capacity, AdultCount, 
              ChildCount, BedType, RoomCardImg, CoverImage, Facilities) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $coverImagesString = implode(',', $newCoverImageNames); 
    
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        echo json_encode(['success' => false, 'message' => 'Failed to prepare statement', 'error' => $conn->error]);
        exit;
    }
    
    $stmt->bind_param(
        "ssdsiiiissss", 
        $roomName,
        $description,
        $priceLKR,
        $size,
        $acAvailable,
        $capacity,
        $adultCount,
        $childCount,
        $bedType,
        $newRoomCardImgName,
        $coverImagesString,
        $facilities
    );
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        
        if ($newRoomCardImgName) {
            deleteImageFile($newRoomCardImgName, $target_dir);
        }
        foreach ($newCoverImageNames as $newImage) {
            deleteImageFile($newImage, $target_dir);
        }
        echo json_encode(['success' => false, 'message' => 'Failed to add new room', 'error' => $stmt->error]); 
    }
}
?>
