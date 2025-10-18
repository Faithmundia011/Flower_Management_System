<?php
// delete_product.php
session_start();
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'admin') {
    header("Location: login.php");
    exit();
}

require 'config.php';

$id = $_GET['id'] ?? 0;

if ($id) {
    $stmt = $pdo->prepare("UPDATE products SET is_active = 0 WHERE id = ?");
    $stmt->execute([$id]);

    header("Location: admin.php?deleted=1");
    exit();
} else {
    header("Location: admin.php?error=no_id");
    exit();
}
