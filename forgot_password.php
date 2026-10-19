<?php
require_once 'config.php';

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch();

    if ($user) {
        $token = sprintf("%06d", mt_rand(100000, 999999));
        $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));

        $update = $pdo->prepare("UPDATE users SET reset_token = :token, reset_expires = :expires WHERE email = :email");
        $update->execute(['token' => $token, 'expires' => $expires, 'email' => $email]);

        $message = "Your reset token is: <strong style='font-size: 1.2rem; color: #1b5e20;'>$token</strong><br><br><a href='reset_password.php?email=" . urlencode($email) . "' class='btn'>Proceed to Reset Password</a>";
    } else {
        $error = "No account found with that email address.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Forgot Password - Murang'a County Portal</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="navbar">
        <h2>Murang'a County e-Citizen Portal</h2>
        <div><a href="login.php">Back to Login</a></div>
    </div>
    <div class="container" style="max-width: 450px;">
        <div class="card">
            <h2>Forgot Password</h2>
            <?php if ($error): ?><p style="color: red; margin-bottom: 1rem;"><?= htmlspecialchars($error) ?></p><?php endif; ?>
            <?php if ($message): ?><p style="color: green; margin-bottom: 1rem; background: #e8f5e9; padding: 10px; border-radius: 4px;"><?= $message ?></p><?php endif; ?>
            
            <form method="POST">
                <div class="form-group">
                    <label>Email Address</label>
                    <input type="email" name="email" required placeholder="e.g. citizen@gmail.com">
                </div>
                <button type="submit" class="btn">Generate Reset Token</button>
            </form>
        </div>
    </div>    
</body>
</html>