<?php
$uploadDir = '../../img/payments/banktransfers/'; 


function deleteAllBankSlips($uploadDir) {
   
    if (is_dir($uploadDir)) {
        $files = glob($uploadDir . '*');

        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }

        echo json_encode(['success' => true, 'message' => 'All bank slips deleted successfully.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Directory does not exist.']);
    }
}


deleteAllBankSlips($uploadDir);
?>