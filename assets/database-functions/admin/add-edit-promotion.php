<?php
include '../dbconnection.php';

$id = $_POST['id'] ?? null;
$title = $_POST['title'];
$description = $_POST['description'];
$discount_percentage = $_POST['discount_percentage'];
$start_date = $_POST['start_date'];
$end_date = $_POST['end_date'];
$selectedRooms = $_POST['promo_applied_rooms'] ?? [];
$promoAppliedRooms = is_array($selectedRooms) ? implode(',', $selectedRooms) : $selectedRooms;

$target_dir = "../../img/promotions/";
$image = $_FILES['image'] ?? null;

function deleteImageFile($imageName, $targetDir) {
    if ($imageName) {
        $imagePath = $targetDir . $imageName;
        if (file_exists($imagePath)) {
            unlink($imagePath);
        }
    }
}

$newImageName = null;
if ($image && $image['name']) {
    $newImageName = uniqid() . "_" . basename($image["name"]);
    $target_file = $target_dir . $newImageName;
    
    if (!move_uploaded_file($image["tmp_name"], $target_file)) {
        echo json_encode(['success' => false, 'message' => 'Failed to upload image']);
        exit;
    }
}

if ($id) {
    
    $query = "SELECT image FROM promotions WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $currentPromotion = $result->fetch_assoc();

    
    if ($newImageName) {
        $query = "UPDATE promotions SET title = ?, description = ?, discount_percentage = ?, start_date = ?, end_date = ?, image = ?, promo_applied_rooms = ? WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("sssssssi", $title, $description, $discount_percentage, $start_date, $end_date, $newImageName, $promoAppliedRooms, $id);
    } else {
        $query = "UPDATE promotions SET title = ?, description = ?, discount_percentage = ?, start_date = ?, end_date = ?, promo_applied_rooms = ? WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ssssssi", $title, $description, $discount_percentage, $start_date, $end_date, $promoAppliedRooms, $id);
    }

    if ($stmt->execute()) {
        if ($newImageName && $currentPromotion['image']) {
           
            deleteImageFile($currentPromotion['image'], $target_dir);
        }
        echo json_encode(['success' => true]);
    } else {
        
        if ($newImageName) {
            deleteImageFile($newImageName, $target_dir);
        }
        echo json_encode(['success' => false, 'message' => 'Failed to update promotion']);
    }
} else {
    
    $query = "INSERT INTO promotions (title, description, discount_percentage, start_date, end_date, image, promo_applied_rooms) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sssssss", $title, $description, $discount_percentage, $start_date, $end_date, $newImageName, $promoAppliedRooms);

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        
        if ($newImageName) {
            deleteImageFile($newImageName, $target_dir);
        }
        echo json_encode(['success' => false, 'message' => 'Failed to add new promotion']);
    }
}
?>