<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    echo "<div class='error-message'>Please log in first.</div>";
    exit();
}

$logged_in_user = $_SESSION['user_id'];

$connection = new mysqli("localhost", "root", "", "skillswap", 3306);
if ($connection->connect_error) {
    die("<div class='error-message'>Database connection failed.</div>");
}

// Fetch all accepted connections for the logged-in user
$query = "SELECT u.id, u.Fullname FROM connections c 
          JOIN userregister u ON 
          (c.sender_id = u.id OR c.receiver_id = u.id) 
          WHERE (c.sender_id = ? OR c.receiver_id = ?) 
          AND c.status = 'accepted' 
          AND u.id != ?";

$stmt = $connection->prepare($query);
$stmt->bind_param("iii", $logged_in_user, $logged_in_user, $logged_in_user);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chats</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap');
        
        body {
            font-family: 'Poppins', sans-serif;
            background: #e5ddd5;
            margin: 0;
            display: flex;
            flex-direction: column;
            height: 100vh;
        }
        .header {
            background: #075e54;
            color: white;
            padding: 15px;
            text-align: center;
            font-size: 20px;
            font-weight: 600;
            position: relative;
        }
        .home-button {
            position: absolute;
            left: 20px;
            top: 15px;
            background: white;
            color: #075e54;
            padding: 8px 15px;
            border-radius: 5px;
            text-decoration: none;
            font-weight: 500;
        }
        .home-button:hover {
            background: #f0f0f0;
        }
        .chat-container {
            display: flex;
            width: 100%;
            height: 100%;
        }
        .chat-list-section {
            width: 30%;
            background: #fff;
            padding: 20px;
            border-right: 1px solid #ccc;
            overflow-y: auto;
        }
        .chat-window {
            width: 70%;
            background: #f7f7f7;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 18px;
            color: #555;
        }
        h2 {
            color: #075e54;
            font-weight: 600;
            text-align: center;
        }
        .chat-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .chat-list li {
            padding: 15px;
            border-bottom: 1px solid #ddd;
            transition: background 0.3s;
        }
        .chat-list li:hover {
            background: #f0f0f0;
        }
        .chat-list a {
            text-decoration: none;
            color: #000;
            font-size: 16px;
            font-weight: 500;
            display: block;
        }
        .error-message {
            color: red;
            font-size: 16px;
            text-align: center;
        }
        .search-box {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 14px;
        }
    </style>
    <script>
        function searchChats() {
            let input = document.getElementById('search').value.toLowerCase();
            let chatItems = document.querySelectorAll('.chat-list li');
            
            chatItems.forEach(item => {
                let name = item.textContent.toLowerCase();
                if (name.includes(input)) {
                    item.style.display = "block";
                } else {
                    item.style.display = "none";
                }
            });
        }
    </script>
</head>
<body>
    <div class="header">
        <a href="home.php" class="home-button">Home</a>
        Chats
    </div>
    <div class="chat-container">
        <div class="chat-list-section">
            <h2>Chats</h2>
            <input type="text" id="search" class="search-box" placeholder="Search chats..." onkeyup="searchChats()">
            <ul class="chat-list">
                <?php
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<li><a href='chat_window.php?id=" . $row['id'] . "' target='chat-frame'>" . htmlspecialchars($row['Fullname']) . "</a></li>";
                    }
                } else {
                    echo "<li style='text-align: center; color: #888;'>No accepted connections yet.</li>";
                }
                ?>
            </ul>
        </div>
        <div class="chat-window">
            <iframe name="chat-frame" width="100%" height="100%" frameborder="0" style="background: #fff;"></iframe>
        </div>
    </div>
</body>
</html>

<?php $connection->close(); ?>
