<?php
session_start();
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'admin') {
  header("Location: login.php");
  exit;
}
require 'config.php'; 
require_once __DIR__ . '/fpdf/fpdf.php'; // ✅ Ensure FPDF library is available

// ✅ Handle Database Backup
if (isset($_POST['backup'])) {
    try {
        $dbNameStmt = $pdo->query("SELECT DATABASE() AS db_name");
        $dbName = $dbNameStmt->fetch(PDO::FETCH_ASSOC)['db_name'];

        $tables = [];
        $stmt = $pdo->query("SHOW TABLES");
        while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $tables[] = $row[0];
        }

        $backupSQL = "-- Database Backup for: $dbName\n";
        $backupSQL .= "-- Generated on: " . date('Y-m-d H:i:s') . "\n\n";
        $backupSQL .= "SET FOREIGN_KEY_CHECKS=0;\n\n";

        foreach ($tables as $table) {
            $createTableStmt = $pdo->query("SHOW CREATE TABLE `$table`");
            $createTableRow = $createTableStmt->fetch(PDO::FETCH_ASSOC);
            $backupSQL .= "DROP TABLE IF EXISTS `$table`;\n";
            $backupSQL .= $createTableRow['Create Table'] . ";\n\n";

            $dataStmt = $pdo->query("SELECT * FROM `$table`");
            $rows = $dataStmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($rows as $row) {
                $values = array_map(function ($value) use ($pdo) {
                    return isset($value) ? $pdo->quote($value) : 'NULL';
                }, $row);
                $backupSQL .= "INSERT INTO `$table` (`" . implode("`, `", array_keys($row)) . "`) VALUES (" . implode(", ", $values) . ");\n";
            }
            $backupSQL .= "\n";
        }

        $backupSQL .= "SET FOREIGN_KEY_CHECKS=1;\n";
        $filename = "backup_" . date("Y-m-d_H-i-s") . ".sql";
        $filePath = __DIR__ . "/$filename";
        file_put_contents($filePath, $backupSQL);

        header("Content-Description: File Transfer");
        header("Content-Type: application/octet-stream");
        header("Content-Disposition: attachment; filename=$filename");
        header("Content-Length: " . filesize($filePath));
        readfile($filePath);
        unlink($filePath);
        exit;
    } catch (Exception $e) {
        echo "<script>alert('Backup failed: " . htmlspecialchars($e->getMessage()) . "');</script>";
    }
}

// ✅ Handle Report Generation
if (isset($_POST['report'])) {
    class PDF extends FPDF {
        function Header() {
            $this->SetFont('Arial','B',16);
            $this->Cell(0,10,'🌸 Flower Shop - Sales Report',0,1,'C');
            $this->Ln(5);
        }
        function Footer() {
            $this->SetY(-15);
            $this->SetFont('Arial','I',8);
            $this->Cell(0,10,'Generated on '.date('Y-m-d H:i:s'),0,0,'C');
        }
    }

    $query = $pdo->query("
        SELECT 
            o.id AS order_id,
            u.username AS customer_name,
            p.name AS flower_name,
            o.quantity,
            (o.quantity * p.price) AS total_price,
            o.payment_status
        FROM orders o
        JOIN users u ON o.user_id = u.id
        JOIN products p ON o.products_id = p.id
        ORDER BY o.id DESC
    ");
    $orders = $query->fetchAll(PDO::FETCH_ASSOC);

    $pdf = new PDF();
    $pdf->AddPage();
    $pdf->SetFont('Arial','B',12);
    $pdf->SetFillColor(230,230,250);
    $pdf->Cell(20,10,'ID',1,0,'C',true);
    $pdf->Cell(40,10,'Customer',1,0,'C',true);
    $pdf->Cell(50,10,'Flower',1,0,'C',true);
    $pdf->Cell(30,10,'Quantity',1,0,'C',true);
    $pdf->Cell(30,10,'Total (KES)',1,0,'C',true);
    $pdf->Cell(30,10,'Status',1,1,'C',true);

    $pdf->SetFont('Arial','',11);
    foreach ($orders as $row) {
        $pdf->Cell(20,10,$row['order_id'],1);
        $pdf->Cell(40,10,utf8_decode($row['customer_name']),1);
        $pdf->Cell(50,10,utf8_decode($row['flower_name']),1);
        $pdf->Cell(30,10,$row['quantity'],1);
        $pdf->Cell(30,10,number_format($row['total_price'],2),1);
        $pdf->Cell(30,10,$row['payment_status'],1,1);
    }

    $filename = "report_" . date("Y-m-d_H-i-s") . ".pdf";
    $pdf->Output('D', $filename);
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Dashboard</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <style>
    body {
      font-family: "Poppins", "Segoe UI", sans-serif;
      min-height: 100vh;
      display: flex;
      margin: 0;
      background: linear-gradient(135deg, #ff9a9e, #fad0c4, #a18cd1);
      background-size: 400% 400%;
      animation: gradientFlow 15s ease infinite;
      color: #333;
    }
    @keyframes gradientFlow {
      0% { background-position: 0% 50%; }
      50% { background-position: 100% 50%; }
      100% { background-position: 0% 50%; }
    }

    /* Sidebar */
    .sidebar {
      position: fixed;
      top: 0;
      left: 0;
      width: 250px;
      height: 100%;
      background: rgba(142, 68, 173, 0.92);
      box-shadow: 4px 0 20px rgba(0,0,0,0.2);
      padding: 35px 20px;
      display: flex;
      flex-direction: column;
      align-items: center;
      border-top-right-radius: 20px;
      border-bottom-right-radius: 20px;
      animation: fadeIn 1s ease;
    }
    @keyframes fadeIn {
      from { opacity: 0; transform: translateX(-30px); }
      to { opacity: 1; transform: translateX(0); }
    }
    .sidebar h2 {
      color: #fff;
      font-size: 1.6em;
      margin-bottom: 25px;
    }
    .sidebar a {
      width: 100%;
      padding: 12px 20px;
      color: #f4e1ff;
      text-decoration: none;
      border-radius: 10px;
      font-size: 1.1em;
      margin: 6px 0;
      transition: all 0.3s ease;
      display: flex;
      align-items: center;
    }
    .sidebar a i {
      margin-right: 12px;
      font-size: 1.2em;
    }
    .sidebar a:hover {
      background: linear-gradient(90deg, #ff758c, #a29bfe);
      transform: scale(1.05);
      color: #fff;
    }

    /* Content */
    .content {
      margin-left: 260px;
      padding: 60px 40px;
      flex-grow: 1;
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
    }

    .card {
      background: rgba(255, 255, 255, 0.95);
      padding: 45px;
      border-radius: 25px;
      box-shadow: 0 15px 45px rgba(0,0,0,0.2);
      max-width: 650px;
      text-align: center;
      transition: transform 0.4s ease, box-shadow 0.4s ease;
      backdrop-filter: blur(10px);
    }
    .card:hover {
      transform: translateY(-8px);
      box-shadow: 0 25px 60px rgba(0,0,0,0.25);
    }

    h1 {
      font-size: 2.5em;
      color: #8e44ad;
      margin-bottom: 15px;
    }

    .backup-btn, .report-btn {
      margin-top: 15px;
      padding: 12px 20px;
      width: 100%;
      background: linear-gradient(45deg, #8e44ad, #c850c0);
      color: white;
      border: none;
      border-radius: 10px;
      font-size: 1.1em;
      cursor: pointer;
      transition: 0.3s;
      box-shadow: 0 4px 10px rgba(0,0,0,0.2);
    }
    .backup-btn:hover, .report-btn:hover {
      background: linear-gradient(45deg, #43cea2, #185a9d);
      transform: scale(1.05);
    }

    .sidebar form {
      width: 100%;
      margin-top: 15px;
    }

    .logout-link {
      margin-top: auto;
      background: rgba(255,255,255,0.15);
      padding: 10px 20px;
      border-radius: 10px;
      color: #fff;
      text-decoration: none;
      font-weight: bold;
      transition: 0.3s;
    }
    .logout-link:hover {
      background: #e74c3c;
      color: #fff;
      transform: scale(1.05);
    }
  </style>
</head>
<body>
  <div class="sidebar">
    <h2>🌸 Admin Panel</h2>
    <a href="admin.php"><i class="fas fa-home"></i> Dashboard</a>
    <a href="admin_orders.php"><i class="fas fa-shopping-cart"></i> Customer Orders</a>
    <a href="product_list.php"><i class="fas fa-seedling"></i> Manage Products</a>

    <form method="post">
        <button type="submit" name="backup" class="backup-btn">
            <i class="fas fa-database"></i> Backup Database
        </button>
    </form>
    <form method="post">
        <button type="submit" name="report" class="report-btn">
            <i class="fas fa-file-pdf"></i> Generate Report
        </button>
    </form>

    <a href="logout.php" class="logout-link"><i class="fas fa-sign-out-alt"></i> Logout</a>
  </div>

  <div class="content">
    <div class="card">
      <h1>🌺 Welcome Admin</h1>
      <p>Manage your flower shop efficiently. Use the sidebar to navigate, backup data, or generate reports seamlessly.</p>
    </div>
  </div>
</body>
</html>
