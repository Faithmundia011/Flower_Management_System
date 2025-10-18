<?php
session_start();
require 'config.php';

// Only allow admin to add products
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$success = $error = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $price = $_POST['price'] ?? '';
    $image = "";

    if (!empty($_FILES['image']['name'])) {
        $targetDir = "uploads/";
        $image = time() . "_" . basename($_FILES['image']['name']);
        move_uploaded_file($_FILES['image']['tmp_name'], $targetDir . $image);
    }

    if ($name && $price) {
        $stmt = $pdo->prepare("INSERT INTO products (name, price, image) VALUES (?,?,?)");
        if ($stmt->execute([$name, $price, $image])) {
            $success = "✅ Product added successfully!";
        } else {
            $error = "❌ Failed to add product.";
        }
    } else {
        $error = "⚠️ Please fill in all fields.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>➕ Add Product</title>
  <style>
    body { margin:0; display:flex; height:100vh; font-family:sans-serif; background:linear-gradient(135deg,#ffe6f1,#e0b3ff); }
    
    /* Sidebar */
    .sidebar { 
      width:220px; 
      background:linear-gradient(180deg,#ff6f91,#c77dff); 
      padding:20px; 
      color:white; 
      display:flex; 
      flex-direction:column; 
      justify-content:space-between;
      box-shadow: 2px 0 8px rgba(0,0,0,0.1);
    }
    .sidebar h2 { text-align:center; margin-bottom:20px; font-size:18px; }
    .sidebar a { display:block; padding:12px; margin:8px 0; border-radius:8px; color:white; text-decoration:none; font-weight:bold; transition:0.3s; }
    .sidebar a:hover { background:rgba(255,255,255,0.2); }
    .logout { background:white; color:#ff4b5c !important; text-align:center; }
    
    /* Main */
    .main { flex:1; padding:30px; overflow:auto; }
    .card { 
      background:white; 
      padding:25px; 
      border-radius:12px; 
      box-shadow:0 4px 12px rgba(0,0,0,0.15); 
      max-width:500px; 
      margin:auto; 
      animation: fadeIn 0.6s ease-in-out;
    }
    @keyframes fadeIn {
      from { opacity:0; transform:translateY(20px); }
      to { opacity:1; transform:translateY(0); }
    }
    
    h2 { color:#a64ca6; text-align:center; margin-bottom:20px; }
    form label { display:block; margin:10px 0 5px; font-weight:bold; color:#555; }
    form input { width:100%; padding:10px; border:1px solid #ccc; border-radius:6px; }
    form button { 
      margin-top:15px; width:100%; padding:12px; 
      background:linear-gradient(90deg,#ff6f91,#c77dff); 
      border:none; color:white; font-weight:bold; 
      border-radius:6px; cursor:pointer; 
      transition:0.3s;
    }
    form button:hover { opacity:0.85; }
    
    /* Alerts */
    .alert { padding:10px; margin:10px 0; border-radius:6px; }
    .success { background:#d4edda; color:#155724; }
    .error { background:#f8d7da; color:#721c24; }
  </style>
</head>
<body>
  <div class="sidebar">
    <div>
      <h2>🌸 Admin Panel</h2>
      <a href="admin.php">🏠 Dashboard</a>
      <a href="product_list.php">📦 Product List</a>
      <a href="add_product.php">➕ Add Product</a>
      <a href="customer_orders.php">🛒 Orders</a>
      <a href="profile.php">👤 Profile</a>
    </div>
    <a href="logout.php" class="logout">🚪 Logout</a>
  </div>

  <div class="main">
    <div class="card">
      <h2>➕ Add New Product</h2>
      <?php if ($success): ?><div class="alert success"><?= $success ?></div><?php endif; ?>
      <?php if ($error): ?><div class="alert error"><?= $error ?></div><?php endif; ?>

      <form method="post" enctype="multipart/form-data">
        <label>Flower Name</label>
        <input type="text" name="name" required>

        <label>Price (KES)</label>
        <input type="number" step="0.01" name="price" required>

        <label>Image</label>
        <input type="file" name="image">

        <button type="submit">Add Product</button>
      </form>
    </div>
  </div>
</body>
</html>
