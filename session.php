<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    echo json_encode(["success" => false, "message" => "User not logged in"]);
    exit();
}

// Return session data as JSON
echo json_encode([
    "success" => true,
    "username" => $_SESSION['username'],
    "email" => $_SESSION['email'],
    "dob" => $_SESSION['dob'],
    "mobile" => $_SESSION['mobile'],
    "experience" => $_SESSION['experience'],
    "skills" => $_SESSION['skills']
]);
?>