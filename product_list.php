<?php
// product_list.php
session_start();
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'admin') {
  header("Location: login.php");
  exit;
}
require 'config.php';

/* ----------------- HANDLE PRODUCT UPDATES ----------------- */
// Inline product update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'update_product') {
  $id    = $_POST['id'] ?? 0;
  $price = $_POST['price'] ?? 0;
  $stock = $_POST['stock'] ?? 0;

  $stmt = $pdo->prepare("UPDATE products SET price = ?, stock = ? WHERE id = ?");
  $stmt->execute([$price, $stock, $id]);
  $success = "✅ Product updated successfully!";
}

// Toggle product status
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'toggle_status') {
  $id = $_POST['id'] ?? 0;
  $status = $_POST['status'] ?? 0;

  $stmt = $pdo->prepare("UPDATE products SET is_active = ? WHERE id = ?");
  $stmt->execute([$status, $id]);
  $success = "✅ Product status updated!";
}

/* ----------------- SEARCH & FILTER ----------------- */
$search = trim($_GET['search'] ?? '');
$filter = $_GET['filter'] ?? 'all';

$query = "SELECT * FROM products WHERE 1=1";
$params = [];

if ($search !== '') {
  $query .= " AND name LIKE ?";
  $params[] = "%$search%";
}
if ($filter === 'active') {
  $query .= " AND is_active = 1";
} elseif ($filter === 'hidden') {
  $query .= " AND is_active = 0";
}

$query .= " ORDER BY created_at DESC";
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Product List</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <style>
    * { margin: 0; padding: 0; box-sizing: border-box; }

    body {
      font-family: 'Segoe UI', sans-serif;
      background: linear-gradient(135deg, #ff9a9e, #8e44ad);
      background-size: 400% 400%;
      animation: gradientMove 12s ease infinite;
      min-height: 100vh;
      display: flex;
    }

    @keyframes gradientMove {
      0% { background-position: 0% 50%; }
      50% { background-position: 100% 50%; }
      100% { background-position: 0% 50%; }
    }

    /* Sidebar */
    .sidebar {
      width: 240px;
      height: 100vh;
      position: fixed;
      left: 0; top: 0;
      background: rgba(142, 68, 173, 0.85);
      backdrop-filter: blur(12px);
      padding: 30px 20px;
      color: white;
      box-shadow: 5px 0 20px rgba(0,0,0,0.2);
    }
    .sidebar h2 {
      margin-bottom: 30px;
      font-size: 20px;
      text-align: center;
      color: #f8eaf6;
    }
    .sidebar a {
      display: block;
      padding: 12px 18px;
      margin: 10px 0;
      text-decoration: none;
      color: #f8eaf6;
      font-weight: bold;
      border-radius: 10px;
      transition: all 0.3s ease;
    }
    .sidebar a:hover {
      background: linear-gradient(90deg, #ff758c, #a29bfe);
      color: #fff;
      transform: translateX(5px);
    }

    .content {
      margin-left: 260px;
      padding: 40px;
      flex: 1;
    }

    h2 {
      color: #fff;
      margin-bottom: 20px;
    }

    .alert {
      padding: 12px;
      border-radius: 8px;
      margin: 15px 0;
      font-weight: bold;
    }
    .success {
      background: #d4edda;
      color: #155724;
      border: 1px solid #c3e6cb;
    }

    /* Filter */
    .filter-bar {
      display: flex;
      gap: 10px;
      margin-bottom: 20px;
    }
    .filter-bar input, .filter-bar select, .filter-bar button {
      padding: 10px;
      border-radius: 8px;
      border: 1px solid #ccc;
      font-size: 0.9em;
    }
    .filter-bar button {
      background: #ff6f91;
      border: none;
      color: #fff;
      cursor: pointer;
      font-weight: bold;
      transition: background 0.3s;
    }
    .filter-bar button:hover {
      background: #e65b80;
    }

    /* Table */
    table {
      width: 100%;
      border-collapse: collapse;
      background: #fff;
      border-radius: 12px;
      overflow: hidden;
      box-shadow: 0 8px 20px rgba(0,0,0,0.15);
    }
    th, td {
      padding: 14px;
      border-bottom: 1px solid #eee;
      text-align: center;
    }
    th {
      background: #f9f1f9;
      color: #8e44ad;
      font-size: 1em;
    }
    img.thumb {
      width: 60px;
      height: 60px;
      object-fit: cover;
      border-radius: 8px;
      border: 2px solid #ddd;
    }

    /* Inline form */
    .inline-form input {
      width: 70px;
      padding: 6px;
      border-radius: 6px;
      border: 1px solid #ccc;
      text-align: center;
    }
    .inline-form button {
      padding: 6px 12px;
      border: none;
      border-radius: 6px;
      background: #8e44ad;
      color: white;
      cursor: pointer;
      font-weight: bold;
      transition: 0.3s;
    }
    .inline-form button:hover {
      background: #a55eea;
    }

    /* Toggle switch */
    .switch {
      position: relative;
      display: inline-block;
      width: 50px;
      height: 24px;
    }
    .switch input { opacity: 0; width: 0; height: 0; }
    .slider {
      position: absolute;
      cursor: pointer;
      top: 0; left: 0; right: 0; bottom: 0;
      background-color: #ccc;
      transition: .4s;
      border-radius: 24px;
    }
    .slider:before {
      position: absolute;
      content: "";
      height: 18px; width: 18px;
      left: 3px; bottom: 3px;
      background-color: white;
      transition: .4s;
      border-radius: 50%;
    }
    input:checked + .slider { background-color: #ff6f91; }
    input:checked + .slider:before { transform: translateX(26px); }
  </style>
</head>
<body>

  <!-- Sidebar -->
  <div class="sidebar">
    <h2>🌸 Admin Panel</h2>
    <a href="admin.php"><i class="fas fa-home"></i> Dashboard</a>
    <a href="product_list.php"><i class="fas fa-box"></i> Products</a>
    <a href="add_product.php"><i class="fas fa-plus-circle"></i> Add Product</a>
    <a href="customer_orders.php"><i class="fas fa-shopping-cart"></i> Orders</a>
    <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
  </div>

  <div class="content">
    <h2>📦 Product List</h2>

    <?php if (!empty($success)): ?>
      <div class="alert success"><?php echo $success; ?></div>
    <?php endif; ?>

    <!-- Search & Filter -->
    <form method="get" class="filter-bar">
      <input type="text" name="search" placeholder="Search..." value="<?php echo htmlspecialchars($search); ?>">
      <select name="filter">
        <option value="all" <?php if ($filter==='all') echo 'selected'; ?>>All</option>
        <option value="active" <?php if ($filter==='active') echo 'selected'; ?>>Active</option>
        <option value="hidden" <?php if ($filter==='hidden') echo 'selected'; ?>>Hidden</option>
      </select>
      <button type="submit"><i class="fas fa-filter"></i> Apply</button>
    </form>

    <table>
      <tr>
        <th>ID</th>
        <th>Flower</th>
        <th>Price (Ksh)</th>
        <th>Stock</th>
        <th>Image</th>
        <th>Status</th>
        <th>Actions</th>
      </tr>
      <?php foreach ($products as $product): ?>
        <tr>
          <td><?php echo $product['id']; ?></td>
          <td><?php echo htmlspecialchars($product['name']); ?></td>
          <td colspan="2">
            <form method="post" action="product_list.php" class="inline-form" style="display:flex; gap:12px; justify-content:center;">
              <input type="hidden" name="action" value="update_product">
              <input type="hidden" name="id" value="<?php echo $product['id']; ?>">

              <div>
                <label style="font-size:12px; color:#555;">Price</label><br>
                <input type="number" name="price" value="<?php echo $product['price']; ?>" step="0.01">
              </div>

              <div>
                <label style="font-size:12px; color:#555;">Stock</label><br>
                <input type="number" name="stock" value="<?php echo $product['stock']; ?>">
              </div>

              <button type="submit">Update</button>
            </form>
          </td>
          <td>
            <?php if (!empty($product['image'])): ?>
              <img src="uploads/<?php echo $product['image']; ?>" class="thumb">
            <?php else: ?> No Image <?php endif; ?>
          </td>
          <td>
            <form method="post" action="product_list.php">
              <input type="hidden" name="action" value="toggle_status">
              <input type="hidden" name="id" value="<?php echo $product['id']; ?>">
              <label class="switch">
                <input type="checkbox" name="status" value="1" <?php echo $product['is_active'] ? 'checked' : ''; ?> onchange="this.form.submit()">
                <span class="slider"></span>
              </label>
            </form>
          </td>
          <td>
            <a href="edit_product.php?id=<?php echo $product['id']; ?>" style="color:#8e44ad; font-weight:bold; text-decoration:none;">Edit</a>
          </td>
        </tr>
      <?php endforeach; ?>
    </table>
  </div>
</body>
</html>
