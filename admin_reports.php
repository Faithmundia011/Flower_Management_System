<?php
session_start();
require 'config.php';

// Fetch orders (make sure 'products' table exists)
$stmt = $pdo->prepare("
    SELECT 
        o.id,
        u.username AS customer_name,
        p.name AS product_name,
        o.quantity,
        o.price,
        o.total_price,
        o.payment_status,
        o.status,
        o.order_date
    FROM orders o
    JOIN users u ON o.user_id = u.id
    JOIN products p ON o.products_id = p.id
    ORDER BY o.order_date DESC
");
$stmt->execute();
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

// CSV Export
if (isset($_GET['export']) && $_GET['export'] === 'csv') {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment;filename="flower_orders_report.csv"');

    $output = fopen('php://output', 'w');
    if (!empty($orders)) {
        fputcsv($output, array_keys($orders[0]));
        foreach ($orders as $row) {
            fputcsv($output, $row);
        }
    } else {
        fputcsv($output, ['No data available']);
    }
    fclose($output);
    exit;
}

// PDF Export (manual using basic HTML-to-PDF output)
if (isset($_GET['export']) && $_GET['export'] === 'pdf') {
    require_once __DIR__ . '/fpdf/fpdf.php';

    class PDF extends FPDF {
        function Header() {
            $this->SetFont('Arial', 'B', 14);
            $this->SetTextColor(138, 43, 226);
            $this->Cell(0, 10, '🌸 Flower Orders Report', 0, 1, 'C');
            $this->Ln(5);
        }
    }

    $pdf = new PDF();
    $pdf->AddPage();
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->SetFillColor(255, 182, 193); // Pink Header
    $pdf->Cell(15, 10, 'ID', 1, 0, 'C', true);
    $pdf->Cell(35, 10, 'Customer', 1, 0, 'C', true);
    $pdf->Cell(35, 10, 'Product', 1, 0, 'C', true);
    $pdf->Cell(15, 10, 'Qty', 1, 0, 'C', true);
    $pdf->Cell(25, 10, 'Price', 1, 0, 'C', true);
    $pdf->Cell(25, 10, 'Total', 1, 0, 'C', true);
    $pdf->Cell(25, 10, 'Payment', 1, 0, 'C', true);
    $pdf->Cell(25, 10, 'Status', 1, 1, 'C', true);

    $pdf->SetFont('Arial', '', 9);
    foreach ($orders as $row) {
        $pdf->Cell(15, 8, $row['id'], 1);
        $pdf->Cell(35, 8, $row['customer_name'], 1);
        $pdf->Cell(35, 8, $row['product_name'], 1);
        $pdf->Cell(15, 8, $row['quantity'], 1);
        $pdf->Cell(25, 8, 'KES ' . number_format($row['price'], 2), 1);
        $pdf->Cell(25, 8, 'KES ' . number_format($row['total_price'], 2), 1);
        $pdf->Cell(25, 8, $row['payment_status'], 1);
        $pdf->Cell(25, 8, $row['status'], 1, 1);
    }

    $pdf->Output('D', 'flower_orders_report.pdf');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Admin Reports - Flower Management</title>
<style>
    body {
        font-family: 'Segoe UI', sans-serif;
        background: linear-gradient(135deg, #ffccf9, #cbb4ff, #d5a6e6);
        color: #333;
        margin: 0;
        display: flex;
        height: 100vh;
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
    h2 {
        margin-bottom: 20px;
        color: #4a004e;
    }
    table {
        width: 100%;
        border-collapse: collapse;
        background: white;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
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
    .export-buttons {
        margin-bottom: 20px;
    }
    .export-buttons a {
        background: linear-gradient(45deg, #ff6fd8, #3813c2);
        color: white;
        padding: 10px 16px;
        border-radius: 6px;
        text-decoration: none;
        margin-right: 10px;
        font-weight: bold;
        transition: transform 0.2s;
    }
    .export-buttons a:hover {
        transform: translateY(-2px);
        opacity: 0.9;
    }
</style>
</head>
<body>
<div class="sidebar">
    <div>
        <h2>🌸 Admin</h2>
        <a href="admin.php">🏠 Dashboard</a>
        <a href="add_product.php">➕ Add Product</a>
        <a href="product_list.php">🌼 Product List</a>
        <a href="customer_orders.php">📋 Orders</a>
        <a href="admin_reports.php">📊 Reports</a>
    </div>
    <a href="logout.php" class="logout">🚪 Logout</a>
</div>

<div class="main">
    <h2>📊 Sales & Order Reports</h2>
    <div class="export-buttons">
        <a href="?export=pdf">⬇️ Export as PDF</a>
        <a href="?export=csv">⬇️ Export as CSV</a>
    </div>

    <table>
        <tr>
            <th>ID</th>
            <th>Customer</th>
            <th>Product</th>
            <th>Qty</th>
            <th>Price (KES)</th>
            <th>Total (KES)</th>
            <th>Payment</th>
            <th>Status</th>
            <th>Date</th>
        </tr>
        <?php if (!empty($orders)): ?>
            <?php foreach ($orders as $row): ?>
                <tr>
                    <td><?= htmlspecialchars($row['id']) ?></td>
                    <td><?= htmlspecialchars($row['customer_name']) ?></td>
                    <td><?= htmlspecialchars($row['product_name']) ?></td>
                    <td><?= htmlspecialchars($row['quantity']) ?></td>
                    <td><?= number_format($row['price'], 2) ?></td>
                    <td><?= number_format($row['total_price'], 2) ?></td>
                    <td><?= htmlspecialchars($row['payment_status']) ?></td>
                    <td><?= htmlspecialchars($row['status']) ?></td>
                    <td><?= htmlspecialchars($row['order_date']) ?></td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="9"><em>No orders available</em></td></tr>
        <?php endif; ?>
    </table>
</div>
</body>
</html>
