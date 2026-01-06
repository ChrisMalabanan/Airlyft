<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
require_once("config.php");

if (!isset($_SESSION['adminlogin'])) {
    header("Location: login.php");
    exit;
}

$table = $_GET['table'] ?? '';
$id = $_GET['id'] ?? '';
$primary_key = $_GET['pk'] ?? '';

$allowed_tables = ['users', 'addresses', 'booking', 'lift', 'passengers', 'payments', 'places', 'schedule'];
if (!in_array($table, $allowed_tables)) {
    header("Location: admin_dashboard.php?table=$table&error=Invalid table.");
    exit;
}

if (empty($id) || empty($primary_key)) {
    header("Location: admin_dashboard.php?table=$table&error=Missing ID or primary key.");
    exit;
}

try {
    // Check if the record exists
    $stmt = $db->prepare("SELECT * FROM `$table` WHERE `$primary_key` = :id");
    $stmt->execute([':id' => $id]);
    if (!$stmt->fetch()) {
        header("Location: admin_dashboard.php?table=$table&error=Record not found.");
        exit;
    }

    // Foreign key rules from your database
    $constraints = [
        'users' => [['table' => 'booking', 'column' => 'User_Id']],
        'addresses' => [['table' => 'passengers', 'column' => 'Address_Id']],
        'booking' => [
            ['table' => 'passengers', 'column' => 'Booking_Id'],
            ['table' => 'payments', 'column' => 'Booking_id']
        ],
        'lift' => [
            ['table' => 'booking', 'column' => 'Aircraft_Id'],
            ['table' => 'schedule', 'column' => 'Aircraft_Id']
        ],
        'schedule' => [['table' => 'booking', 'column' => 'Sched_Id']],
    ];

    if (isset($constraints[$table])) {
        foreach ($constraints[$table] as $constraint) {
            $chk = $db->prepare("SELECT COUNT(*) FROM `{$constraint['table']}` WHERE `{$constraint['column']}` = :id");
            $chk->execute([':id' => $id]);
            if ($chk->fetchColumn() > 0) {
                header("Location: admin_dashboard.php?table=$table&error=Cannot delete. Related records exist in '{$constraint['table']}'.");
                exit;
            }
        }
    }

    // Perform deletion
    $del = $db->prepare("DELETE FROM `$table` WHERE `$primary_key` = :id");
    $del->execute([':id' => $id]);

    header("Location: admin_dashboard.php?table=$table&success=Record deleted from $table.");
    exit;

} catch (PDOException $e) {
    header("Location: admin_dashboard.php?table=$table&error=PDO Error: " . urlencode($e->getMessage()));
    exit;
}
