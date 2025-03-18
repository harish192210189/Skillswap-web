<?php
$connection = new mysqli("localhost", "root", "", "skillswap", 3306);

if ($connection->connect_error) {
    die(json_encode(["success" => false, "message" => "Connection failed."]));
}

// Get parameters from GET request
$search = isset($_GET['search']) ? "%" . $_GET['search'] . "%" : '';
$skill = isset($_GET['skill']) && $_GET['skill'] !== '' ? $_GET['skill'] : null;
$experience = isset($_GET['experience']) && $_GET['experience'] !== '' ? $_GET['experience'] : null;

// Base query
$query = "SELECT id, Fullname, skills, experience FROM userregister WHERE (Fullname LIKE ? OR skills LIKE ?)";

// Add conditions dynamically
$types = "ss"; // For Fullname and skills
$params = [$search, $search];

if ($skill) {
    $query .= " AND skills = ?";
    $types .= "s";
    $params[] = $skill;
}
if ($experience) {
    $query .= " AND experience = ?";
    $types .= "i"; // Experience is assumed to be an integer
    $params[] = $experience;
}

// Prepare the query
$stmt = $connection->prepare($query);

// Bind parameters dynamically
$stmt->bind_param($types, ...$params);

$stmt->execute();
$result = $stmt->get_result();

$skills = [];
while ($row = $result->fetch_assoc()) {
    $skills[] = $row;
}

// Return the results as JSON
echo json_encode($skills);

$stmt->close();
$connection->close();
?>