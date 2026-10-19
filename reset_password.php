<?php
require_once 'config.php';

$message = '';
$error = '';
$token = $_GET['token'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if ($password !== $confirm_password) {
        $error = "Passwords do not match. Please try again.";
    } elseif (strlen($password) < 8 || !preg_match("/[A-Z]/", $password) || !preg_match("/[0-9]/", $password)) {
        $error = "Password must be at least 8 characters long and include at least 1 uppercase letter and 1 number.";
    } else {
        // Example logic for updating password in database
        $hashed = password_hash($password, PASSWORD_BCRYPT);
        
        // Update user password logic here
        $message = "Your password has been reset successfully! You can now <a href='login.php'>Login</a>.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - Murang'a County Public Request System</title>
    <link rel="stylesheet" href="style.css">
    <script>
        function togglePassword(fieldId) {
            const field = document.getElementById(fieldId);
            field.type = field.type === "password" ? "text" : "password";
        }
    </script>
</head>
<body>
    <div class="navbar">
        <h2>Murang'a County Public Request System</h2>
        <div><a href="index.php">Home</a> | <a href="login.php">Login</a></div>
    </div>

    <div class="container" style="max-width: 450px;">
        <div class="card">
            <h2>Reset Your Password</h2>
            <p style="color:#666; font-size:0.9rem; margin-bottom:1rem;">Enter your new password below.</p>

            <?php if ($error): ?><p style="color: red; margin-bottom:1rem;"><?= htmlspecialchars($error) ?></p><?php endif; ?>
            <?php if ($message): ?><p style="color: green; margin-bottom:1rem;"><?= $message ?></p><?php endif; ?>

            <form method="POST">
                <div class="form-group">
                    <label>New Password</label>
                    <input type="password" id="pass" name="password" required>
                    <small style="color:#666; display:block; margin-top:3px;">
                        💡 Strong Password: Minimum 8 characters, 1 uppercase, and 1 number.
                    </small>
                </div>

                <div class="form-group">
                    <label>Confirm New Password</label>
                    <input type="password" id="confirm_pass" name="confirm_password" required>
                </div>

                <div style="margin-bottom: 1.2rem;">
                    <input type="checkbox" onclick="togglePassword('pass'); togglePassword('confirm_pass');"> Show Passwords
                </div>

                <button type="submit" class="btn btn-success" style="width:100%;">Update Password</button>
            </form>
        </div>
    </div>
</body>
</html>