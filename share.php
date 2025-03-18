<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection
$connection = mysqli_connect("localhost", "root", "", "skillswap", 3306);

if (!$connection) {
    die("Database connection failed: " . mysqli_connect_error());
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset($_POST['name'], $_POST['skill'], $_POST['experience'])) {
        echo "Missing required fields.";
        exit;
    }

    // Sanitize input
    $name = mysqli_real_escape_string($connection, $_POST['name']);
    $skill = mysqli_real_escape_string($connection, $_POST['skill']);
    $experience = mysqli_real_escape_string($connection, $_POST['experience']);

    // Insert data into database
    $sql = "INSERT INTO skills (name, skill, experience) VALUES ('$name', '$skill', '$experience')";

    if (mysqli_query($connection, $sql)) {
        echo "success";
    } else {
        echo "Database error: " . mysqli_error($connection);
    }
}

mysqli_close($connection);
?>
