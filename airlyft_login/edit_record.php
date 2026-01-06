<?php
session_start();
require_once("config.php");

if (!isset($_SESSION['adminlogin'])) {
    header("Location: login.php");
    exit;
}

$table = $_GET['table'] ?? '';
$id = $_GET['id'] ?? '';
$primary_key = $_GET['pk'] ?? '';

if (!$table || !$id || !$primary_key) {
    die("Invalid request.");
}

// Fetch columns
$stmt = $db->query("SHOW COLUMNS FROM `$table`");
$columns = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch current data
$stmt = $db->prepare("SELECT * FROM `$table` WHERE `$primary_key` = :id");
$stmt->execute([':id' => $id]);
$data = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$data) {
    die("Record not found.");
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit <?php echo ucfirst($table); ?> Record</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light p-5">
<div class="container bg-white p-4 shadow rounded" style="max-width: 700px;">
    <h3>Edit Record from <strong><?php echo ucfirst($table); ?></strong></h3>
    <form method="POST" action="update_record.php">
        <input type="hidden" name="table" value="<?php echo $table; ?>">
        <input type="hidden" name="pk" value="<?php echo $primary_key; ?>">
        <input type="hidden" name="id" value="<?php echo $id; ?>">

		<?php foreach ($columns as $col): 
			$field = $col['Field'];

			// SKIP password field only for the 'users' table
			if ($table === 'users' && $field === 'password') continue;

			$value = htmlspecialchars($data[$field] ?? '');
			$readonly = $field == $primary_key ? 'readonly' : '';
		?>
			<div class="mb-3">
				<label class="form-label"><?php echo $field; ?></label>
				<input type="text" name="<?php echo $field; ?>" class="form-control" value="<?php echo $value; ?>" <?php echo $readonly; ?>>
			</div>
		<?php endforeach; ?>


        <button type="submit" class="btn btn-success">Update Record</button>
        <a href="admin_dashboard.php?table=<?php echo $table; ?>" class="btn btn-secondary">Cancel</a>
    </form>
</div>
</body>
</html>
