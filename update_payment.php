<?php
session_start();
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'admin') {
    header("Location: login.php");
    exit();
}
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $order_id = $_POST['order_id'] ?? 0;
    $new_status = $_POST['payment_status'] ?? 'Unpaid';

    $stmt = $pdo->prepare("UPDATE orders SET payment_status = ? WHERE id = ?");
    $stmt->execute([$new_status, $order_id]);

    header("Location: admin.php?payment_updated=1");
    exit();
}
header("Location: admin.php");
exit();
