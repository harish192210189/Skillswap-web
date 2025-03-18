<?php
$servername = "localhost";
$username = "root";  
$password = "";  
$database = "skillswap";
$port = 3306;

// Connect to database
$conn = new mysqli($servername, $username, $password, $database, $port);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get search query
$search = isset($_POST['query']) ? trim($_POST['query']) : '';

$response = [];

if (!empty($search)) {
    $sql = "SELECT Fullname, skills,id FROM userregister WHERE skills LIKE ?";
    $stmt = $conn->prepare($sql);
    $searchParam = "%" . $search . "%";
    $stmt->bind_param("s", $searchParam);
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($row = $result->fetch_assoc()) {
        $response[] = [
            'id' => $row['id'],
            'name' => $row['Fullname'],
            'skills' => $row['skills']
        ];
    }

    $stmt->close();
}

$conn->close();

// Return JSON response
echo json_encode($response);
?>