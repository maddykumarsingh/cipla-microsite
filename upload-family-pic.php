<?php
$uploadDirectory = __DIR__ . '/uploads/user/family-image/';
$timestamp = time();
$extension = pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION);
$uploadedFile = $uploadDirectory . $timestamp . '.' . $extension;

if (move_uploaded_file($_FILES['avatar']['tmp_name'], $uploadedFile)) {
    echo json_encode(['filename' =>basename($uploadedFile)]);
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to upload file']);
}
?>
