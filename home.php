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
    <title>SkillSwap - Home</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #f5f7fa, #e4e7eb);
            color: #2d3748;
            margin: 0;
            padding: 0;
            min-height: 100vh;
        }

        header {
            background: linear-gradient(to right, #4a90e2, #357abd);
            color: #fff;
            padding: 1.2rem;
            font-size: 1.8rem;
            font-weight: 600;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        nav {
            background: #fff;
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
        }

        nav .nav-links {
            display: flex;
            gap: 2rem;
            align-items: center;
        }

        nav a {
            color: #4a5568;
            text-decoration: none;
            font-size: 1.1rem;
            font-weight: 500;
            transition: all 0.3s ease;
            padding: 0.5rem 1rem;
            border-radius: 6px;
        }

        nav a:hover {
            color: #4a90e2;
            background: #f7fafc;
        }

        .notification-badge {
            background: #ff4d4d;
            color: white;
            padding: 0.2rem 0.6rem;
            border-radius: 12px;
            font-size: 0.8rem;
            position: absolute;
            top: -8px;
            right: -12px;
        }

        .logout-btn {
            background: #ff4d4d;
            padding: 0.8rem 1.5rem;
            border-radius: 8px;
            color: white;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.3s ease;
            box-shadow: 0 2px 4px rgba(255, 77, 77, 0.2);
        }

        .logout-btn:hover {
            background: #e63939;
            transform: translateY(-1px);
        }

        .container {
            margin: 2rem auto;
            padding: 2.5rem;
            max-width: 1100px;
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        }

        h2 {
            color: #2d3748;
            margin-bottom: 1.5rem;
            font-size: 2rem;
            font-weight: 600;
        }

        .search-bar {
            display: flex;
            justify-content: center;
            margin: 2rem 0;
            gap: 1rem;
        }

        .search-bar input {
            padding: 1rem 1.5rem;
            width: 60%;
            border: 2px solid #e2e8f0;
            border-radius: 10px;
            font-size: 1.1rem;
            transition: all 0.3s ease;
        }

        .search-bar input:focus {
            outline: none;
            border-color: #4a90e2;
            box-shadow: 0 0 0 3px rgba(74, 144, 226, 0.1);
        }

        .search-bar button {
            padding: 1rem 2rem;
            background: #4a90e2;
            border: none;
            color: #fff;
            font-size: 1.1rem;
            font-weight: 500;
            cursor: pointer;
            border-radius: 10px;
            transition: all 0.3s ease;
        }

        .search-bar button:hover {
            background: #357abd;
            transform: translateY(-1px);
        }

        .buttons {
            display: flex;
            justify-content: center;
            gap: 2rem;
            margin-top: 2.5rem;
        }

        .btn {
            padding: 1.2rem 2.5rem;
            background: #4a90e2;
            color: #fff;
            border: none;
            border-radius: 10px;
            font-size: 1.2rem;
            font-weight: 500;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(74, 144, 226, 0.2);
        }

        .btn:hover {
            background: #357abd;
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(74, 144, 226, 0.3);
        }

        .latest-skills {
            margin-top: 3rem;
            text-align: left;
        }

        .skill-box {
            background: #f8fafc;
            padding: 1.5rem;
            border-radius: 12px;
            margin-bottom: 1rem;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.06);
            transition: all 0.3s ease;
            border: 1px solid #e2e8f0;
        }

        .skill-box:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
        }

        .call-to-action {
            background: linear-gradient(135deg, #edf2f7, #e2e8f0);
            padding: 2rem;
            margin-top: 3rem;
            border-radius: 12px;
            text-align: center;
        }

        .profile-section {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .welcome-text {
            font-size: 1.4rem;
            color: #2d3748;
            font-weight: 500;
        }

        #btn-prof {
            padding: 0.8rem 1.5rem;
            background: #4a90e2;
            color: #fff;
            border: none;
            border-radius: 8px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        #btn-prof:hover {
            background: #357abd;
            transform: translateY(-1px);
        }

        @media (max-width: 768px) {
            .container {
                padding: 1.5rem;
                margin: 1rem;
            }

            .search-bar input {
                width: 100%;
            }

            .buttons {
                flex-direction: column;
                gap: 1rem;
            }

            nav {
                flex-direction: column;
                gap: 1rem;
            }

            nav .nav-links {
                flex-wrap: wrap;
                justify-content: center;
            }
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
                    <span class="notification-badge"><?php echo $pending_count; ?></span>
                <?php endif; ?>
                Swap Requests
            </a>
            <a href="chat.php">Chats</a>
        </div>
        <div class="prof">
            <a href="reviews.php" class="swap-link">
            <i class="fa-solid fa-bell"></i>
            <?php if ($new_reviews > 0) : ?>
            <span class="notification-badge"><?php echo $new_reviews; ?></span>
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
            <a href="connect.html" class="btn">Swap Requests</a>
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
