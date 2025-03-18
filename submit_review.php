<?php
$connection = new mysqli("localhost", "root", "", "skillswap", 3306);

if ($connection->connect_error) {
    die("Database connection failed: " . $connection->connect_error);
}

$user_id = $_POST['user_id'];
$reviewer_name = $_POST['reviewer_name'];
$rating = $_POST['rating'];
$review_text = $_POST['review_text'];

$query = "INSERT INTO reviews (user_id, reviewer_name, rating, review_text) VALUES (?, ?, ?, ?)";
$stmt = $connection->prepare($query);
$stmt->bind_param("isis", $user_id, $reviewer_name, $rating, $review_text);
$stmt->execute();

$connection->close();
header("Location: user_profile.php?id=" . $user_id);
exit();
?>