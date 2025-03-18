<?php
session_start();
$connection = new mysqli("localhost", "root", "", "skillswap", 3306);

if ($connection->connect_error) {
    die(json_encode(["success" => false, "message" => "Database connection failed"]));
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset($_SESSION['user_id'])) {
        die(json_encode(["success" => false, "message" => "User not logged in"]));
    }

    $user_id = $_SESSION['user_id'];
    $name = trim($_POST['username']);
    $email = trim($_POST['email']);
    $dob = trim($_POST['dob']);
    $mobile = trim($_POST['mobile']);
    $experience = trim($_POST['experience']);
    $skills = trim($_POST['skills']); // Get skills input

    // Validate input
    if (empty($name) || empty($email) || empty($dob) || empty($mobile) || empty($experience)) {
        die(json_encode(["success" => false, "message" => "All fields are required."]));
    }

    // Update user details in the database
    $stmt = $connection->prepare("UPDATE userregister SET Fullname=?, email=?, dob=?, mobile=?, experience=?, skills=? WHERE id=?");
    $stmt->bind_param("ssssssi", $name, $email, $dob, $mobile, $experience, $skills, $user_id);

    if ($stmt->execute()) {
        $_SESSION['username'] = $name;
        $_SESSION['email'] = $email;
        $_SESSION['dob'] = $dob;
        $_SESSION['mobile'] = $mobile;
        $_SESSION['experience'] = $experience;
        $_SESSION['skills'] = $skills;
        echo json_encode(["success" => true, "message" => "Profile updated successfully"]);
    } else {
        echo json_encode(["success" => false, "message" => "Failed to update profile"]);
    }

    $stmt->close();
}

$connection->close();
?>