<?php
session_start();
require_once("config.php");

if (!isset($_SESSION['adminlogin'])) {
    header("Location: login.php");
    exit;
}

$table = $_GET['table'] ?? '';
if (!$table) die("No table selected.");

$stmt = $db->query("SHOW COLUMNS FROM `$table`");
$columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add New <?php echo ucfirst($table); ?> Record</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light p-5">
<div class="container bg-white p-4 shadow rounded" style="max-width: 700px;">
    <h3>Add New Record to <strong><?php echo ucfirst($table); ?></strong></h3>
    <form method="POST" action="insert_record.php">
        <input type="hidden" name="table" value="<?php echo $table; ?>">

        <?php foreach ($columns as $col): 
            $field = $col['Field'];
            $autoIncrement = strpos($col['Extra'], 'auto_increment') !== false;
            if ($autoIncrement) continue; // skip primary key if auto
        ?>
            <div class="mb-3">
                <label class="form-label"><?php echo $field; ?></label>
                <input type="text" name="<?php echo $field; ?>" class="form-control" required>
            </div>
        <?php endforeach; ?>

        <button type="submit" class="btn btn-success">Add Record</button>
        <a href="admin_dashboard.php?table=<?php echo $table; ?>" class="btn btn-secondary">Cancel</a>
    </form>
</div>
</body>
</html>
