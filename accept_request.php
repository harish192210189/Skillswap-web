<?php
$connection = new mysqli("localhost", "root", "", "skillswap", 3306);
session_start();

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["success" => false, "message" => "User not logged in."]);
    exit();
}

$user_id = $_SESSION['user_id'];
$request_id = $_POST['request_id']; // The ID of the connection request

// Update connection status
$updateQuery = "UPDATE connections SET status = 'accepted' WHERE receiver_id = ? AND sender_id = ?";
$stmt = $connection->prepare($updateQuery);
$stmt->bind_param("ii", $user_id, $request_id);

if ($stmt->execute()) {
    echo json_encode(["success" => true, "message" => "Connection request accepted. You can now chat!"]);
} else {
    echo json_encode(["success" => false, "message" => "Failed to accept request."]);
}

$connection->close();
?>
