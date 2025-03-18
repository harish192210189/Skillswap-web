<?php
session_start();
$connection = new mysqli("localhost", "root", "", "skillswap", 3306);

if ($connection->connect_error) {
    die(json_encode(["success" => false, "message" => "Connection failed."]));
}

if (!isset($_SESSION['user_id'])) {
    echo "Login required!";
    exit();
}

$request_id = $_POST['request_id'];
$action = $_POST['action']; // "accepted" or "denied"

$sql = "UPDATE connections SET status = ? WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("si", $action, $request_id);

if ($stmt->execute()) {
    echo "Request " . $action;
} else {
    echo "Failed to update request.";
}

$stmt->close();
$conn->close();
?>
