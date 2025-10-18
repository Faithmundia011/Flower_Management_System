<?php
require 'config.php';

$status = $_GET['status'] ?? '';
$from_date = $_GET['from_date'] ?? '';
$to_date = $_GET['to_date'] ?? '';

$query = "SELECT o.id, u.username, p.name AS product_name, o.quantity, 
                 (p.price * o.quantity) AS total, o.payment_status, o.created_at 
          FROM orders o
          JOIN users u ON o.user_id = u.id
          JOIN products p ON o.product_id = p.id
          WHERE 1=1";
$params = [];

if ($status) {
  $query .= " AND o.payment_status = ?";
  $params[] = $status;
}
if ($from_date && $to_date) {
  $query .= " AND DATE(o.created_at) BETWEEN ? AND ?";
  $params[] = $from_date;
  $params[] = $to_date;
}

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

// CSV headers
header('Content-Type: text/csv');
header('Content-Disposition: attachment;filename="flower_report.csv"');

$output = fopen('php://output', 'w');
fputcsv($output, ['Order ID', 'Customer', 'Flower', 'Quantity', 'Total (KES)', 'Payment Status', 'Date']);

foreach ($rows as $r) {
  fputcsv($output, [
    $r['id'],
    $r['username'],
    $r['product_name'],
    $r['quantity'],
    $r['total'],
    $r['payment_status'],
    $r['created_at']
  ]);
}

fclose($output);
exit;
?>
