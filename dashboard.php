<?php
require_once 'config.php';

$message = '';
$error = '';

// Handle Delete Request (Only if Pending)
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $req_id = intval($_GET['id']);
    $user_id = $_SESSION['user_id'] ?? null;

    $stmt = $pdo->prepare("DELETE FROM requests WHERE id = ? AND (user_id = ? OR ? IS NULL) AND status = 'Pending'");
    if ($stmt->execute([$req_id, $user_id, $user_id])) {
        $message = "Request #" . $req_id . " deleted successfully.";
    } else {
        $error = "Unable to delete request or request is already being processed.";
    }
}

// Handle Form Submission (New & Edit)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $request_id = $_POST['request_id'] ?? null;
    $constituency = $_POST['constituency'] ?? '';
    $ward = $_POST['ward'] ?? '';
    $village = trim($_POST['village']);
    $category = $_POST['category'];
    $description = trim($_POST['description']);
    $is_anonymous = isset($_POST['is_anonymous']) ? 1 : 0;
    $user_id = isLoggedIn() ? $_SESSION['user_id'] : null;

    $attachment = null;
    if (!empty($_FILES['attachment']['name'])) {
        $targetDir = "uploads/";
        if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);
        $attachment = time() . '_' . basename($_FILES['attachment']['name']);
        move_uploaded_file($_FILES['attachment']['tmp_name'], $targetDir . $attachment);
    }

    if (empty($constituency) || empty($ward) || empty($village) || empty($description)) {
        $error = "Please fill in all location and description fields.";
    } else {
        if ($request_id) {
            // Edit existing pending request
            $stmt = $pdo->prepare("UPDATE requests SET constituency = ?, ward = ?, village = ?, category = ?, description = ? WHERE id = ? AND status = 'Pending'");
            $stmt->execute([$constituency, $ward, $village, $category, $description, $request_id]);
            $message = "Request updated successfully!";
        } else {
            // Insert new request with local datetime
            $stmt = $pdo->prepare("INSERT INTO requests (user_id, is_anonymous, constituency, ward, village, category, description, attachment, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, datetime('now', 'localtime'))");
            $stmt->execute([$user_id, $is_anonymous, $constituency, $ward, $village, $category, $description, $attachment]);
            $message = "Your request has been submitted successfully!";
        }
    }
}

// Fetch single request for Editing
$edit_request = null;
if (isset($_GET['action']) && $_GET['action'] === 'edit' && isset($_GET['id'])) {
    $stmt = $pdo->prepare("SELECT * FROM requests WHERE id = ? AND status = 'Pending'");
    $stmt->execute([intval($_GET['id'])]);
    $edit_request = $stmt->fetch();
}

// Fetch requests for logged-in citizen
$my_requests = [];
if (isLoggedIn() && !isAdmin()) {
    $stmt = $pdo->prepare("SELECT * FROM requests WHERE user_id = ? ORDER BY created_at DESC");
    $stmt->execute([$_SESSION['user_id']]);
    $my_requests = $stmt->fetchAll();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Murang'a County Public Request System</title>
    <link rel="stylesheet" href="style.css">
    <script>
        const wardsData = {
            "Kangema": ["Kakang'u", "Kiru", "Muguru"],
            "Mathioya": ["Gitugi", "Kirimunge", "Kamacharia"],
            "Kigumo": ["Kahumbu", "Kangari", "Kinyona", "Muthithi", "Kigumo Ward"],
            "Kandara": ["Gaichanjiru", "Ithiru", "Muruka", "Ng'araria", "Ruchu"],
            "Gatanga": ["Gatumu", "Kihumbu-ini", "Mugumo-ini", "Ithanga", "Kakuzi/Mitubiri"],
            "Maragua": ["Ichagaki", "Kamahuha", "Kambiti", "Kirimiri", "Makuyu", "Nginda"],
            "Gatanga/Town": ["Township", "Mbiri", "Mugoiri", "Gaturi"]
        };

        function updateWards(selectedWard = '') {
            const constituencySelect = document.getElementById("constituency");
            const wardSelect = document.getElementById("ward");
            const selectedConstituency = constituencySelect.value;

            wardSelect.innerHTML = "<option value=''>-- Select Ward --</option>";

            if (selectedConstituency && wardsData[selectedConstituency]) {
                wardsData[selectedConstituency].forEach(ward => {
                    const option = document.createElement("option");
                    option.value = ward;
                    option.text = ward;
                    if (ward === selectedWard) option.selected = true;
                    wardSelect.appendChild(option);
                });
            }
        }

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
            <button class="access-btn" onclick="toggleHighContrast()">High Contrast</button>
        </div>
        <span>📞 County Support: 0800 720 000</span>
    </div>

    <!-- Navigation Header -->
    <div class="navbar">
        <div class="navbar-brand">
            <img src="uploads/muranga_logo.png" alt="County Logo" onerror="this.style.display='none';">
            <h2 style="margin:0; font-size:1.2rem;">Murang'a County Public Request System</h2>
        </div>
        <div>
            <a href="index.php">Home</a>
            <?php if (isLoggedIn()): ?>
                <!-- Clean Welcome Greeting without Brackets -->
                <span style="color:#d4af37; margin-left:15px; font-weight:600;">Welcome <?= htmlspecialchars($_SESSION['fullname']) ?></span>
                <a href="logout.php" style="margin-left:10px; background:#c62828; padding:5px 12px; border-radius:4px;">Logout</a>
            <?php else: ?>
                <a href="login.php" style="margin-left:10px;">Login</a>
            <?php endif; ?>
        </div>
    </div>

    <div class="container">
        <div class="card">
            <h2><?= $edit_request ? "Edit Request #" . $edit_request['id'] : "Submit a Public Service Request" ?></h2>
            
            <?php if ($message): ?><p style="color: green; font-weight:bold; margin-bottom:1rem;"><?= $message ?></p><?php endif; ?>
            <?php if ($error): ?><p style="color: red; font-weight:bold; margin-bottom:1rem;"><?= $error ?></p><?php endif; ?>

            <form method="POST" enctype="multipart/form-data">
                <?php if ($edit_request): ?>
                    <input type="hidden" name="request_id" value="<?= $edit_request['id'] ?>">
                <?php endif; ?>

                <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap:15px;">
                    <div class="form-group">
                        <label>Constituency</label>
                        <select id="constituency" name="constituency" onchange="updateWards()" required>
                            <option value="">-- Select Constituency --</option>
                            <?php foreach (["Kangema", "Mathioya", "Kigumo", "Kandara", "Gatanga", "Maragua", "Gatanga/Town"] as $c): ?>
                                <option value="<?= $c ?>" <?= ($edit_request && $edit_request['constituency'] === $c) ? 'selected' : '' ?>><?= $c ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Ward</label>
                        <select id="ward" name="ward" required>
                            <option value="">-- Select Ward --</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Village / Specific Area</label>
                        <input type="text" name="village" value="<?= htmlspecialchars($edit_request['village'] ?? '') ?>" placeholder="e.g. Ng'araria Center" required>
                    </div>
                </div>

                <div class="form-group">
                    <label>Category</label>
                    <select name="category" required>
                        <?php foreach (["Roads & Infrastructure", "Water & Sanitation", "Health Services", "Agriculture & Livestock", "General Public Suggestion"] as $cat): ?>
                            <option value="<?= $cat ?>" <?= ($edit_request && $edit_request['category'] === $cat) ? 'selected' : '' ?>><?= $cat ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Description of Issue / Suggestion</label>
                    <textarea name="description" rows="4" required><?= htmlspecialchars($edit_request['description'] ?? '') ?></textarea>
                </div>

                <?php if (!$edit_request): ?>
                <div class="form-group">
                    <label>Photo Evidence (Optional)</label>
                    <input type="file" name="attachment" accept="image/*">
                </div>

                <!-- Fixed Checkbox Alignment -->
                <label class="checkbox-group">
                    <input type="checkbox" name="is_anonymous" value="1">
                    <span>Submit Anonymously (Hide identity from public officials)</span>
                </label>
                <?php endif; ?>

                <button type="submit" class="btn btn-success"><?= $edit_request ? "Update Request" : "Submit Request" ?></button>
                <?php if ($edit_request): ?>
                    <a href="dashboard.php" class="btn btn-gold" style="margin-left:10px;">Cancel Edit</a>
                <?php endif; ?>
            </form>
        </div>

        <?php if (isLoggedIn() && !empty($my_requests)): ?>
        <div class="card">
            <h2>My Submitted Requests</h2>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Location</th>
                        <th>Category</th>
                        <th>Status</th>
                        <th>Date & Time</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($my_requests as $req): ?>
                    <tr>
                        <td>#<?= $req['id'] ?></td>
                        <td><?= htmlspecialchars($req['constituency'] . ' - ' . $req['ward']) ?></td>
                        <td><?= htmlspecialchars($req['category']) ?></td>
                        <td>
                            <strong style="color: <?= $req['status'] === 'Resolved' ? 'green' : ($req['status'] === 'In Progress' ? 'blue' : 'orange') ?>;">
                                <?= htmlspecialchars($req['status']) ?>
                            </strong>
                        </td>
                        <td><?= $req['created_at'] ?></td>
                        <td>
                            <?php if ($req['status'] === 'Pending'): ?>
                                <a href="dashboard.php?action=edit&id=<?= $req['id'] ?>" class="btn btn-gold" style="padding: 3px 8px; font-size: 0.8rem;">Edit</a>
                                <a href="dashboard.php?action=delete&id=<?= $req['id'] ?>" onclick="return confirm('Are you sure you want to delete this request?');" class="btn btn-danger" style="padding: 3px 8px; font-size: 0.8rem;">Delete</a>
                            <?php elseif ($req['status'] === 'Resolved'): ?>
                                <a href="submit_feedback.php?request_id=<?= $req['id'] ?>" class="btn btn-success" style="padding: 3px 8px; font-size: 0.8rem;">Rate</a>
                            <?php else: ?>
                                <span style="color:#777; font-size:0.85rem;">Processing</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>

    <?php if ($edit_request): ?>
        <script>
            // Pre-select ward on edit load
            updateWards("<?= htmlspecialchars($edit_request['ward']) ?>");
        </script>
    <?php endif; ?>
</body>
</html>