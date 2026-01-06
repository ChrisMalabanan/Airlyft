<?php
session_start();
require_once("config.php");

if (!isset($_SESSION['adminlogin'])) {
    header("Location: login.php");
    exit;
}

$table = $_POST['table'] ?? '';
$id = $_POST['id'] ?? '';
$primary_key = $_POST['pk'] ?? '';

if (!$table || !$id || !$primary_key) {
    die("Invalid form submission.");
}

// Prepare update
$fields = [];
$params = [];

foreach ($_POST as $key => $value) {
    if (!in_array($key, ['table', 'id', 'pk'])) {
        $fields[] = "`$key` = :$key";
        $params[":$key"] = $value;
    }
}
$params[":id"] = $id;

$set = implode(", ", $fields);
$sql = "UPDATE `$table` SET $set WHERE `$primary_key` = :id";

try {
    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    header("Location: admin_dashboard.php?table=$table&success=Record updated successfully.");
} catch (PDOException $e) {
    header("Location: admin_dashboard.php?table=$table&error=Update failed: " . urlencode($e->getMessage()));
}
exit;
