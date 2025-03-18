<?php
$connection = new mysqli("localhost", "root", "", "skillswap", 3306);

if ($connection->connect_error) {
    die(json_encode(["success" => false, "message" => "Connection failed."]));
}

session_start();
if (!isset($_SESSION['user_id'])) {
    echo json_encode(["success" => false, "message" => "User not logged in."]);
    exit();
}

$sender_id = $_SESSION['user_id'];
$receiver_id = $_POST['receiver_id'];
$receiver_name = $_POST['receiver_name'];

// Check if the request already exists
$checkQuery = "SELECT * FROM connections WHERE sender_id = ? AND receiver_id = ?";
$stmt = $connection->prepare($checkQuery);
$stmt->bind_param("ii", $sender_id, $receiver_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo json_encode(["success" => false, "message" => "Request already sent."]);
    exit();
}

// Insert swap request
$insertQuery = "INSERT INTO connections (sender_id, receiver_id, status) VALUES (?, ?, 'pending')";
$stmt = $connection->prepare($insertQuery);
$stmt->bind_param("ii", $sender_id, $receiver_id);

if ($stmt->execute()) {
    echo json_encode(["success" => true, "message" => "Connection request sent to $receiver_name."]);
} else {
    echo json_encode(["success" => false, "message" => "Failed to send request."]);
}

$connection->close();
?>
