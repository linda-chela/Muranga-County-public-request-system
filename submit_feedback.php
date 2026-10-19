<?php
require_once 'config.php';

if (!isLoggedIn()) {
    header("Location: login.php");
    exit;
}

$request_id = $_GET['request_id'] ?? null;
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $request_id = $_POST['request_id'];
    $rating = intval($_POST['rating']);
    $comments = trim($_POST['comments']);

    $stmt = $pdo->prepare("INSERT INTO feedback (request_id, user_id, rating, comments) VALUES (?, ?, ?, ?)");
    if ($stmt->execute([$request_id, $_SESSION['user_id'], $rating, $comments])) {
        $message = "Thank you for your feedback!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Submit Feedback - Murang'a County</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="navbar">
        <h2>Murang'a County Public Request System</h2>
        <div><a href="dashboard.php">Dashboard</a></div>
    </div>

    <div class="container" style="max-width:500px;">
        <div class="card">
            <h2>Rate Service Resolution</h2>
            <?php if ($message): ?>
                <p style="color:green; font-weight:bold;"><?= $message ?></p>
                <p style="margin-top:1rem;"><a href="dashboard.php" class="btn btn-success">Back to Dashboard</a></p>
            <?php else: ?>
                <form method="POST">
                    <input type="hidden" name="request_id" value="<?= htmlspecialchars($request_id) ?>">
                    
                    <div class="form-group">
                        <label>Satisfaction Rating (1 - 5 Stars)</label>
                        <select name="rating" required>
                            <option value="5">⭐⭐⭐⭐⭐ 5 - Excellent Resolution</option>
                            <option value="4">⭐⭐⭐⭐ 4 - Good</option>
                            <option value="3">⭐⭐⭐ 3 - Satisfactory</option>
                            <option value="2">⭐⭐ 2 - Poor</option>
                            <option value="1">⭐ 1 - Unsatisfactory</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Comments / Suggestions</label>
                        <textarea name="comments" rows="4" placeholder="Share your experience..."></textarea>
                    </div>

                    <button type="submit" class="btn btn-success">Submit Feedback</button>
                </form>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>