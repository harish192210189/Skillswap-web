<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    echo "<div class='error-message'>Please log in first.</div>";
    exit();
}

$logged_in_user = $_SESSION['user_id'];
$receiver_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($receiver_id == 0) {
    echo "<div class='error-message'>Invalid chat user.</div>";
    exit();
}

$connection = new mysqli("localhost", "root", "", "skillswap", 3306);
if ($connection->connect_error) {
    die("<div class='error-message'>Database connection failed.</div>");
}

// Fetch receiver details
$query = "SELECT Fullname FROM userregister WHERE id = ?";
$stmt = $connection->prepare($query);
$stmt->bind_param("i", $receiver_id);
$stmt->execute();
$result = $stmt->get_result();
$receiver = $result->fetch_assoc();

if (!$receiver) {
    echo "<div class='error-message'>User not found.</div>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat with <?php echo htmlspecialchars($receiver['Fullname']); ?></title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap');
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Poppins', sans-serif;
            background: #e5ddd5;
            display: flex;
            height: 100vh;
        }
        .chat-container {
            flex: 1;
            height: 100vh;
            display: flex;
            flex-direction: column;
            background: #fff;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
        }
        .chat-header {
            padding: 15px;
            background: #075e54;
            color: white;
            font-size: 18px;
            font-weight: bold;
            text-align: center;
        }
        .chat-box {
            flex: 1;
            padding: 15px;
            overflow-y: auto;
            background: #ece5dd;
            display: flex;
            flex-direction: column;
        }
        .message {
            padding: 10px 15px;
            border-radius: 10px;
            margin: 5px 0;
            max-width: 60%;
            font-size: 14px;
        }
        .sent {
            background: #dcf8c6;
            align-self: flex-end;
        }
        .received {
            background: #ffffff;
            align-self: flex-start;
        }
        .chat-footer {
            display: flex;
            padding: 10px;
            background: #f7f7f7;
            border-top: 1px solid #ccc;
        }
        .input-box {
            flex: 1;
            padding: 10px;
            border: none;
            border-radius: 20px;
            outline: none;
            font-size: 14px;
            background: #fff;
        }
        .send-btn {
            padding: 8px 12px;
            background: #075e54;
            color: white;
            border: none;
            border-radius: 50%;
            cursor: pointer;
            margin-left: 10px;
            font-size: 16px;
        }
        .send-btn:hover {
            background: #054c44;
        }
        .error-message {
            color: red;
            font-size: 16px;
            text-align: center;
            margin: 10px;
        }
    </style>
</head>
<body>
    <div class="chat-container">
        <div class="chat-header">Chat with <?php echo htmlspecialchars($receiver['Fullname']); ?></div>
        <div class="chat-box" id="chatBox">
            <!-- Messages will be loaded here via AJAX -->
        </div>
        <div class="chat-footer">
            <input type="text" id="message" class="input-box" placeholder="Type a message...">
            <button class="send-btn" onclick="sendMessage()">➤</button>
        </div>
    </div>

    <script>
        function sendMessage() {
            let message = document.getElementById("message").value;
            if (message.trim() === "") return;

            let xhr = new XMLHttpRequest();
            xhr.open("POST", "send_message.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.onload = function () {
                if (this.status == 200) {
                    document.getElementById("message").value = "";
                    loadMessages();
                }
            };
            xhr.send("receiver_id=<?php echo $receiver_id; ?>&message=" + encodeURIComponent(message));
        }

        function loadMessages() {
            let xhr = new XMLHttpRequest();
            xhr.open("GET", "fetch_messages.php?receiver_id=<?php echo $receiver_id; ?>", true);
            xhr.onload = function () {
                if (this.status == 200) {
                    document.getElementById("chatBox").innerHTML = this.responseText;
                }
            };
            xhr.send();
        }

        setInterval(loadMessages, 3000);
        loadMessages();
    </script>
</body>
</html>

<?php $connection->close(); ?>
