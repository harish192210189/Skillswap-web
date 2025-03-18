<?php
session_start();

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "skillswap"; 
$port = 3306;

$conn = new mysqli($servername, $username, $password, $dbname, $port);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get logged-in user ID
$userId = $_SESSION['user_id'];

// Fetch reviews *only for the logged-in user*
$sql = "SELECT reviewer_name, rating, review_text, created_at 
        FROM reviews 
        WHERE user_id = ?  
        ORDER BY created_at DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Reviews</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; text-align: center; }
        .container { max-width: 600px; margin: auto; padding: 20px; background: #fff; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
        .review-box { border: 1px solid #ccc; padding: 10px; margin-bottom: 10px; border-radius: 5px; text-align: left; }
        .reviewer { font-weight: bold; }
        .rating { color: #FFD700; } /* Gold color for rating */
        .btn { padding: 10px 15px; background: #ff4444; color: #fff; border: none; border-radius: 5px; cursor: pointer; margin-top: 10px; }
        .btn:hover { background: #cc0000; }
    </style>
</head>
<body>
    <div class="container">
        <h2>My Reviews</h2>

        <div id="reviewsContainer">
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <div class="review-box">
                        <p class="reviewer"><?php echo htmlspecialchars($row['reviewer_name']); ?>:</p>
                        <p class="rating">Rating: <?php echo $row['rating']; ?> ⭐</p>
                        <p><?php echo htmlspecialchars($row['review_text']); ?></p>
                        <small>Reviewed on: <?php echo $row['created_at']; ?></small>
                    </div>
                <?php endwhile; ?>
                <button class="btn" id="clearReviewsBtn">Clear All Reviews</button>
            <?php else: ?>
                <p>No reviews yet.</p>
            <?php endif; ?>
        </div>

        <?php 
        $stmt->close();
        $conn->close();
        ?>
    </div>

    <script>
        $(document).ready(function () {
            $("#clearReviewsBtn").click(function () {
                $.ajax({
                    url: "clear_reviews.php",
                    type: "POST",
                    success: function (response) {
                        let result = JSON.parse(response);
                        if (result.success) {
                            $("#reviewsContainer").html("<p>No reviews yet.</p>");
                        } else {
                            alert("Error: " + result.message);
                        }
                    }
                });
            });
        });
    </script>
</body>
</html>
