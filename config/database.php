<?php

$conn = new mysqli('localhost', 'root', 'sarthak@123', 'cipla');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}




?>