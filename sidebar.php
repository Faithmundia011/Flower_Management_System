<div class="sidebar">
  <h2>🌸 Admin Panel</h2>
  <ul>
    <li><a href="admin.php">🏠 Dashboard</a></li>
    <li><a href="add_product.php">➕ Add Product</a></li>
    <li><a href="product_list.php">📦 Product List</a></li>
    <li><a href="customer_orders.php">🛒 Customer Orders</a></li>
    <li><a href="admin_reports.php">📊 Sales Reports</a></li>
    <li><a href="logout.php">🚪 Logout</a></li>
    
  </ul>
</div>

<style>
.sidebar {
  width: 220px;
  height: 100vh;
  position: fixed;
  left: 0; top: 0;
  background: linear-gradient(180deg,#2E8B57,#3CB371);
  padding: 20px;
  color: white;
}
.sidebar h2 { font-size: 18px; margin-bottom: 20px; }
.sidebar ul { list-style: none; padding: 0; }
.sidebar ul li { margin: 15px 0; }
.sidebar ul li a {
  color: white;
  text-decoration: none;
  font-weight: bold;
  display: block;
  padding: 8px 12px;
  border-radius: 6px;
  transition: 0.3s;
}
.sidebar ul li a:hover {
  background: #4CAF50;
}
</style>
