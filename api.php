<?php
// api.php
header("Content-Type: application/json");
session_start();
require_once "config.php"; // contains $pdo

$action = $_POST['action'] ?? $_GET['action'] ?? null;

if (!$action) {
    echo json_encode(["status" => "error", "message" => "No action specified"]);
    exit;
}

try {
    // -------------------------------
    // Add a new product
    // -------------------------------
    if ($action === "add_product") {
        $name = $_POST['name'] ?? '';
        $price = $_POST['price'] ?? 0;

        if (empty($name) || empty($price)) {
            echo json_encode(["status" => "error", "message" => "Missing product name or price"]);
            exit;
        }

        $stmt = $pdo->prepare("INSERT INTO products (name, price, created_at) VALUES (?, ?, NOW())");
        $stmt->execute([$name, $price]);

        echo json_encode(["status" => "success", "message" => "Product added successfully"]);
        exit;
    }

    // -------------------------------
    // List products
    // -------------------------------
    elseif ($action === "list_products") {
        $stmt = $pdo->query("SELECT * FROM products ORDER BY created_at DESC");
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode(["status" => "success", "data" => $products]);
        exit;
    }

    // -------------------------------
    // Delete a product
    // -------------------------------
    elseif ($action === "delete_product") {
        $id = $_POST['id'] ?? null;

        if (!$id) {
            echo json_encode(["status" => "error", "message" => "No product ID provided"]);
            exit;
        }

        $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
        $stmt->execute([$id]);

        echo json_encode(["status" => "success", "message" => "Product deleted"]);
        exit;
    }

    // -------------------------------
    // List all customer orders
    // -------------------------------
    elseif ($action === "list_orders") {
        $stmt = $pdo->query("
            SELECT o.id, o.quantity, o.status, o.created_at,
                   u.username,
                   p.name AS product_name,
                   (p.price * o.quantity) AS total
            FROM orders o
            JOIN users u ON o.user_id = u.id
            JOIN products p ON o.product_id = p.id
            ORDER BY o.created_at DESC
        ");
        $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode(["status" => "success", "data" => $orders]);
        exit;
    }

    // -------------------------------
    // Update order status
    // -------------------------------
    elseif ($action === "update_order_status") {
        $id = $_POST['id'] ?? null;
        $status = $_POST['status'] ?? null;

        if (!$id || !$status) {
            echo json_encode(["status" => "error", "message" => "Missing order ID or status"]);
            exit;
        }

        $stmt = $pdo->prepare("UPDATE orders SET status = ? WHERE id = ?");
        $stmt->execute([$status, $id]);

        echo json_encode(["status" => "success", "message" => "Order status updated"]);
        exit;
    }

    // -------------------------------
    // Invalid action
    // -------------------------------
    else {
        echo json_encode(["status" => "error", "message" => "Invalid action"]);
        exit;
    }
} catch (Exception $e) {
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
    exit;
}
