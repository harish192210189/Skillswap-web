<?php
session_start();

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

// Database credentials
$servername = "localhost";
$username = "root";  
$password = "";  

// Connect to database
$conn = new mysqli($servername, $username, $password, "skillswap", 3306);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch logged-in user details
$userId = $_SESSION['user_id'];
$sql_user = "SELECT Fullname FROM userregister WHERE id = ?";
$stmt_user = $conn->prepare($sql_user);
$stmt_user->bind_param("i", $userId);
$stmt_user->execute();
$result_user = $stmt_user->get_result();

if ($result_user->num_rows > 0) {
    $user = $result_user->fetch_assoc();
    $username = htmlspecialchars($user['Fullname']);
} else {
    $username = "User";
}

// Fetch the count of pending swap requests
$sql_pending_requests = "SELECT COUNT(*) AS pending_count FROM connections WHERE receiver_id = ? AND status = 'pending'";
$stmt_pending = $conn->prepare($sql_pending_requests);
$stmt_pending->bind_param("i", $userId);
$stmt_pending->execute();
$result_pending = $stmt_pending->get_result();
$pending_count = 0;

if ($result_pending->num_rows > 0) {
    $row = $result_pending->fetch_assoc();
    $pending_count = $row['pending_count'];
}

// Fetch count of new reviews (last 24 hours)
$sql_review_notifications = "SELECT COUNT(*) AS new_reviews FROM reviews WHERE user_id = ?";
$stmt_review_notifications = $conn->prepare($sql_review_notifications);
$stmt_review_notifications->bind_param("i", $userId);
$stmt_review_notifications->execute();
$result_review_notifications = $stmt_review_notifications->get_result();
$new_reviews = 0;

if ($result_review_notifications->num_rows > 0) {
    $row = $result_review_notifications->fetch_assoc();
    $new_reviews = $row['new_reviews'];
}

$stmt_review_notifications->close();
$stmt_pending->close();
$stmt_user->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Skill Swap - Home</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #D0E8FF, #FFFFFF);
            color: #333;
            margin: 0;
            padding: 0;
            text-align: center;
        }
        header {
            background: #4A90E2;
            color: #fff;
            padding: 1rem;
            font-size: 1.5rem;
            font-weight: bold;
        }
        nav {
            background: #3178C6;
            padding: 0.8rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        nav .nav-links {
            display: flex;
            gap: 1.5rem;
            position: relative;
        }
        nav a {
            color: #fff;
            text-decoration: none;
            font-size: 1.1rem;
            transition: color 0.3s;
            position: relative;
        }
        nav a:hover {
            color: #FFD700;
        }
        .logout-btn {
            background: #FF4D4D;
            padding: 0.6rem 1rem;
            border-radius: 6px;
            color: white;
            font-size: 1rem;
            text-decoration: none;
            transition: background 0.3s;
        }
        .logout-btn:hover {
            background: #D93636;
        }
        .container {
            margin: 2rem auto;
            padding: 2rem;
            max-width: 900px;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }
        h2 {
            color: #4A90E2;
            margin-bottom: 1rem;
        }
        .search-bar {
            display: flex;
            justify-content: center;
            margin: 1.5rem 0;
        }
        .search-bar input {
            padding: 0.8rem;
            width: 60%;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 1rem;
        }
        .search-bar button {
            padding: 0.8rem;
            background: #5CA9FF;
            border: none;
            color: #fff;
            font-size: 1rem;
            cursor: pointer;
            border-radius: 6px;
            margin-left: 0.5rem;
            transition: background 0.3s;
        }
        .search-bar button:hover {
            background: #3178C6;
        }
        .buttons {
            display: flex;
            justify-content: center;
            gap: 1.5rem;
            margin-top: 2rem;
        }
        .btn {
            padding: 1rem 2rem;
            background: #5CA9FF;
            color: #fff;
            border: none;
            border-radius: 8px;
            font-size: 1.2rem;
            cursor: pointer;
            text-decoration: none;
            transition: background 0.3s, transform 0.2s;
        }
        .btn:hover {
            background: #4891E3;
            transform: scale(1.05);
        }
        .latest-skills {
            margin-top: 2rem;
            text-align: left;
        }
        .skill-box {
            background: #E3F2FD;
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 0.8rem;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        .call-to-action {
            background: #EAF6FF;
            padding: 1.5rem;
            margin-top: 2rem;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }
        .pending-count {
            background: red;
            color: white;
            padding: 3px 7px;
            border-radius: 50%;
            font-size: 0.8rem;
            position: absolute;
            top: -12px;
            right: -18px;
        }
        #bellcount{
            background: red;
            color: white;
            padding: 3px 7px;
            border-radius: 50%;
            font-size: 0.8rem;
            position: absolute;
            top: -12px;
            right: 0;

        }
        .swap-link {
            position: relative;
            display: inline-block;
            padding: 5px;
        }
        .notification-link {
          position: relative;
          color: white;
          font-size: 1.2rem;
          margin-right: 1.5rem;
        }

        .notification-count {
            background: red;
            color: white;
            padding: 3px 7px;
            border-radius: 50%;
            font-size: 0.8rem;
            position: absolute;
            top: -8px;
            right: -10px;
        }
        .fa-bell{
            margin-left: -50px;
        }
        footer {
            background: #4A90E2;
            color: #fff;
            padding: 1rem;
            margin-top: 2rem;
        }
        #btn-prof{
            padding: 0.5rem 1rem;
            background: #5CA9FF;
            color: #fff;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            cursor: pointer;
            text-decoration: none;
            transition: background 0.3s, transform 0.2s;
        }
    </style>
</head>
<body>
    <header>Skill Swap</header>
    
    <nav>
        <div class="nav-links">
            <a href="home.php">Home</a>
            <a href="discover.html">Discover Skills</a>
            <a href="connect.html" class="swap-link">
                <?php if ($pending_count > 0) : ?>
                    <span class="pending-count"><?php echo $pending_count; ?></span>
                <?php endif; ?>
                Swap Requests
            </a>
            <a href="chat.php">Chats</a>
        </div>
        <div class="prof">
            <a href="reviews.php" class="swap-link">
            <i class="fa-solid fa-bell"></i>
            <?php if ($new_reviews > 0) : ?>
            <span class="pending-count" id="bellcount"><?php echo $new_reviews; ?></span>
            <?php endif; ?>
            </a>

            <a href="profile.html"><i class="fa-solid fa-user"></i></a>
            <a href="logout.php" class="logout-btn">Logout</a>
        </div>
    </nav>

    <div class="container">
        <h2>Welcome to Skill Swap, <?php echo $username; ?>!</h2>
        <p>Connect with people to share and learn new skills. Explore opportunities, network, and grow together!</p>
        
        <div class="search-bar">
            <input type="text" id="search-input" placeholder="Search for a skill..." />
            <button id="search-btn">Search</button>
        </div>

        <div id="search-results"></div>

        <div class="buttons">
            <a href="discover.html" class="btn">Discover Skills</a>
            <a href="connect.html" class="btn">Connect & Collaborate</a>
        </div>

        <?php include 'latest_skills.php'; ?>

    </div>

    <footer>
        <p>&copy; 2025 Skill Swap. All Rights Reserved.</p>
    </footer>
    <script>
    document.getElementById('search-btn').addEventListener('click', function() {
    let query = document.getElementById('search-input').value.trim();

    if (query !== '') {
        fetch('searchbar.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'query=' + encodeURIComponent(query)
        })
        .then(response => response.json())
        .then(data => {
            let resultsDiv = document.getElementById('search-results');
            resultsDiv.innerHTML = '';

            if (data.length > 0) {
                data.forEach(user => {
                    resultsDiv.innerHTML += `<div class="skill-box"><strong>${user.name}</strong> - ${user.skills} <a id="btn-prof" href="user_profile.php?id=${user.id}">View Profile</a></div>`;

                });
            } else {
                resultsDiv.innerHTML = '<p>No skills found.</p>';
            }
        })
        .catch(error => console.error('Error:', error));
    }
});
</script>


</body>
</html>