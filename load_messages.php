<?php
session_start();
$connection = new mysqli("localhost", "root", "", "skillswap", 3306);

$sender_id = $_SESSION['user_id'];
$receiver_id = $_POST['chat_partner_id'];

$query = "SELECT * FROM messages WHERE (sender_id = ? AND receiver_id = ?) OR (sender_id = ? AND receiver_id = ?) ORDER BY timestamp ASC";
$stmt = $connection->prepare($query);
$stmt->bind_param("iiii", $sender_id, $receiver_id, $receiver_id, $sender_id);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $class = $row['sender_id'] == $sender_id ? "sent" : "received";
    echo "<div class='message $class'>" . htmlspecialchars($row['message']) . "</div>";
}

$connection->close();
?>
