<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "skillswap";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch skills from the database
if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $sql = "SELECT id, name, skill, experience FROM skills";
    $result = $conn->query($sql);

    $skills = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $skills[] = $row;
        }
    }
    echo json_encode($skills);
}

// Handle swap request submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["user_name"]) && isset($_POST["skill_id"])) {
        $user_name = $conn->real_escape_string($_POST["user_name"]);
        $skill_id = (int)$_POST["skill_id"];

        // Get skill details
        $skillQuery = "SELECT skill FROM skills WHERE id = $skill_id";
        $skillResult = $conn->query($skillQuery);
        if ($skillResult->num_rows > 0) {
            $skillRow = $skillResult->fetch_assoc();
            $skill_name = $skillRow['skill'];

            // Insert swap request into database
            $insertQuery = "INSERT INTO swap_requests (user_name, skill) VALUES ('$user_name', '$skill_name')";
            if ($conn->query($insertQuery) === TRUE) {
                echo "success";
            } else {
                echo "Error: " . $conn->error;
            }
        } else {
            echo "Invalid skill ID.";
        }
    }
}

$conn->close();
?>
