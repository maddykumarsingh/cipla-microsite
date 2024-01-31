<?php
header('Content-Type: application/json');

session_start();

// Include the database connection code
include_once('./config/database.php');

// Check if the user is not logged in, redirect to login page
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}



if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Process other form fields if needed
    // ...

    $user_id = intval($_SESSION['user_id']);
    $adjective = $_POST["adjective"];
    $nick_name = $_POST["nickName"];

    // Prepare and bind the SQL statement with placeholders for update
    $sql = "UPDATE users SET adjective=?, nick_name=? WHERE user_id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssi", $adjective, $nick_name, $user_id);
    $stmt->execute();
    $stmt->close();

    // Handle profile image upload
    if (!empty($_FILES['profileImage']['name'])) {
        $targetDir = __DIR__ . '/uploads/user/profile-image/';
        $extension = pathinfo($_FILES['profileImage']['name'], PATHINFO_EXTENSION);
        $timestamp = time();
        $profileImagePath = $targetDir . $timestamp . '.' . $extension;  


        move_uploaded_file($_FILES["profileImage"]["tmp_name"], $profileImagePath);
        // Update the user_data table with the profile image path
        $sql = "UPDATE users SET avatar = ? WHERE user_id = ?";
        $stmt = $conn->prepare($sql);
        $imagePathParameter = $timestamp . '.' . $extension;
        $stmt->bind_param("ss",$imagePathParameter, $user_id);
        $stmt->execute();
        $stmt->close();
    }

    // Handle family picture upload
    if (!empty($_FILES['familyPic']['name'])) {
        $targetDir = __DIR__ . '/uploads/user/family-image/';
        $extension = pathinfo($_FILES['familyPic']['name'], PATHINFO_EXTENSION);
        $timestamp = time();
        $familyPicPath = $targetDir . $timestamp . '.' . $extension;

        move_uploaded_file($_FILES["familyPic"]["tmp_name"], $familyPicPath);

        // SQL statement to insert a record
        $sql = "INSERT INTO user_family_photos (user_id, file_name ) VALUES (?, ?)";

        // Prepare and execute the statement
        $stmt = $conn->prepare($sql);
        $imagePathParameter = $timestamp . '.' . $extension;
        $stmt->bind_param("ss", $user_id,$imagePathParameter);
        $stmt->execute();
        $stmt->close();

        // Do something with the family picture path, maybe save it in the database
        // ...
    }

     header('Location:dashboard.php');

}

// Close the database connection
$conn->close();
?>
