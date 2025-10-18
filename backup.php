<?php
session_start();
require 'config.php';

// ✅ Only admin can access backup
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

try {
    // Get database name from config
    $dbNameStmt = $pdo->query("SELECT DATABASE() AS db_name");
    $dbName = $dbNameStmt->fetch(PDO::FETCH_ASSOC)['db_name'];

    // Get all table names
    $tables = [];
    $stmt = $pdo->query("SHOW TABLES");
    while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
        $tables[] = $row[0];
    }

    // Start building SQL backup
    $backupSQL = "-- Database Backup for: $dbName\n";
    $backupSQL .= "-- Generated on: " . date('Y-m-d H:i:s') . "\n\n";
    $backupSQL .= "SET FOREIGN_KEY_CHECKS=0;\n\n";

    foreach ($tables as $table) {
        // Get table creation syntax
        $createTableStmt = $pdo->query("SHOW CREATE TABLE `$table`");
        $createTableRow = $createTableStmt->fetch(PDO::FETCH_ASSOC);
        $backupSQL .= "DROP TABLE IF EXISTS `$table`;\n";
        $backupSQL .= $createTableRow['Create Table'] . ";\n\n";

        // Get table data
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

    // Save the SQL file
    $filename = "backup_" . date("Y-m-d_H-i-s") . ".sql";
    $filePath = __DIR__ . "/$filename";
    file_put_contents($filePath, $backupSQL);

    // Send file for download
    header("Content-Description: File Transfer");
    header("Content-Type: application/octet-stream");
    header("Content-Disposition: attachment; filename=$filename");
    header("Content-Length: " . filesize($filePath));
    readfile($filePath);

    // Optional: delete the file after download
    unlink($filePath);
    exit;

} catch (Exception $e) {
    echo "<h3 style='color:red;'>Backup failed: " . htmlspecialchars($e->getMessage()) . "</h3>";
}
?>
