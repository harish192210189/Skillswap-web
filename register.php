<?php
session_start();
$connection = new mysqli("localhost", "root", "", "skillswap", 3306);

if ($connection->connect_error) {
    die(json_encode(["success" => false, "message" => "Connection failed: " . $connection->connect_error]));
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $mobile = trim($_POST['mobile']);
    $skills = trim($_POST['skills']);
    $experience = trim($_POST['experience']);
    $dob = trim($_POST['dob']);

    // Validate input (basic example)
    if (empty($name) || empty($email) || empty($password) || empty($mobile) || empty($skills) || empty($experience) || empty($dob)) {
        die(json_encode(["success" => false, "message" => "All fields are required."]));
    }

    // Hash the password securely
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Use Prepared Statements to prevent SQL Injection
    $stmt = $connection->prepare("INSERT INTO userregister (Fullname, email, password, mobile, skills, experience, dob) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssss", $name, $email, $hashed_password, $mobile, $skills, $experience, $dob);

    if ($stmt->execute()) {
        // Fetch the newly inserted user ID
        $user_id = $stmt->insert_id;

        // Start session and store user data
        $_SESSION['user_id'] = $user_id;
        $_SESSION['username'] = $name;
        $_SESSION['email'] = $email;
        $_SESSION['dob'] = $dob;
        $_SESSION['mobile'] = $mobile;
        $_SESSION['experience'] = $experience;
        $_SESSION['skills'] = $skills;

        // Redirect to home page 
        echo '<script>location.replace("home.php")</script>';
    } else {
        echo json_encode(["success" => false, "message" => "Error: " . $stmt->error]);
    }

    $stmt->close();
}

$connection->close();
?>
