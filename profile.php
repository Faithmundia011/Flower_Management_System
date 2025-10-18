<?php
session_start();
require 'config.php';

// Check if logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch user info
$stmt = $pdo->prepare("SELECT username, role, created_at FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    die("⚠️ User not found.");
}

// Handle password update
$success = $error = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_pass = $_POST['new_password'] ?? '';
    $confirm_pass = $_POST['confirm_password'] ?? '';

    if ($new_pass && $confirm_pass) {
        if ($new_pass === $confirm_pass) {
            $hashed = password_hash($new_pass, PASSWORD_DEFAULT);
            $update = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
            $update->execute([$hashed, $user_id]);
            $success = "✅ Password updated successfully!";
        } else {
            $error = "❌ Passwords do not match.";
        }
    } else {
        $error = "⚠️ Please fill both fields.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>👤 Profile - Flower Shop</title>
  <style>
    body {
      margin:0;
      font-family: 'Segoe UI', sans-serif;
      display:flex;
      height:100vh;
      background: linear-gradient(135deg,#ff9a9e,#fad0c4,#a18cd1,#fbc2eb);
      background-size: 300% 300%;
      animation: gradientShift 10s ease infinite;
    }
    @keyframes gradientShift {
      0%{background-position:0% 50%}
      50%{background-position:100% 50%}
      100%{background-position:0% 50%}
    }
    .sidebar {
      width: 240px;
      background: linear-gradient(180deg,#a18cd1,#fbc2eb);
      padding: 20px;
      color: white;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      box-shadow: 3px 0 10px rgba(0,0,0,0.2);
    }
    .sidebar h2 { margin:0 0 20px; font-size:22px; text-align:center; }
    .sidebar a {
      display:block; color:white; text-decoration:none;
      margin:10px 0; padding:12px;
      border-radius:8px; font-weight:bold;
      transition: background 0.3s, transform 0.2s;
    }
    .sidebar a:hover { background: rgba(255,255,255,0.2); transform: translateX(5px); }
    .logout { background:#fff; color:#a18cd1 !important; text-align:center; font-weight:bold; }
    .main { flex:1; padding:30px; overflow:auto; }
    .card {
      background:white;
      padding:25px;
      border-radius:12px;
      max-width:600px;
      margin:auto;
      box-shadow:0 6px 15px rgba(0,0,0,0.1);
      animation: fadeIn 0.8s ease;
    }
    @keyframes fadeIn {
      from {opacity:0; transform:translateY(10px);}
      to {opacity:1; transform:translateY(0);}
    }
    h2 { margin-top:0; color:#6a0572; font-size:26px; }
    .info { margin:15px 0; }
    .info strong { display:inline-block; width:120px; color:#444; }
    .alert { padding:12px; border-radius:6px; margin:15px 0; font-size:14px; }
    .success { background:#d4edda; color:#155724; }
    .error { background:#f8d7da; color:#721c24; }
    form label { display:block; margin:10px 0 5px; font-weight:bold; }
    form input {
      width:100%; padding:10px;
      border:1px solid #ccc; border-radius:6px;
    }
    form button {
      margin-top:15px; width:100%;
      background:linear-gradient(45deg,#a18cd1,#fbc2eb);
      border:none; color:white; padding:12px;
      font-weight:bold; border-radius:8px; cursor:pointer;
      transition:0.3s;
    }
    form button:hover { background:#6a0572; }
  </style>
</head>
<body>

  <!-- Sidebar -->
  <div class="sidebar">
    <div>
      <h2>🌸 Flower Shop</h2>
      <a href="customer.php">🏠 Dashboard</a>
      <a href="my_orders.php">🛒 My Orders</a>
      <a href="profile.php">👤 Profile</a>
    </div>
    <a href="logout.php" class="logout">🚪 Logout</a>
  </div>

  <!-- Main content -->
  <div class="main">
    <div class="card">
      <h2>👤 My Profile</h2>

      <?php if ($success): ?>
        <div class="alert success"><?php echo $success; ?></div>
      <?php elseif ($error): ?>
        <div class="alert error"><?php echo $error; ?></div>
      <?php endif; ?>

      <div class="info"><strong>Username:</strong> <?php echo htmlspecialchars($user['username']); ?></div>
      <div class="info"><strong>Role:</strong> <?php echo ucfirst($user['role']); ?></div>
      <div class="info"><strong>Joined:</strong> <?php echo $user['created_at']; ?></div>

      <hr>
      <h3>🔑 Update Password</h3>
      <form method="post">
        <label>New Password</label>
        <input type="password" name="new_password" required>
        <label>Confirm Password</label>
        <input type="password" name="confirm_password" required>
        <button type="submit">Update Password</button>
      </form>
      
    </div>
  </div>

</body>
</html>
