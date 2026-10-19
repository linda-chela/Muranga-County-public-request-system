<?php
require_once 'config.php';

if (!isAdmin()) {
    header("Location: login.php?role=admin");
    exit;
}

// Action to update request status
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['request_id'], $_POST['status'])) {
    $stmt = $pdo->prepare("UPDATE requests SET status = ? WHERE id = ?");
    $stmt->execute([$_POST['status'], $_POST['request_id']]);
}

// --- BASIC ANALYTICS CALCULATIONS ---
$total_requests = $pdo->query("SELECT COUNT(*) FROM requests")->fetchColumn();
$pending_requests = $pdo->query("SELECT COUNT(*) FROM requests WHERE status = 'Pending'")->fetchColumn();
$in_progress_requests = $pdo->query("SELECT COUNT(*) FROM requests WHERE status = 'In Progress'")->fetchColumn();
$resolved_requests = $pdo->query("SELECT COUNT(*) FROM requests WHERE status = 'Resolved'")->fetchColumn();
$open_requests = $pending_requests + $in_progress_requests;

// Retrieve all requests with user details
$stmt = $pdo->query("SELECT r.*, u.fullname FROM requests r LEFT JOIN users u ON r.user_id = u.id ORDER BY r.created_at DESC");
$requests = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Murang'a County Public Request System</title>
    <link rel="stylesheet" href="style.css">
    <script>
        function changeTextSize(action) {
            let body = document.body;
            let style = window.getComputedStyle(body).getPropertyValue('font-size');
            let currentSize = parseFloat(style);
            if (action === 'increase') body.style.fontSize = (currentSize + 2) + 'px';
            if (action === 'decrease') body.style.fontSize = (currentSize - 2) + 'px';
            if (action === 'reset') body.style.fontSize = '16px';
        }

        function toggleHighContrast() {
            document.body.classList.toggle('high-contrast');
        }
    </script>
</head>
<body>
    <!-- Accessibility Toolbar -->
    <div class="accessibility-bar">
        <div>
            <strong>Accessibility:</strong> Text Size 
            <button class="access-btn" onclick="changeTextSize('decrease')">A-</button>
            <button class="access-btn" onclick="changeTextSize('reset')">A</button>
            <button class="access-btn" onclick="changeTextSize('increase')">A+</button> | 
            <button class="access-btn" onclick="toggleHighContrast()">High Contrast Mode</button>
        </div>
        <span>🔒 Official Admin Environment</span>
    </div>

    <!-- Navigation Header -->
    <div class="navbar">
        <div class="navbar-brand">
            <img src="uploads/muranga_logo.png" alt="County Logo" onerror="this.style.display='none';">
            <h2 style="margin:0; font-size:1.2rem;">Murang'a County Admin Portal</h2>
        </div>
        <div>
            <span style="color:var(--muranga-gold); font-weight:600; margin-right:15px;">Administrator Logged In</span>
            <a href="logout.php" class="btn btn-danger" style="padding:5px 12px; border-radius:4px;">Logout</a>
        </div>
    </div>

    <div class="container">
        
        <!-- BASIC ANALYTICS SUMMARY CARDS -->
        <h2 style="margin-bottom:1rem; color:var(--dark-text);">📊 Case Analytics Summary</h2>
        <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap:18px; margin-bottom:2rem;">
            
            <div class="card" style="border-left: 5px solid #0288d1; margin-bottom:0;">
                <h4 style="color:#666; margin:0; font-size:0.9rem;">Total Requests</h4>
                <p style="font-size:2rem; font-weight:bold; color:#0288d1; margin:5px 0 0 0;"><?= $total_requests ?></p>
            </div>

            <div class="card" style="border-left: 5px solid #ed6c02; margin-bottom:0;">
                <h4 style="color:#666; margin:0; font-size:0.9rem;">Open Cases (Pending + Progress)</h4>
                <p style="font-size:2rem; font-weight:bold; color:#ed6c02; margin:5px 0 0 0;"><?= $open_requests ?></p>
            </div>

            <div class="card" style="border-left: 5px solid #2e7d32; margin-bottom:0;">
                <h4 style="color:#666; margin:0; font-size:0.9rem;">Resolved Cases</h4>
                <p style="font-size:2rem; font-weight:bold; color:#2e7d32; margin:5px 0 0 0;"><?= $resolved_requests ?></p>
            </div>

            <div class="card" style="border-left: 5px solid #d4af37; margin-bottom:0;">
                <h4 style="color:#666; margin:0; font-size:0.9rem;">Resolution Rate</h4>
                <p style="font-size:2rem; font-weight:bold; color:#d4af37; margin:5px 0 0 0;">
                    <?= $total_requests > 0 ? round(($resolved_requests / $total_requests) * 100) : 0 ?>%
                </p>
            </div>

        </div>

        <!-- REQUESTS MANAGEMENT TABLE -->
        <div class="card">
            <h2>Public Requests Management</h2>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Citizen</th>
                        <th>Location</th>
                        <th>Category</th>
                        <th>Description</th>
                        <th>Evidence</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($requests as $req): ?>
                    <tr>
                        <td>#<?= $req['id'] ?></td>
                        <td><?= $req['is_anonymous'] ? '🕵️ Anonymous' : htmlspecialchars($req['fullname'] ?? 'Guest Citizen') ?></td>
                        <td><?= htmlspecialchars($req['constituency'] . ', ' . $req['ward'] . ' (' . $req['village'] . ')') ?></td>
                        <td><?= htmlspecialchars($req['category']) ?></td>
                        <td><?= htmlspecialchars($req['description']) ?></td>
                        <td>
                            <?php if (!empty($req['attachment'])): ?>
                                <a href="uploads/<?= htmlspecialchars($req['attachment']) ?>" target="_blank" style="color:var(--muranga-green); font-weight:bold;">View Photo</a>
                            <?php else: ?>
                                <span style="color:#aaa;">None</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <strong style="color: <?= $req['status'] === 'Resolved' ? 'green' : ($req['status'] === 'In Progress' ? 'blue' : 'orange') ?>;">
                                <?= htmlspecialchars($req['status']) ?>
                            </strong>
                        </td>
                        <td>
                            <form method="POST" style="display:flex; gap:5px;">
                                <input type="hidden" name="request_id" value="<?= $req['id'] ?>">
                                <select name="status" style="padding:4px; border-radius:4px; font-size:0.85rem;">
                                    <option value="Pending" <?= $req['status'] === 'Pending' ? 'selected' : '' ?>>Pending</option>
                                    <option value="In Progress" <?= $req['status'] === 'In Progress' ? 'selected' : '' ?>>In Progress</option>
                                    <option value="Resolved" <?= $req['status'] === 'Resolved' ? 'selected' : '' ?>>Resolved</option>
                                </select>
                                <button type="submit" class="btn btn-success" style="padding:4px 8px; font-size:0.85rem;">Update</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>