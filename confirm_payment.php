<?php
// confirm_payment.php
// Marks an order as paid (customer-confirmed payment).
// - Verifies the user is logged in
// - Verifies the order exists and belongs to the user
// - Updates the orders table: payment_status = 'Paid', status = 'Completed'
// - Shows a confirmation (or error) page

// Only start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require 'config.php'; // your PDO $pdo connection

// must have order_id
if (!isset($_GET['order_id'])) {
    header("Location: my_orders.php?error=No order specified");
    exit;
}

$order_id = (int) $_GET['order_id'];
$user_id  = $_SESSION['user_id'] ?? null;

if (!$user_id) {
    header("Location: login.php");
    exit;
}

// fetch order and verify ownership
$stmt = $pdo->prepare("SELECT id, user_id, payment_status FROM orders WHERE id = ?");
$stmt->execute([$order_id]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    header("Location: my_orders.php?error=Order not found.");
    exit;
}

if ((int)$order['user_id'] !== (int)$user_id) {
    header("Location: my_orders.php?error=Unauthorized.");
    exit;
}

// If already paid, no update needed
$alreadyPaid = ($order['payment_status'] === 'Paid');
$updated = false;

if (!$alreadyPaid) {
    try {
        $upd = $pdo->prepare("UPDATE orders SET payment_status = 'Paid', status = 'Completed' WHERE id = ?");
        $updated = $upd->execute([$order_id]);
    } catch (PDOException $e) {
        // Log error in real app; show friendly message below
        $updated = false;
        $dbError = $e->getMessage();
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Payment Confirmation</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      margin: 0;
      display: flex;
      justify-content:center;
      align-items:center;
      min-height: 100vh;
      background: linear-gradient(135deg, #f9f7f6, #fbe9e7);
    }
    .confirmation-card {
      background: #fff;
      padding: 36px;
      border-radius: 12px;
      text-align: center;
      box-shadow: 0 8px 24px rgba(0,0,0,0.08);
      max-width: 560px;
      width: 100%;
    }
    .confirmation-card i { font-size:48px; margin-bottom:12px; }
    .success { color: #16a34a; }
    .error   { color: #dc2626; }
    .btn {
      display:inline-block;
      margin-top:18px;
      padding:10px 18px;
      border-radius:8px;
      background:#ff6f61;
      color:#fff;
      text-decoration:none;
      font-weight:600;
    }
    .btn:hover { background:#e65b50; }
    .small { color:#555; margin-top:10px; display:block; }
  </style>
</head>
<body>
  <div class="confirmation-card">
    <?php if ($alreadyPaid || $updated): ?>
      <i class="fas fa-check-circle success"></i>
      <h2 class="success">Payment Confirmed</h2>
      <p>Thank you! Your order <strong>#<?= htmlspecialchars($order_id) ?></strong> has been marked as <strong>Paid</strong>.</p>
      <a href="my_orders.php" class="btn"><i class="fas fa-box-open"></i> View My Orders</a>
      <span class="small">If you used Pochi la Biashara (0115652819), allow a few seconds for the vendor to reconcile.</span>
    <?php else: ?>
      <i class="fas fa-times-circle error"></i>
      <h2 class="error">Confirmation Failed</h2>
      <p>We couldn't update your order right now. Please try again in a moment or contact support.</p>
      <?php if (!empty($dbError)): ?>
        <p style="font-size:0.9em;color:#666;"><strong>Details:</strong> <?= htmlspecialchars($dbError) ?></p>
      <?php endif; ?>
      <a href="my_orders.php" class="btn">Back to My Orders</a>
    <?php endif; ?>
  </div>
</body>
</html>
