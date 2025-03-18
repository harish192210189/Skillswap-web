<?php
session_start();
if (!isset($_SESSION["email"])) {
    echo json_encode(["success" => false, "message" => "User not logged in"]);
    exit;
}

$connection = mysqli_connect("localhost", "root", "", "skillswap");

if (!$connection) {
    echo json_encode(["success" => false, "message" => "Database connection failed"]);
    exit;
}

$email = $_SESSION["email"];

$username = mysqli_real_escape_string($connection, $_POST["username"]);
$dob = mysqli_real_escape_string($connection, $_POST["dob"]);
$mobile = mysqli_real_escape_string($connection, $_POST["mobile"]);
$experience = mysqli_real_escape_string($connection, $_POST["experience"]);

$query = "UPDATE user_info SET username='$username', dob='$dob', mobile='$mobile', experience='$experience' WHERE email='$email'";

if (mysqli_query($connection, $query)) {
    echo json_encode(["success" => true, "message" => "Profile updated successfully"]);
} else {
    echo json_encode(["success" => false, "message" => "Error updating profile: " . mysqli_error($connection)]);
}

mysqli_close($connection);
?>
