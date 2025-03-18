<?php
session_start();
$connection = new mysqli("localhost", "root", "", "skillswap", 3306);

if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $connection->prepare("SELECT id, Fullname, password, email, dob,mobile,experience,skills FROM userregister WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $stored_hash = $user['password'];

        // ✅ Password verification now works correctly
        if (password_verify($password, $stored_hash)) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['Fullname'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['dob'] = $user['dob'];
            $_SESSION['mobile'] = $user['mobile'];
            $_SESSION['experience'] = $user['experience'];
            $_SESSION['skills'] = $user['skills'];
            echo json_encode(["success" => true, "message" => "Login successful", "redirect" => "home.php"]); // Redirect to home page
        } else {
            echo json_encode(["success" => false, "message" => "Incorrect password"]);
        }
    } else {
        echo json_encode(["success" => false, "message" => "User not found"]);
    }

    $stmt->close();
}

$connection->close();
?>
