<?php
require_once 'config.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullname = trim($_POST['fullname']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if ($password !== $confirm_password) {
        $error = "Password and Confirm Password do not match.";
    } elseif (strlen($password) < 8 || !preg_match("/[A-Z]/", $password) || !preg_match("/[0-9]/", $password)) {
        $error = "Password must be at least 8 characters long and include at least 1 uppercase letter and 1 number.";
    } else {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $error = "This email address is already registered.";
        } else {
            $hashed = password_hash($password, PASSWORD_BCRYPT);
            $stmt = $pdo->prepare("INSERT INTO users (fullname, email, password, role) VALUES (?, ?, ?, 'citizen')");
            if ($stmt->execute([$fullname, $email, $hashed])) {
                $success = "Account created successfully! You can now <a href='login.php'>Login here</a>.";
            } else {
                $error = "An error occurred during registration. Please try again.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Murang'a County Public Request System</title>
    <link rel="stylesheet" href="style.css">
    <script>
        function calculateAge() {
            const dob = new Date(document.getElementById('dob').value);
            if (dob) {
                const diff = Date.now() - dob.getTime();
                const ageDate = new Date(diff);
                const age = Math.abs(ageDate.getUTCFullYear() - 1970);
                document.getElementById('age').value = isNaN(age) ? '' : age;
            }
        }

        function togglePassword(fieldId) {
            const field = document.getElementById(fieldId);
            field.type = field.type === "password" ? "text" : "password";
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
        <div><a href="index.php">Home</a> | <a href="login.php">Login</a></div>
    </div>

    <div class="container" style="max-width: 520px;">
        <div class="card">
            <h2>Citizen Registration</h2>
            <p style="color:#666; font-size:0.9rem; margin-bottom:1rem;">Register your account to track public requests and receive updates.</p>

            <?php if ($error): ?><p style="color: red; margin-bottom:1rem;"><?= htmlspecialchars($error) ?></p><?php endif; ?>
            <?php if ($success): ?><p style="color: green; margin-bottom:1rem;"><?= $success ?></p><?php endif; ?>

            <form method="POST">
                <div class="form-group">
                    <label>Full Name</label>
                    <input type="text" name="fullname" required>
                </div>

                <div class="form-group">
                    <label>Email Address</label>
                    <input type="email" name="email" required>
                </div>

                <div style="display:flex; gap:10px;">
                    <div class="form-group" style="flex:2;">
                        <label>Date of Birth</label>
                        <!-- Reasonable DOB Limit: Max today, min year 1920 -->
                        <input type="date" id="dob" name="dob" max="<?= date('Y-m-d') ?>" min="1920-01-01" onchange="calculateAge()" required>
                    </div>
                    <div class="form-group" style="flex:1;">
                        <label>Age</label>
                        <input type="number" id="age" name="age" readonly style="background:#f0f0f0;">
                    </div>
                </div>

                <div class="form-group">
                    <label>Password</label>
                    <input type="password" id="pass" name="password" required>
                    <small style="color:#666; display:block; margin-top:3px;">
                        💡 Strong Password: Minimum 8 characters, 1 uppercase, and 1 number.
                    </small>
                </div>

                <div class="form-group">
                    <label>Confirm Password</label>
                    <input type="password" id="confirm_pass" name="confirm_password" required>
                </div>

                <!-- Fixed Checkbox Alignment -->
                <label class="checkbox-group">
                    <input type="checkbox" onclick="togglePassword('pass'); togglePassword('confirm_pass');">
                    <span>Show Passwords</span>
                </label>

                <!-- Fixed Consent Checkbox Alignment -->
                <label class="checkbox-group">
                    <input type="checkbox" required>
                    <span>I consent to the processing of my personal details in compliance with the <strong>Kenya Data Protection Act 2019</strong>.</span>
                </label>

                <button type="submit" class="btn btn-success" style="width:100%;">Create Account</button>
            </form>
        </div>
    </div>
</body>
</html>