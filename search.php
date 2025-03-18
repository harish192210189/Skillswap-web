<?php
$connection = new mysqli("localhost", "root", "", "skillswap", 3306);

if ($connection->connect_error) {
    die(json_encode(["success" => false, "message" => "Connection failed."]));
}

// Check if a search query is sent
if (isset($_GET['query']) && !empty($_GET['query'])) {
    $search = "%" . $connection->real_escape_string($_GET['query']) . "%";

    // Fetch users with matching skills
    $stmt = $connection->prepare("SELECT Fullname AS name, skills AS skill, experience FROM userregister WHERE skills LIKE ?");
    $stmt->bind_param("s", $search);
    $stmt->execute();
    $result = $stmt->get_result();

    $users = [];
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }

    echo json_encode(["success" => true, "users" => $users]);
} else {
    echo json_encode(["success" => false, "message" => "No search query provided."]);
}

$connection->close();
?>
