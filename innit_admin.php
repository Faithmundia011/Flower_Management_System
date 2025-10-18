<?php
// init_admin.php - run once from browser or CLI to create an admin
require_once 'config.php';

$username = 'admin';
$password_plain = 'admin123'; // change after first login

$hash = password_hash($password_plain, PASSWORD_DEFAULT);

$stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
$stmt->execute([$username]);
if ($stmt->fetch()) {
    echo "Admin already exists.\n";
    exit;
}

$insert = $pdo->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, 'admin')");
$insert->execute([$username, $hash]);
echo "Admin created: $username / $password_plain\n";
