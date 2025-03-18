<?php
$connection = new mysqli("localhost", "root", "", "skillswap", 3306);
session_start();

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["success" => false, "message" => "User not logged in."]);
    exit();
}

$user_id = $_SESSION['user_id'];
$chat_partner_id = $_POST['chat_partner_id'];

$query = "SELECT status FROM connections WHERE (sender_id = ? AND receiver_id = ?) OR (sender_id = ? AND receiver_id = ?)";
$stmt = $connection->prepare($query);
$stmt->bind_param("iiii", $user_id, $chat_partner_id, $chat_partner_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    if ($row['status'] === 'accepted') {
        echo json_encode(["success" => true, "message" => "Chat access granted."]);
    } else {
        echo json_encode(["success" => false, "message" => "Chat is only available after request acceptance."]);
    }
} else {
    echo json_encode(["success" => false, "message" => "No connection found."]);
}

$connection->close();
?>
