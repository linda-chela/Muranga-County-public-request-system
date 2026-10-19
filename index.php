<?php
require_once 'config.php';

// Redirect logged-in users to their respective portals
if (isLoggedIn()) {
    header("Location: " . (isAdmin() ? "admin.php" : "dashboard.php"));
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home - Murang'a County Public Request System</title>
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
    <!-- Functional Accessibility Toolbar -->
    <div class="accessibility-bar">
        <div>
            <strong>Accessibility Options:</strong> Text Size 
            <button class="access-btn" onclick="changeTextSize('decrease')">A-</button>
            <button class="access-btn" onclick="changeTextSize('reset')">A</button>
            <button class="access-btn" onclick="changeTextSize('increase')">A+</button> | 
            <button class="access-btn" onclick="toggleHighContrast()">High Contrast Mode</button>
        </div>
        <span>📞 County Support Hotline: 0800 720 000</span>
    </div>

    <!-- Navigation Header -->
    <div class="navbar">
        <div class="navbar-brand">
            <img src="uploads/muranga_logo.png" alt="Murang'a County Logo" onerror="this.style.display='none';">
            <h2 style="margin:0; font-size:1.2rem;">Murang'a County Public Request System</h2>
        </div>
        <div style="display:flex; align-items:center; gap:8px;">
            <a href="index.php">Home</a>
            <a href="login.php">Citizen Login</a>
            <a href="register.php" class="btn btn-success">Sign Up</a>
            
            <!-- Dedicated Admin Access Link -->
            <a href="login.php?role=admin" style="background: rgba(212, 175, 55, 0.2); border: 1px solid var(--muranga-gold); color: var(--muranga-gold); font-weight:600; padding:6px 12px; border-radius:4px; margin-left:6px;">
                🔒 Admin Portal
            </a>
        </div>
    </div>

    <div class="container">
        
        <!-- Interactive Hero Banner -->
        <div class="hero-banner">
            <h1 style="font-size:2.4rem; margin-bottom:0.8rem; font-weight:700;">Welcome to Murang'a</h1>
            
            <div style="margin: 1rem 0 1.5rem 0;">
                <span class="tagline-badge">🚨 Urgency is our priority</span>
                <span class="tagline-badge">🤝 # Service for all</span>
                <span class="tagline-badge">🗣️ ✓ People voice</span>
            </div>

            <p style="font-size:1.05rem; max-width:850px; margin:0 auto 2rem auto; line-height:1.7; text-align:justify; background:rgba(0,0,0,0.25); padding:1.2rem; border-radius:8px; border-left:4px solid var(--muranga-gold);">
                Guided by our official county motto <em>"Kamuingi Koyaga Ndiri"</em> and Governor Dr. Irungu Kang'ata’s vision for automated, inclusive, and responsive governance, the Murang'a County Public Request System bridges the gap between citizens and county administration. <strong>"Urgency is our priority"</strong>, and <strong>"we are dedicated to work for the citizen of Murang'a"</strong> by providing a digitized, transparent platform where every voice matters. Whether you choose to sign up or send an anonymous request across our 7 constituencies, 35 wards, and local villages, our platform guarantees safe, accountable service delivery—empowering community feedback and real-time request tracking because <strong>Huduma Bora kwa Wote</strong> is our pledge.
            </p>

            <div style="display:flex; justify-content:center; gap:15px; flex-wrap:wrap;">
                <a href="register.php" class="btn btn-success" style="font-size:1.1rem; padding:12px 28px;">Sign Up Now</a>
                <a href="dashboard.php" class="btn btn-gold" style="font-size:1.1rem; padding:12px 28px;">Send Anonymous Request</a>
            </div>
        </div>

        <!-- System Core Capabilities -->
        <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap:22px; margin-top:2.5rem;">
            <div class="card" style="border-top:4px solid var(--muranga-green);">
                <h3 style="color:var(--muranga-green); margin-top:0;">📑 Public Requests</h3>
                <p style="color:#555;">Submit public service issues directly mapped to your specific Constituency, Ward, and Village.</p>
            </div>

            <div class="card" style="border-top:4px solid var(--muranga-gold);">
                <h3 style="color:#8b7500; margin-top:0;">🔍 Real-time Status</h3>
                <p style="color:#555;">Track progress updates directly from your personal dashboard as county officers act on your requests.</p>
            </div>

            <div class="card" style="border-top:4px solid var(--muranga-green);">
                <h3 style="color:var(--muranga-green); margin-top:0;">⭐ Citizen Feedback</h3>
                <p style="color:#555;">Provide star ratings and comments on resolved issues to help maintain high service standards.</p>
            </div>
        </div>

    </div>

    <!-- Official County Footer -->
    <footer style="text-align:center; padding:1.8rem; margin-top:3rem; border-top:1px solid #e0e0e0; background:#ffffff; color:#555; font-size:0.9rem;">
        <p style="margin-bottom:6px;">&copy; <?= date('Y') ?> <strong>County Government of Murang'a</strong>. All Rights Reserved.</p>
        <p style="margin:0;">
            County Staff Member? 
            <a href="login.php?role=admin" style="color:var(--muranga-green); font-weight:700; text-decoration:none; margin-left:4px;">
                🔒 Access Admin Login
            </a>
        </p>
    </footer>
</body>
</html>