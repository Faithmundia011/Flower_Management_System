<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'customer') {
    header("Location: login.php");
    exit();
}
require 'config.php';

$user_id = $_SESSION['user_id'];

/* ---------------- FETCH CUSTOMER ORDERS ---------------- */
$stmt = $pdo->prepare("
    SELECT 
        o.id, 
        f.name AS flower_name, 
        o.quantity, 
        o.total_price, 
        o.payment_status, 
        o.status,
        o.order_date
    FROM orders o
    JOIN products f ON o.products_id = f.id
    WHERE o.user_id = ?
    ORDER BY o.order_date DESC
");
$stmt->execute([$user_id]);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>My Orders & Payments</title>
<style>
    body {
        font-family: 'Segoe UI', sans-serif;
        margin: 0;
        display: flex;
        height: 100vh;
        background: linear-gradient(135deg, #ffccf9, #cbb4ff, #d5a6e6);
    }
    .sidebar {
        width: 220px;
        background: linear-gradient(180deg, #b26eb9, #e27db9);
        color: white;
        padding: 20px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }
    .sidebar h2 {
        text-align: center;
        margin-bottom: 20px;
    }
    .sidebar a {
        display: block;
        padding: 12px;
        border-radius: 8px;
        color: white;
        text-decoration: none;
        font-weight: bold;
        margin-bottom: 10px;
        transition: background 0.3s;
    }
    .sidebar a:hover {
        background: rgba(255, 255, 255, 0.2);
    }
    .logout {
        background: white;
        color: #b26eb9 !important;
        text-align: center;
    }
    .main {
        flex: 1;
        padding: 30px;
        overflow-y: auto;
    }
    .header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: white;
        padding: 15px 25px;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        margin-bottom: 25px;
    }
    .logout-btn {
        background: linear-gradient(45deg, #ff6fd8, #3813c2);
        color: white;
        padding: 10px 15px;
        border-radius: 6px;
        text-decoration: none;
        font-weight: bold;
    }
    table {
        width: 100%;
        border-collapse: collapse;
        background: white;
        border-radius: 12px;
        box-shadow: 0 3px 8px rgba(0,0,0,0.08);
    }
    th, td {
        padding: 12px;
        text-align: center;
        border-bottom: 1px solid #eee;
    }
    th {
        background: #f3e5f5;
        color: #4a004e;
    }
    tr:hover {
        background: #fce4ec;
    }
    .pay-btn {
        background: linear-gradient(20deg, #ff6fd8, #3813c2);
        color: white;
        border: none;
        padding: 4px 8px;
        border-radius: 4px;
        cursor: pointer;
        font-weight: bold;
        text-decoration: none;
    }
    .paid-label {
        color: green;
        font-weight: bold;
    }
</style>
</head>
<body>
    <div class="sidebar">
        <div>
            <h2>🌸 Customer</h2>
            <a href="customer.php">🏠 Dashboard</a>
            <a href="customer_orders.php">🧾 My Orders</a>
            <a href="admin.php">🌷 Available Flowers</a>
        </div>
        <a href="logout.php" class="logout">🚪 Logout</a>
    </div>

    <div class="main">
        <div class="header">
            <h1>🧾 My Orders & Payments</h1>
            <a href="logout.php" class="logout-btn">Logout</a>
        </div>

        <?php if ($orders): ?>
            <table>
                <tr>
                    <th>Order ID</th>
                    <th>Flower</th>
                    <th>Quantity</th>
                    <th>Total (KES)</th>
                    <th>Payment Status</th>
                    <th>Order Status</th>
                    <th>Date</th>
                    <th>Action</th>
                </tr>
                <?php foreach ($orders as $o): ?>
                    <tr>
                        <td><?= $o['id']; ?></td>
                        <td><?= htmlspecialchars($o['flower_name']); ?></td>
                        <td><?= $o['quantity']; ?></td>
                        <td><?= number_format($o['total_price'], 2); ?></td>
                        <td>
                            <?php if (strtolower($o['payment_status']) === 'paid'): ?>
                                <span class="paid-label">Paid ✅</span>
                            <?php else: ?>
                                <span style="color: red;">Unpaid ❌</span>
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars($o['status']); ?></td>
                        <td><?= htmlspecialchars($o['order_date']); ?></td>
                        <td>
                            <?php if (strtolower($o['payment_status']) !== 'paid'): ?>
                                <a href="payment.php?order_id=<?= $o['id']; ?>" class="pay-btn">💳 Pay Now</a>
                            <?php else: ?>
                                <span class="paid-label">✔</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>
        <?php else: ?>
            <p><em>You have not placed any orders yet 🌸</em></p>
        <?php endif; ?>
    </div>
</body>
</html>
