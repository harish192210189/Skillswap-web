<?php
session_start();
$conn = new mysqli("localhost", "root", "", "skillswap", 3306);

if ($conn->connect_error) {
    die(json_encode(["success" => false, "message" => "Connection failed."]));
}


if (!isset($_SESSION['user_id'])) {
    echo json_encode(["success" => false, "message" => "User not logged in"]);
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['id'], $_POST['action'])) {
    $connection_id = intval($_POST['id']);
    $action = $_POST['action'];

    // Set the new status based on the action
    $new_status = ($action === "accept") ? "accepted" : "denied";

    // Update the connection status in the database
    $sql = "UPDATE connections SET status = ? WHERE id = ? AND receiver_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sii", $new_status, $connection_id, $_SESSION['user_id']);

    if ($stmt->execute()) {
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["success" => false, "message" => "Database update failed"]);
    }

    $stmt->close();
    $conn->close();
} else {
    echo json_encode(["success" => false, "message" => "Invalid request"]);
}
?>
