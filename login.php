<?php
require_once 'config.php';

$error = '';
$isAdminView = isset($_GET['role']) && $_GET['role'] === 'admin';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        // Enforce Admin View Security Constraint
        if ($isAdminView && $user['role'] !== 'admin') {
            $error = "Access Denied: Regular citizen accounts cannot log in through the Admin Portal.";
        } else {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['fullname'] = $user['fullname'];
            $_SESSION['role'] = $user['role'];

            header("Location: " . ($user['role'] === 'admin' ? "admin.php" : "dashboard.php"));
            exit;
        }
    } else {
        $error = "Invalid email address or password.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $isAdminView ? 'Admin Login' : 'Citizen Login' ?> - Murang'a County</title>
    <link rel="stylesheet" href="style.css">
    <script>
        function togglePass() {
            var x = document.getElementById("login_pass");
            x.type = x.type === "password" ? "text" : "password";
        }
    </script>
</head>
<body>
    <!-- Navigation Header -->
    <div class="navbar">
        <div class="navbar-brand">
            <img src="uploads/muranga_logo.png" alt="County Logo" onerror="this.style.display='none';">
            <h2 style="margin:0; font-size:1.2rem;">Murang'a County Public Request System</h2>
        </div>
        <div>
            <a href="index.php">Home</a>
            <?php if (!$isAdminView): ?>
                | <a href="register.php">Register</a>
            <?php endif; ?>
        </div>
    </div>

    <div class="container" style="max-width: 440px;">
        <div class="card" style="<?= $isAdminView ? 'border-top: 5px solid var(--muranga-gold);' : '' ?>">
            
            <?php if ($isAdminView): ?>
                <!-- Admin View Branding -->
                <h2 style="color: var(--dark-text); display:flex; align-items:center; gap:8px;">
                    🔒 County Admin Portal
                </h2>
                <p style="color:#666; font-size:0.9rem; margin-bottom:1.2rem;">
                    Restricted area for authorized Murang'a County administrative staff and officers only.
                </p>
            <?php else: ?>
                <!-- Citizen View Branding -->
                <h2>Citizen Account Login</h2>
                <p style="color:#666; font-size:0.9rem; margin-bottom:1.2rem;">
                    Log in to submit public requests and track resolution status.
                </p>
            <?php endif; ?>

            <?php if ($error): ?>
                <div style="background:#ffebee; color:#c62828; padding:10px; border-radius:5px; border-left:4px solid #c62828; margin-bottom:1rem; font-size:0.9rem;">
                    ⚠️ <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <form method="POST">
                <div class="form-group">
                    <label>Email Address</label>
                    <input type="email" name="email" placeholder="<?= $isAdminView ? 'officer@muranga.go.ke' : 'your.email@example.com' ?>" required>
                </div>

                <div class="form-group">
                    <label>Password</label>
                    <input type="password" id="login_pass" name="password" required>
                </div>

                <!-- Checkbox Alignment Fix -->
                <label class="checkbox-group">
                    <input type="checkbox" onclick="togglePass()">
                    <span>Show Password</span>
                </label>

                <button type="submit" class="btn <?= $isAdminView ? 'btn-gold' : 'btn-success' ?>" style="width:100%; font-size:1rem; margin-top:0.5rem;">
                    <?= $isAdminView ? 'Authenticate Officer' : 'Sign In' ?>
                </button>
            </form>

            <?php if (!$isAdminView): ?>
                <!-- Registration Link (Only visible to citizens) -->
                <p style="margin-top:1.2rem; text-align:center; font-size:0.9rem;">
                    Don't have an account? <a href="register.php" style="color:var(--muranga-green); font-weight:600;">Register here</a>
                </p>
            <?php else: ?>
                <!-- Notice for Admins -->
                <p style="margin-top:1.2rem; text-align:center; font-size:0.85rem; color:#888;">
                    Unauthorized access attempts are logged and monitored. <a href="login.php" style="color:#555;">Switch to Citizen Login</a>
                </p>
            <?php endif; ?>

        </div>
    </div>
</body>
</html>