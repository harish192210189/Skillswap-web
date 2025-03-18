<?php
session_start();

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(["success" => false, "message" => "User not logged in."]);
    exit();
}

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "skillswap"; 
$port = 3306;

$conn = new mysqli($servername, $username, $password, $dbname, $port);

// Check connection
if ($conn->connect_error) {
    echo json_encode(["success" => false, "message" => "Database connection failed."]);
    exit();
}

// Get user ID
$userId = $_SESSION['user_id'];

// Delete all reviews for this user
$sql = "DELETE FROM reviews WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userId);

if ($stmt->execute()) {
    echo json_encode(["success" => true, "message" => "All reviews cleared."]);
} else {
    echo json_encode(["success" => false, "message" => "Failed to clear reviews."]);
}

$stmt->close();
$conn->close();
?>
