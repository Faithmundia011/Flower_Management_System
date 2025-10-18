<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'customer') {
    header("Location: login.php");
    exit();
}
require 'config.php';

$user_id = $_SESSION['user_id'];

/* ---------------- FETCH FLOWERS ---------------- */
$stmt = $pdo->query("SELECT * FROM products WHERE is_active = 1 ORDER BY created_at DESC");
$flowers = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Customer Dashboard</title>
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
    h1, h2 {
        color: #4a004e;
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

    .section {
        background: white;
        border-radius: 12px;
        padding: 20px;
        box-shadow: 0 3px 8px rgba(0,0,0,0.1);
        margin-bottom: 25px;
    }

    .products {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
        gap: 20px;
    }
    .product-card {
        background: #fff;
        border-radius: 12px;
        text-align: center;
        padding: 15px;
        box-shadow: 0 2px 6px rgba(0,0,0,0.1);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .product-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 4px 10px rgba(0,0,0,0.2);
    }
    .product-card img {
        width: 100%;
        height: 130px;
        object-fit: cover;
        border-radius: 10px;
        margin-bottom: 10px;
    }
    .product-card button {
        background: linear-gradient(45deg, #ff6fd8, #3813c2);
        color: white;
        border: none;
        padding: 8px 12px;
        border-radius: 6px;
        cursor: pointer;
        font-weight: bold;
    }
    .product-card input[type="number"] {
        width: 60px;
        padding: 5px;
        border-radius: 6px;
        border: 1px solid #ccc;
    }
</style>
</head>
<body>
    <div class="sidebar">
        <div>
            <h2>🌸 Customer</h2>
            <a href="customer.php">🏠 Dashboard</a>
            <a href="#flowers">🌷 Flowers</a>
            <a href="my_orders.php">🧾 My Orders</a>
        </div>
        <a href="logout.php" class="logout">🚪 Logout</a>
    </div>

    <div class="main">
        <div class="header">
            <h1>Welcome, <?= htmlspecialchars($_SESSION['username']); ?> 💐</h1>
        </div>

        <div class="section" id="welcome">
            <h2>🌼 Dashboard Overview</h2>
            <p>Welcome to your flower shop dashboard! Browse flowers, place orders, and track your purchases — all in one place.</p>
        </div>

        <div class="section" id="flowers">
            <h2>🌷 Available Flowers</h2>
            <div class="products">
                <?php if ($flowers): ?>
                    <?php foreach ($flowers as $f): ?>
                        <div class="product-card">
                            <img src="uploads/<?= htmlspecialchars($f['image'] ?? 'placeholder.jpg'); ?>" alt="<?= htmlspecialchars($f['name']); ?>">
                            <h3><?= htmlspecialchars($f['name']); ?></h3>
                            <p>Price: KES <?= number_format($f['price'], 2); ?></p>
                            <p>Stock: <?= htmlspecialchars($f['stock']); ?></p>
                            <form method="POST" action="order.php">
                                <input type="hidden" name="product_id" value="<?= $f['id']; ?>">
                                <input type="number" name="quantity" min="1" max="<?= $f['stock']; ?>" required placeholder="Qty">
                                <button type="submit">Order Now</button>
                            </form>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p><em>No flowers available right now 🌷</em></p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>
