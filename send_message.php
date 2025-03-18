<?php
session_start();
$connection = new mysqli("localhost", "root", "", "skillswap", 3306);

if (!isset($_SESSION['user_id'])) {
    exit();
}

$sender_id = $_SESSION['user_id'];
$receiver_id = $_POST['receiver_id'];
$message = $_POST['message'];

$query = "INSERT INTO messages (sender_id, receiver_id, message) VALUES (?, ?, ?)";
$stmt = $connection->prepare($query);
$stmt->bind_param("iis", $sender_id, $receiver_id, $message);
$stmt->execute();

$connection->close();
?>
