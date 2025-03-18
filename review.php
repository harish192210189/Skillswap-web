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

// Fetch user details
$query = "SELECT Fullname, skills, experience, email FROM userregister WHERE id = ?";
$stmt = $connection->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "User not found.";
    exit();
}

$user = $result->fetch_assoc();

// Fetch reviews
$reviews_query = "SELECT reviewer_name, rating, review_text, created_at FROM reviews WHERE user_id = ? ORDER BY created_at DESC";
$stmt_reviews = $connection->prepare($reviews_query);
$stmt_reviews->bind_param("i", $user_id);
$stmt_reviews->execute();
$reviews_result = $stmt_reviews->get_result();

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
            background: #f3f2ef;
            color: #333;
            display: flex;
            justify-content: center;
            padding: 2rem;
        }

        .profile-container {
            width: 90%;
            max-width: 1200px;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
            overflow: hidden;
            padding: 3rem;
        }

        .cover-photo {
            width: 100%;
            height: 120px;
            background: linear-gradient(135deg, #0073b1, #005582);
        }

        .profile-content {
            padding: 3rem;
        }

        h1 {
            font-size: 3.5rem;
            color: #0073b1;
            font-weight: bold;
            margin-bottom: 10px;
        }

        h2 {
            font-size: 2.5rem;
            margin-top: 20px;
            color: #333;
        }

        p {
            font-size: 1.5rem;
            color: #444;
            margin: 10px 0;
        }

        .btn {
            display: inline-block;
            padding: 14px 28px;
            background: #0073b1;
            color: white;
            font-size: 1.8rem;
            border-radius: 6px;
            text-decoration: none;
            transition: 0.3s ease;
            margin-top: 20px;
        }

        .btn:hover {
            background: #005582;
        }

        .review-section {
            margin-top: 40px;
            padding: 20px;
            background: #f9f9f9;
            border-radius: 8px;
        }

        .review {
            border-bottom: 1px solid #ccc;
            padding: 10px 0;
        }

        .review:last-child {
            border-bottom: none;
        }

        .star-rating {
            color: #f39c12;
            font-size: 1.5rem;
        }

        .review-form input, .review-form textarea, .review-form select {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border-radius: 5px;
            border: 1px solid #ccc;
            font-size: 1.2rem;
        }

        .review-form button {
            width: 100%;
            padding: 12px;
            background: #0073b1;
            color: white;
            font-size: 1.5rem;
            border-radius: 6px;
            border: none;
            cursor: pointer;
        }

        .review-form button:hover {
            background: #005582;
        }

        @media (max-width: 768px) {
            .profile-container {
                width: 95%;
            }
        }
    </style>
</head>
<body>
    <div class="profile-container">
        <div class="cover-photo"></div>

        <div class="profile-content">
            <h1><?php echo htmlspecialchars($user['Fullname']); ?></h1>
            <p><strong>Skill:</strong> <?php echo htmlspecialchars($user['skills']); ?></p>
            <p><strong>Experience:</strong> <?php echo htmlspecialchars($user['experience']); ?> years</p>

            <h2>Contact Information</h2>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>

            <a href="discover.html" class="btn">Back</a>

            <!-- Review Section -->
            <div class="review-section">
                <h2>Reviews & Ratings</h2>

                <?php while ($review = $reviews_result->fetch_assoc()) : ?>
                    <div class="review">
                        <p><strong><?php echo htmlspecialchars($review['reviewer_name']); ?></strong> - 
                        <span class="star-rating"><?php echo str_repeat("★", $review['rating']); ?></span></p>
                        <p><?php echo htmlspecialchars($review['review_text']); ?></p>
                        <p><em><?php echo $review['created_at']; ?></em></p>
                    </div>
                <?php endwhile; ?>

                <!-- Review Form -->
                <h2>Leave a Review</h2>
                <form action="submit_review.php" method="POST" class="review-form">
                    <input type="hidden" name="user_id" value="<?php echo $user_id; ?>">
                    <input type="text" name="reviewer_name" placeholder="Your Name" required>
                    <select name="rating" required>
                        <option value="5">5 - Excellent</option>
                        <option value="4">4 - Good</option>
                        <option value="3">3 - Average</option>
                        <option value="2">2 - Poor</option>
                        <option value="1">1 - Bad</option>
                    </select>
                    <textarea name="review_text" placeholder="Write your review..." rows="4" required></textarea>
                    <button type="submit">Submit Review</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>