<?php
// Configuration & Database Connection (SQLite)
session_start();

try {
    $pdo = new PDO('sqlite:' . __DIR__ . '/database.sqlite');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

    // Initialize Database Tables
    $pdo->exec("CREATE TABLE IF NOT EXISTS users (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        fullname TEXT NOT NULL,
        email TEXT UNIQUE NOT NULL,
        password TEXT NOT NULL,
        role TEXT DEFAULT 'citizen',
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )");

    $pdo->exec("CREATE TABLE IF NOT EXISTS requests (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        user_id INTEGER,
        is_anonymous INTEGER DEFAULT 0,
        constituency TEXT NOT NULL,
        ward TEXT NOT NULL,
        village TEXT NOT NULL,
        category TEXT NOT NULL,
        description TEXT NOT NULL,
        attachment TEXT,
        status TEXT DEFAULT 'Pending',
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id)
    )");

    $pdo->exec("CREATE TABLE IF NOT EXISTS feedback (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        request_id INTEGER NOT NULL,
        user_id INTEGER NOT NULL,
        rating INTEGER NOT NULL,
        comments TEXT,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (request_id) REFERENCES requests(id),
        FOREIGN KEY (user_id) REFERENCES users(id)
    )");

    // Seed Default Admin Account if not exists
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = 'admin@muranga.go.ke'");
    $stmt->execute();
    if ($stmt->fetchColumn() == 0) {
        $adminPass = password_hash('Admin123!', PASSWORD_BCRYPT);
        $pdo->prepare("INSERT INTO users (fullname, email, password, role) VALUES ('System Administrator', 'admin@muranga.go.ke', ?, 'admin')")
            ->execute([$adminPass]);
    }

} catch (PDOException $e) {
    die("Database Connection Error: " . $e->getMessage());
}

// Authentication Helpers
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}
?>