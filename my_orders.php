<?php
session_start();
require 'config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'customer') {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

/* ---- FETCH CUSTOMER ORDERS ---- */
$stmt = $pdo->prepare("
    SELECT 
        o.id AS order_id,
        o.quantity,
        o.payment_status,
        p.name AS flower_name,
        p.price AS flower_price,
        p.image AS flower_image
    FROM orders o
    JOIN products p ON o.products_id = p.id
    WHERE o.user_id = ?
    ORDER BY o.id DESC
");
$stmt->execute([$user_id]);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>My Orders - Flower Shop</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<style>
body {
    margin: 0;
    font-family: 'Segoe UI', sans-serif;
    background: linear-gradient(135deg, #f8bbd0, #ce93d8);
    display: flex;
}

/* Sidebar */
.sidebar {
    width: 230px;
    background: #6a1b9a;
    color: #fff;
    height: 100vh;
    padding: 20px;
    box-shadow: 2px 0 8px rgba(0,0,0,0.1);
}
.sidebar h2 {
    text-align: center;
    margin-bottom: 30px;
}
.sidebar a {
    display: block;
    color: #fff;
    text-decoration: none;
    font-weight: bold;
    padding: 12px;
    margin: 8px 0;
    border-radius: 8px;
    transition: background 0.3s;
}
.sidebar a:hover {
    background: rgba(255, 255, 255, 0.2);
}

/* Content */
.content {
    flex: 1;
    padding: 40px;
}
h2 {
    color: #4a148c;
    text-align: center;
    margin-bottom: 30px;
}

/* Order Cards */
.orders {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 25px;
}
.order-card {
    background: #fff;
    border-radius: 12px;
    padding: 20px;
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    transition: transform 0.3s;
}
.order-card:hover {
    transform: scale(1.02);
}
.order-card img {
    width: 100%;
    height: 180px;
    object-fit: cover;
    border-radius: 8px;
}
.order-info {
    margin-top: 15px;
}
.order-info p {
    margin: 5px 0;
    color: #555;
    font-size: 0.95em;
}
.status {
    margin-top: 10px;
    font-weight: bold;
}
.status.paid {
    color: green;
}
.status.pending {
    color: #e65100;
}
.pay-btn {
    display: block;
    background: #9c27b0;
    color: white;
    text-align: center;
    padding: 10px;
    margin-top: 15px;
    border-radius: 6px;
    text-decoration: none;
    font-weight: bold;
    transition: background 0.3s;
}
.pay-btn:hover {
    background: #7b1fa2;
}
</style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar">
    <h2>🌸 Flower Shop</h2>
    <a href="customer.php"><i class="fas fa-home"></i> Dashboard</a>
    <a href="my_orders.php"><i class="fas fa-shopping-cart"></i> My Orders</a>
    <a href="profile.php"><i class="fas fa-user"></i> Profile</a>
    <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
</div>

<!-- Main Content -->
<div class="content">
    <h2>My Orders</h2>
    <div class="orders">
        <?php if ($orders): ?>
            <?php foreach ($orders as $order): ?>
                <?php $total = $order['flower_price'] * $order['quantity']; ?>
                <div class="order-card">
                    <img src="uploads/<?= htmlspecialchars($order['flower_image']) ?>" alt="<?= htmlspecialchars($order['flower_name']) ?>">
                    <div class="order-info">
                        <p><strong>Flower:</strong> <?= htmlspecialchars($order['flower_name']) ?></p>
                        <p><strong>Price per Item:</strong> KES <?= number_format($order['flower_price'], 2) ?></p>
                        <p><strong>Quantity:</strong> <?= $order['quantity'] ?></p>
                        <p><strong>Total Price:</strong> <span style="color:#8e24aa;">KES <?= number_format($total, 2) ?></span></p>
                        <p class="status <?= $order['payment_status'] === 'Paid' ? 'paid' : 'pending' ?>">
                            <i class="fas <?= $order['payment_status'] === 'Paid' ? 'fa-check-circle' : 'fa-clock' ?>"></i>
                            <?= htmlspecialchars($order['payment_status']) ?>
                        </p>

                        <?php if ($order['payment_status'] !== 'Paid'): ?>
                            <a href="payment.php?order_id=<?= $order['order_id'] ?>" class="pay-btn">
                                <i class="fas fa-credit-card"></i> Pay Now
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p style="text-align:center; color:#4a148c;">You have not placed any orders yet.</p>
        <?php endif; ?>
    </div>
</div>

</body>
</html>
