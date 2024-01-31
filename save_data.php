<?php
header('Content-Type: application/json');

// Include the database connection code
include("./config/database.php");

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $user_id = $_POST["user_id"];
    $adjective = $_POST["adjective"];
    $nick_name = $_POST["nickName"];

    // Prepare and bind the SQL statement with placeholders for update
    $sql = "UPDATE users SET adjective=?, nick_name=? WHERE user_id=?";
    $stmt = $conn->prepare($sql);

    // Bind parameters to the placeholders
    $stmt->bind_param("ssi", $adjective, $nick_name, $user_id);

    // Execute the statement
    if ($stmt->execute()) {
        $response = [
            'success' => true,
            'message' => 'Data updated successfully',
            'data' => [
                'user_id' => $user_id,
                'adjective' => $adjective,
                'nick_name' => $nick_name
            ]
        ];
        echo json_encode($response);
    } else {
        $response = [
            'success' => false,
            'message' => 'Error while updating data !',
            'data' => [
                'user_id' => $user_id,
                'adjective' => $adjective,
                'nick_name' => $nick_name
            ]
        ];
        echo json_encode($response);
    }

    // Close the statement
    $stmt->close();
}

// Close the database connection
$conn->close();
?>
