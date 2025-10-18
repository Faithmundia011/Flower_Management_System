<?php
// auth.php - handles authentication
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();
require_once 'config.php'; // must provide $pdo or $conn

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: login.php');
    exit;
}

$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';

if ($username === '' || $password === '') {
    header('Location: login.php?error=' . urlencode('Please enter both username and password.'));
    exit;
}

$user = false;

// ✅ Using PDO
if (isset($pdo) && $pdo instanceof PDO) {
    $stmt = $pdo->prepare('SELECT id, username, password, role FROM users WHERE username = ? LIMIT 1');
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

// ✅ Using MySQLi
} elseif (isset($conn) && $conn instanceof mysqli) {
    $stmt = $conn->prepare('SELECT id, username, password, role FROM users WHERE username = ? LIMIT 1');
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $res = $stmt->get_result();
    $user = $res->fetch_assoc();
    $stmt->close();
} else {
    header('Location: login.php?error=' . urlencode('Database connection not found. Check config.php.'));
    exit;
}

if ($user && password_verify($password, $user['password'])) {
    $_SESSION['user_id'] = (int)$user['id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['role'] = $user['role'];

    if ($user['role'] === 'admin') {
        header('Location: admin.php');
    } else {
        header('Location: customer.php');
    }
    exit;
}

session_start();
require 'config.php'; // Make sure $pdo is available here

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {
        header("Location: login.php?error=" . urlencode("All fields are required."));
        exit;
    }

    // Check if user exists
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        // Store session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];

        // Redirect based on role
        if ($user['role'] === 'admin') {
            header("Location: admin.php");
        } else {
            header("Location: customer.php");
        }
        exit;
    } else {
        header("Location: login.php?error=" . urlencode("Invalid username or password."));
        exit;
    }
} else {
    header("Location: login.php");
    exit;
}


// login failed
header('Location: login.php?error=' . urlencode('Invalid username or password.'));
exit;
