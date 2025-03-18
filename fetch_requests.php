<?php
session_start();
$conn = new mysqli("localhost", "root", "", "skillswap", 3306);

if ($conn->connect_error) {
    die(json_encode(["success" => false, "message" => "Connection failed."]));
}

if (!isset($_SESSION['user_id'])) {
    echo json_encode([]);
    exit();
}

$receiver_id = $_SESSION['user_id'];

$sql = "SELECT c.id, 
               sender.Fullname AS sender_name, 
               sender.skills AS sender_skill, 
               receiver.skills AS receiver_skill
        FROM connections c
        JOIN userregister sender ON c.sender_id = sender.id
        JOIN userregister receiver ON c.receiver_id = receiver.id
        WHERE c.receiver_id = ? AND c.status = 'pending'";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $receiver_id);
$stmt->execute();
$result = $stmt->get_result();

$requests = [];
while ($row = $result->fetch_assoc()) {
    $requests[] = $row;
}

echo json_encode($requests);

$stmt->close();
$conn->close();
?>