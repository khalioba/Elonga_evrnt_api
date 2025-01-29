<?php
function uploadImage($file) {
    if (isset($file) && $file['error'] == 0) {
        
        $targetDir = "../../../image/";
        $fileExtension = pathinfo($file["name"], PATHINFO_EXTENSION); 

        $newFileName = uniqid('elonga_', true) . '.' . $fileExtension;
        $targetFile = $targetDir . $newFileName;

        if (move_uploaded_file($file["tmp_name"], $targetFile)) {
            return [
                "status" => "success",
                "data" => $newFileName
            ];
        } else {
            return [
                "status" => "error",
                "message" => "Failed to upload file"
            ];
        }
    } else {
        return [
            "status" => "error",
            "message" => "No file uploaded or error with file"
        ];
    }
}