<?php
session_start();
require 'config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'customer') {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $product_id = $_POST['product_id'] ?? 0;
    $quantity = $_POST['quantity'] ?? 1;

    // Get product info
    $stmt = $pdo->prepare("SELECT price, stock FROM products WHERE id = ?");
    $stmt->execute([$product_id]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($product && $product['stock'] >= $quantity) {
        $total_price = $product['price'] * $quantity;

        // Insert into orders
        $stmt = $pdo->prepare("INSERT INTO orders (user_id, products_id, quantity, total_price, payment_status, status, order_date) VALUES (?, ?, ?, ?, 'Pending', 'Processing', NOW())");
        $stmt->execute([$user_id, $product_id, $quantity, $total_price]);

        // Reduce stock
        $newStock = $product['stock'] - $quantity;
        $pdo->prepare("UPDATE products SET stock = ? WHERE id = ?")->execute([$newStock, $product_id]);

        // Redirect to my_orders.php
        header("Location: my_orders.php?success=1");
        exit();
    } else {
        header("Location: customer.php?error=OutOfStock");
        exit();
    }
} else {
    header("Location: customer.php");
    exit();
}
?>
