<?php
session_start();
require 'config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Fetch all orders with customer and product details
$stmt = $pdo->query("
    SELECT 
        o.id AS order_id,
        o.quantity,
        o.total_price,
        o.payment_status,
        o.status,
        o.order_date,
        u.username AS customer_name,
        p.name AS flower_name
    FROM orders o
    JOIN users u ON o.user_id = u.id
    JOIN products p ON o.products_id = p.id
    ORDER BY o.order_date DESC
");
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>🌸 Admin | Customer Orders</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
<style>
    body {
        margin:0; display:flex; height:100vh; font-family:'Segoe UI', sans-serif; background:#fdf6f9;
    }
    .sidebar {
        width:220px; background:linear-gradient(180deg,#ff6f91,#ffb3c6);
        padding:20px; color:white; display:flex; flex-direction:column; justify-content:space-between;
    }
    .sidebar h2 { text-align:center; margin-bottom:20px; }
    .sidebar a {
        display:block; padding:12px; margin:8px 0; border-radius:8px;
        color:white; text-decoration:none; font-weight:bold; transition:0.3s;
    }
    .sidebar a:hover { background:rgba(255,255,255,0.2); }
    .logout { background:white; color:#ff4b5c !important; text-align:center; }

    .main {
        flex:1; padding:30px; overflow:auto;
    }
    h2 {
        color:#ff4b5c; margin-bottom:20px;
    }

    table {
        width:100%; border-collapse: collapse; background:white;
        box-shadow:0 5px 15px rgba(0,0,0,0.1); border-radius:12px; overflow:hidden;
    }
    th, td { padding:12px; text-align:center; border-bottom:1px solid #eee; }
    th { background:#ff6fd8; color:white; font-weight:bold; }
    tr:hover { background:#ffe6f7; transition:0.2s; }

    .status-paid { color:green; font-weight:bold; }
    .status-unpaid { color:red; font-weight:bold; }
    .update-btn {
        background:linear-gradient(45deg,#ff6fd8,#3813c2);
        color:white; border:none; padding:8px 15px;
        border-radius:50px; cursor:pointer; transition:0.3s;
        font-weight:bold;
    }
    .update-btn:hover {
        transform:scale(1.05); background:linear-gradient(45deg,#ff4ab8,#2c0f9f);
    }
</style>
</head>
<body>
<div class="sidebar">
    <div>
        <h2>🌸 Admin Panel</h2>
        <a href="admin.php"><i class="fas fa-home"></i> Dashboard</a>
        <a href="product_list.php"><i class="fas fa-seedling"></i> Manage Products</a>
        <a href="admin_orders.php">📦 Customer Orders</a>
        </div>
    <a href="logout.php" class="logout">🚪 Logout</a>
</div>

<div class="main">
    <h2>📦 All Customer Orders</h2>

    <?php if ($orders): ?>
    <table>
        <thead>
            <tr>
                <th>Order ID</th>
                <th>Customer</th>
                <th>Flower</th>
                <th>Quantity</th>
                <th>Total (KES)</th>
                <th>Order Status</th>
                <th>Payment</th>
                <th>Order Date</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($orders as $order): ?>
            <tr>
                <td><?= $order['order_id'] ?></td>
                <td><?= htmlspecialchars($order['customer_name']) ?></td>
                <td><?= htmlspecialchars($order['flower_name']) ?></td>
                <td><?= $order['quantity'] ?></td>
                <td><?= number_format($order['total_price'], 2) ?></td>
                <td><?= htmlspecialchars($order['status']) ?></td>
                <td>
                    <?php if (strtolower($order['payment_status']) === 'paid'): ?>
                        <span class="status-paid">✅ Paid</span>
                    <?php else: ?>
                        <span class="status-unpaid">❌ Unpaid</span>
                    <?php endif; ?>
                </td>
                <td><?= $order['order_date'] ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php else: ?>
        <p><em>No customer orders found.</em></p>
    <?php endif; ?>
</div>
</body>
</html>
