<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    die("User not logged in.");
}

$logged_in_user = $_SESSION['user_id'];
$receiver_id = isset($_GET['receiver_id']) ? intval($_GET['receiver_id']) : 0;

$connection = new mysqli("localhost", "root", "", "skillswap", 3306);
if ($connection->connect_error) {
    die("Database connection failed.");
}

$query = "SELECT * FROM messages 
          WHERE (sender_id = ? AND receiver_id = ?) 
             OR (sender_id = ? AND receiver_id = ?) 
          ORDER BY timestamp ASC";
$stmt = $connection->prepare($query);
$stmt->bind_param("iiii", $logged_in_user, $receiver_id, $receiver_id, $logged_in_user);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $class = ($row['sender_id'] == $logged_in_user) ? "sent" : "received";
    echo "<div class='message $class'><b>{$row['message']}</b> <small>{$row['timestamp']}</small></div>";
}

$connection->close();
?>
