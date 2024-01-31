<?php
 include_once('./config/database.php');

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}



$user_id = $_GET['userId'];
$file_name = $_GET['filename'];

// SQL statement to insert a record
$sql = "INSERT INTO user_family_photos (user_id, file_name ) VALUES (?, ?)";

// Prepare and execute the statement
$stmt = $conn->prepare($sql);
$stmt->bind_param("is", $user_id, $file_name);

if ($stmt->execute()) {
    echo json_encode(['message' => 'Family photo uploaded successfully']);
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Error uploading family photo']);
}

// Close the statement and connection
$stmt->close();
$conn->close();
?>
