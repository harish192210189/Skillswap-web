<?php
session_start();
header('Content-Type: application/json');

$host = "localhost";
$user = "root";
$password = "";
$database = "skillswap";

$conn = new mysqli($host, $user, $password, $database);
if ($conn->connect_error) {
    die(json_encode(["success" => false, "message" => "Database connection failed: " . $conn->connect_error]));
}

if (!isset($_SESSION['user_id'])) {
    die(json_encode(["success" => false, "message" => "User not logged in"]));
}

$user_id = $_SESSION['user_id'];

$sql = "SELECT name, email, dob, `mobile number` AS mobile, experience FROM userregister WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();

    // ✅ Fetch user skills
    $skillsQuery = "SELECT skill, experience FROM skills WHERE user_id = ?";  // Ensure `user_id` column exists in `skills` table
    $skillsStmt = $conn->prepare($skillsQuery);
    $skillsStmt->bind_param("i", $user_id);
    $skillsStmt->execute();
    $skillsResult = $skillsStmt->get_result();

    $skills = [];
    while ($row = $skillsResult->fetch_assoc()) {
        $skills[] = $row;
    }

    echo json_encode([
        "success" => true,
        "username" => $user['name'],
        "email" => $user['email'],
        "dob" => $user['dob'],
        "mobile" => $user['mobile'],
        "experience" => $user['experience'],
        "skills" => $skills
    ]);
} else {
    echo json_encode(["success" => false, "message" => "User not found"]);
}

$conn->close();
?>
