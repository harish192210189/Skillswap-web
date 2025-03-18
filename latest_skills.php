<?php
$connection = new mysqli("localhost", "root", "", "skillswap", 3306);

if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
}

// Fetch the last 5 registered users with their skills and experience
$query = "SELECT Fullname AS name, skills AS skill, experience FROM userregister ORDER BY id DESC LIMIT 5";
$result = $connection->query($query);

$skills = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $skills[] = $row;
    }
}

$connection->close();
?>

<div class="latest-skills">
    <h2>Latest Shared Skills</h2>
    <?php if (!empty($skills)): ?>
        <?php foreach ($skills as $skill): ?>
            <div class="skill-box">
                <strong><?php echo htmlspecialchars($skill['name']); ?>:</strong>
                <?php echo htmlspecialchars($skill['skill']); ?> - 
                "<?php echo htmlspecialchars($skill['experience']); ?>"
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>No recent registrations yet.</p>
    <?php endif; ?>
</div>
