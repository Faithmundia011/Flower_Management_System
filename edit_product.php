<?php
// edit_product.php
session_start();
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'admin') {
    header("Location: login.php");
    exit();
}
require 'config.php';

$id = $_GET['id'] ?? 0;

// Fetch product
$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    die("Product not found.");
}

// Handle update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name  = $_POST['name'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];
    $image = $product['image']; // keep old image

    if (!empty($_FILES['image']['name'])) {
        $targetDir = "uploads/";
        $image = time() . "_" . basename($_FILES["image"]["name"]);
        $targetFile = $targetDir . $image;
        move_uploaded_file($_FILES["image"]["tmp_name"], $targetFile);
    }

    $updateStmt = $pdo->prepare("UPDATE products SET name=?, price=?, stock=?, image=? WHERE id=?");
    $updateStmt->execute([$name, $price, $stock, $image, $id]);

    header("Location: admin.php?updated=1");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Edit Product</title>
  <style>
    body { font-family: Arial, sans-serif; background:#f4f7fa; }
    .container { width:50%; margin:40px auto; background:#fff; padding:20px; border-radius:8px; box-shadow:0 2px 6px rgba(0,0,0,0.1); }
    h2 { margin-bottom:20px; }
    input, button { width:100%; padding:10px; margin:10px 0; border:1px solid #ccc; border-radius:5px; }
    button { background:#4CAF50; color:white; border:none; font-weight:bold; }
  </style>
</head>
<body>
  <div class="container">
    <h2>Edit Product</h2>
    <form action="" method="post" enctype="multipart/form-data">
      <label>Flower Name:</label>
      <input type="text" name="name" value="<?php echo htmlspecialchars($product['name']); ?>" required>

      <label>Price:</label>
      <input type="number" name="price" value="<?php echo $product['price']; ?>" required>

      <label>Stock:</label>
      <input type="number" name="stock" value="<?php echo $product['stock']; ?>" required>

      <label>Change Image:</label>
      <input type="file" name="image" accept="image/*"><br>
      <?php if (!empty($product['image'])): ?>
        <img src="uploads/<?php echo $product['image']; ?>" width="100" style="margin:10px 0;">
      <?php endif; ?>

      <button type="submit">Update Product</button>
      <div class="tect-center my-4">
        <a href="customer.php"><i class="btn btn-secondary btn-lg"></i> Back to Dashboard</a>
    </div>
    </form>
  </div>
</body>
</html>
