<?php

include('../dbconnection.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $image = $_POST['image'];
    $targetDir = ".../../img/gallery/rooms/";
    $filePath = $targetDir . $image;

    if (file_exists($filePath)) {
        unlink($filePath);
    }

    $stmt = $pdo->prepare("UPDATE roomdetails SET CoverImage = REPLACE(CoverImage, ?, '') WHERE FIND_IN_SET(?, CoverImage) > 0");
    $stmt->execute([$image, $image]);

    echo 'Image deleted successfully';
}
?>
