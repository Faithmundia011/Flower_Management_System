<?php
// place_order.php
session_start();
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'customer') {
    header("Location: login.php");
    exit();
}

require 'config.php'; // contains $pdo

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = (int)($_POST['product_id'] ?? 0);
    $quantity   = (int)($_POST['quantity'] ?? 1);
    $user_id    = $_SESSION['user_id'];

    if ($product_id <= 0 || $quantity <= 0) {
        header("Location: customer.php?error=invalid_input");
        exit();
    }

    try {
        $pdo->beginTransaction();

        // Fetch product
        $stmt = $pdo->prepare("SELECT id, name, price, stock FROM products WHERE id = ?");
        $stmt->execute([$product_id]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$product || $product['stock'] < $quantity) {
            $pdo->rollBack();
            header("Location: customer.php?error=out_of_stock");
            exit();
        }

        // Insert order
        $orderStmt = $pdo->prepare("
            INSERT INTO orders (user_id, product_id, quantity, status, payment_status, created_at) 
            VALUES (?, ?, ?, 'Pending', 'Unpaid', NOW())
        ");
        $orderStmt->execute([$user_id, $product_id, $quantity]);

        // Reduce stock
        $updateStmt = $pdo->prepare("UPDATE products SET stock = stock - ? WHERE id = ?");
        $updateStmt->execute([$quantity, $product_id]);

        $pdo->commit();

        header("Location: customer.php?success=1");
        exit();

    } catch (Exception $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        error_log("Order error: " . $e->getMessage());
        header("Location: customer.php?error=server_error");
        exit();
    }
} else {
    header("Location: customer.php");
    exit();
}
