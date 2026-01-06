<?php
session_start();
require_once("config.php");

if (!isset($_SESSION['adminlogin'])) {
    header("Location: login.php");
    exit;
}

$table = $_POST['table'] ?? '';
if (!$table) die("No table submitted.");

// Collect all fields
$fields = [];
$placeholders = [];
$params = [];

foreach ($_POST as $key => $value) {
    if ($key == 'table') continue;
    $fields[] = "`$key`";
    $placeholders[] = ":$key";
    $params[":$key"] = $value;
}

$sql = "INSERT INTO `$table` (" . implode(",", $fields) . ") VALUES (" . implode(",", $placeholders) . ")";

try {
    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    header("Location: admin_dashboard.php?table=$table&success=New record added to $table.");
} catch (PDOException $e) {
    header("Location: admin_dashboard.php?table=$table&error=Insert failed: " . urlencode($e->getMessage()));
}
exit;
