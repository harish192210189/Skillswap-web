<?php
$connection = new mysqli("localhost", "root", "", "skillswap", 3306);

if ($connection->connect_error) {
    die("Database connection failed: " . $connection->connect_error);
}

if (!isset($_GET['id'])) {
    echo "User ID not provided.";
    exit();
}

$user_id = $_GET['id'];

// Fetch user details from userregister table
$query = "SELECT Fullname, skills, experience, email, mobile FROM userregister WHERE id = ?";
$stmt = $connection->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "User not found.";
    exit();
}

$user = $result->fetch_assoc();

// Fetch average rating and total number of reviewers
$rating_query = "SELECT AVG(rating) AS avg_rating, COUNT(*) AS total_reviewers FROM reviews WHERE user_id = ?";
$stmt = $connection->prepare($rating_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$rating_result = $stmt->get_result();
$rating_data = $rating_result->fetch_assoc();

$avg_rating = round($rating_data['avg_rating'], 1);
$total_reviewers = $rating_data['total_reviewers'];
$full_stars = floor($avg_rating);
$half_star = ($avg_rating - $full_stars) >= 0.5 ? 1 : 0;

$connection->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($user['Fullname']); ?> - Profile</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: "Poppins", sans-serif;
        }

        body {
            background: #eef2f7;
            color: #333;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .profile-container {
            width: 90%;
            max-width: 1000px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
            overflow: hidden;
            padding: 2rem;
            position: relative;
        }

        .cover-banner {
            width: 100%;
            height: 180px;
            background: linear-gradient(135deg, #0073b1, #003b60);
            border-radius: 12px;
        }

        .profile-header {
            text-align: center;
            padding: 2rem 0;
        }

        h1 {
            font-size: 2.5rem;
            color: #0073b1;
            font-weight: 600;
        }

        .info-box {
            background: #ffffff;
            padding: 1.5rem;
            border-radius: 8px;
            margin: 15px 0;
            box-shadow: 0 3px 6px rgba(0, 0, 0, 0.1);
            transition: 0.3s;
        }

        .info-box:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 12px rgba(0, 0, 0, 0.15);
        }

        h2 {
            font-size: 1.8rem;
            color: #333;
            margin-bottom: 10px;
            border-bottom: 2px solid #0073b1;
            padding-bottom: 5px;
            display: inline-block;
        }

        p {
            font-size: 1.4rem;
            color: #555;
        }

        .stars {
            font-size: 1.8rem;
            color: #f39c12;
            margin-top: 5px;
        }

        .buttons {
            text-align: center;
            margin-top: 20px;
        }

        .btn {
            display: inline-block;
            padding: 12px 24px;
            background: #0073b1;
            color: white;
            font-size: 1.4rem;
            font-weight: 500;
            border-radius: 8px;
            text-decoration: none;
            transition: 0.3s ease;
            margin: 10px 5px;
            box-shadow: 0 3px 8px rgba(0, 0, 0, 0.2);
        }

        .btn:hover {
            background: #005582;
            transform: translateY(-2px);
        }

        @media (max-width: 768px) {
            .profile-container {
                width: 95%;
                padding: 2rem;
            }
        }
    </style>
</head>
<body>
    <div class="profile-container">
        <!-- Cover Banner -->
        <div class="cover-banner"></div>

        <div class="profile-header">
            <h1><?php echo htmlspecialchars($user['Fullname']); ?></h1>
        </div>

        <div class="info-box">
            <h2>Skill & Experience</h2>
            <p><strong>Skill:</strong> <?php echo htmlspecialchars($user['skills']); ?></p>
            <p><strong>Experience:</strong> <?php echo htmlspecialchars($user['experience']); ?> years</p>
        </div>

        <div class="info-box">
            <h2>Rating</h2>
            <div class="stars">
                <?php
                for ($i = 0; $i < $full_stars; $i++) {
                    echo "⭐";
                }
                if ($half_star) {
                    echo "⭐";
                }
                echo " ({$avg_rating}/5) - Rated by {$total_reviewers} reviewer(s)";
                ?>
            </div>
        </div>

        <div class="info-box">
            <h2>Contact Information</h2>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
        </div>

        <div class="buttons">
            <a href="ratings&review.php?id=<?php echo $user_id; ?>" class="btn">Ratings & Review</a>
            <a href="review.php?id=<?php echo $user_id; ?>" class="btn">Give Review</a>
            <a href="discover.html" class="btn">Back</a>
        </div>
    </div>
</body>
</html>
