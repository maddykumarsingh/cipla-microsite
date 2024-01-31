<?php

include_once('./config/database.php');

$userId = $_GET['userId'];
$filename = $_GET['filename'];

// Replace this with your logic to update the user table with the new avatar filename
// For example, using a database query




if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Update user table
$sql = "UPDATE users SET avatar = '$filename' WHERE user_id = '$userId'";

if ($conn->query($sql) === TRUE) {
    echo json_encode(['message' => 'User avatar updated successfully']);
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Error updating user avatar']);
}

$conn->close();
?>
