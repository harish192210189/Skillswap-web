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

// Fetch user reviews
$query = "SELECT reviewer_name, rating, review_text, created_at FROM reviews WHERE user_id = ? ORDER BY created_at DESC";
$stmt = $connection->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$reviews = [];
while ($row = $result->fetch_assoc()) {
    $reviews[] = $row;
}

$connection->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Reviews</title>
    <style>
        * {
            font-family: "Poppins", sans-serif;
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            background: #f3f2ef;
            display: flex;
            justify-content: center;
            padding: 2rem;
        }
        .container {
            width: 80%;
            max-width: 800px;
            background: white;
            padding: 2rem;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
        }
        h1 {
            font-size: 2.5rem;
            text-align: center;
            color: #0073b1;
        }
        .review {
            border-bottom: 1px solid #ddd;
            padding: 1rem 0;
        }
        .review:last-child {
            border-bottom: none;
        }
        .reviewer {
            font-weight: bold;
            color: #333;
        }
        .rating {
            color: #f39c12;
        }
        .date {
            font-size: 0.9rem;
            color: #777;
        }
        .back-btn {
            display: inline-block;
            padding: 10px 20px;
            background: #0073b1;
            color: white;
            font-size: 1.2rem;
            border-radius: 6px;
            text-decoration: none;
            margin-top: 20px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>User Reviews</h1>

        <?php if (empty($reviews)): ?>
            <p>No reviews yet for this user.</p>
        <?php else: ?>
            <?php foreach ($reviews as $review): ?>
                <div class="review">
                    <p class="reviewer"><?php echo htmlspecialchars($review['reviewer_name']); ?></p>
                    <p class="rating">Rating: <?php echo str_repeat("⭐", $review['rating']); ?></p>
                    <p><?php echo htmlspecialchars($review['review_text']); ?></p>
                    <p class="date"><?php echo date("F j, Y", strtotime($review['created_at'])); ?></p>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

        <a href="user_profile.php?id=<?php echo $user_id; ?>" class="back-btn">Back</a>
    </div>
</body>
</html>