<?php
session_start();
require 'config.php';

// ✅ Ensure order_id is provided
if (!isset($_GET['order_id'])) {
    header("Location: customer.php?error=No order specified");
    exit;
}

$order_id = (int) $_GET['order_id'];
$user_id = $_SESSION['user_id'] ?? null;

if (!$user_id) {
    header("Location: login.php");
    exit;
}

/* ---- FETCH ORDER DETAILS ---- */
$stmt = $pdo->prepare("
    SELECT 
        o.id,
        o.quantity,
        o.payment_status,
        o.user_id,
        o.products_id,
        p.name AS flower_name,
        p.price AS product_price,
        p.image
    FROM orders o
    JOIN products p ON o.products_id = p.id
    WHERE o.id = ? AND o.user_id = ?
");
$stmt->execute([$order_id, $user_id]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    echo "<p>Invalid order ID.</p>";
    exit;
}

// ✅ Calculate total amount
$amount_due = $order['quantity'] * $order['product_price'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Payment - Flower Shop</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <style>
    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
      font-family: 'Poppins', sans-serif;
    }

    body {
      display: flex;
      min-height: 100vh;
      background: linear-gradient(135deg, #ffb6c1, #dda0dd);
      color: #333;
    }

    /* Sidebar */
    .sidebar {
      width: 230px;
      background: #6a0dad;
      color: #fff;
      display: flex;
      flex-direction: column;
      padding-top: 30px;
      box-shadow: 2px 0 10px rgba(0, 0, 0, 0.2);
    }

    .sidebar h2 {
      text-align: center;
      margin-bottom: 30px;
      font-size: 22px;
      letter-spacing: 1px;
    }

    .sidebar a {
      display: block;
      padding: 12px 20px;
      color: #fff;
      text-decoration: none;
      font-weight: 500;
      transition: all 0.3s ease;
      border-radius: 8px;
      margin: 8px 15px;
      text-align: left;
    }

    .sidebar a:hover, .sidebar a.active {
      background: #ff69b4;
      color: white;
      transform: translateX(5px);
      box-shadow: 0 0 12px rgba(255, 105, 180, 0.6);
    }

    .logout {
      margin-top: auto;
      padding: 20px;
      text-align: center;
    }

    .logout a {
      background: #ff69b4;
      padding: 10px 15px;
      border-radius: 8px;
      color: white;
      text-decoration: none;
      transition: 0.3s;
      font-weight: bold;
      box-shadow: 0 0 10px rgba(255, 105, 180, 0.4);
    }

    .logout a:hover {
      background: #ff1493;
      box-shadow: 0 0 15px rgba(255, 20, 147, 0.6);
    }

    /* Main content */
    .content {
      flex: 1;
      padding: 50px;
      display: flex;
      justify-content: center;
      align-items: center;
    }

    .payment-card {
      background: #fff;
      padding: 40px;
      border-radius: 16px;
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.15);
      max-width: 500px;
      width: 100%;
      text-align: center;
      animation: fadeIn 0.6s ease-in-out;
    }

    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(20px); }
      to { opacity: 1; transform: translateY(0); }
    }

    .payment-card h3 {
      color: #6a0dad;
      margin-bottom: 10px;
      font-size: 24px;
    }

    .highlight {
      background: #ff69b4;
      color: #fff;
      padding: 12px;
      margin: 20px 0;
      border-radius: 8px;
      font-weight: bold;
      font-size: 18px;
    }

    .order-details {
      margin: 20px 0;
      text-align: left;
      font-size: 1em;
      color: #444;
    }

    .order-details p {
      margin: 8px 0;
    }

    .confirm-btn {
      display: inline-block;
      background: #6a0dad;
      color: #fff;
      padding: 12px 20px;
      border-radius: 8px;
      text-decoration: none;
      font-weight: bold;
      transition: all 0.3s;
      box-shadow: 0 0 10px rgba(106, 13, 173, 0.3);
    }

    .confirm-btn:hover {
      background: #ff69b4;
      box-shadow: 0 0 15px rgba(255, 105, 180, 0.6);
      transform: scale(1.05);
    }

    .back-btn {
      margin-top: 25px;
    }

    .back-btn a {
      text-decoration: none;
      color: #6a0dad;
      font-weight: bold;
      transition: 0.3s;
    }

    .back-btn a:hover {
      color: #ff1493;
      text-decoration: underline;
    }
  </style>
</head>
<body>

  <!-- Sidebar -->
  <div class="sidebar">
      <h2>🌸 Flower Shop</h2>
      <a href="customer.php"><i class="fas fa-home"></i> Dashboard</a>
      <a href="my_orders.php" class="active"><i class="fas fa-shopping-cart"></i> My Orders</a>
      <a href="profile.php"><i class="fas fa-user"></i> Profile</a>
      <div class="logout">
        <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
      </div>
  </div>

  <!-- Main Content -->
  <div class="content">
    <div class="payment-card">
        <h3><i class="fas fa-credit-card"></i> M-Pesa Payment</h3>
        <p>Complete your payment using the details below:</p>

        <div class="highlight">
          POCHI LA BIASHARA: <strong>0115652819</strong>
        </div>

        <div class="order-details">
          <p><i class="fas fa-receipt"></i> <b>Order ID:</b> <?= htmlspecialchars($order['id']) ?></p>
          <p><i class="fas fa-spa"></i> <b>Flower:</b> <?= htmlspecialchars($order['flower_name']) ?></p>
          <p><i class="fas fa-tags"></i> <b>Price per Flower:</b> KES <?= number_format($order['product_price'], 2) ?></p>
          <p><i class="fas fa-sort-numeric-up"></i> <b>Quantity:</b> <?= htmlspecialchars($order['quantity']) ?></p>
          <p><i class="fas fa-money-bill-wave"></i> <b>Total Amount Due:</b> 
             <strong>KES <?= number_format($amount_due, 2) ?></strong></p>
        </div>

        <p>Once payment is complete, click below to confirm.</p>

        <a href="confirm_payment.php?order_id=<?= htmlspecialchars($order['id']) ?>" class="confirm-btn">
            <i class="fas fa-check-circle"></i> Confirm Payment
        </a>

        <div class="back-btn">
            <a href="my_orders.php"><i class="fas fa-arrow-left"></i> Back to My Orders</a>
        </div>
    </div>
  </div>
</body>
</html>
