<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['sql_file'])) {
    $file = $_FILES['sql_file']['tmp_name'];

    if ($_FILES['sql_file']['type'] !== 'application/octet-stream' && pathinfo($_FILES['sql_file']['name'], PATHINFO_EXTENSION) !== 'sql') {
        header("Location: admin_dashboard.php?error=Invalid file format. Please upload a .sql file.");
        exit;
    }

    // Import using mysql command line
    $mysql = 'C:\\xampp\\mysql\\bin\\mysql.exe';
	$command = "$mysql -u root airlyft < " . escapeshellarg($file);

    exec($command, $output, $result);

    if ($result === 0) {
        header("Location: admin_dashboard.php?success=Database restored successfully.");
    } else {
        header("Location: admin_dashboard.php?error=Restore failed. Make sure mysql CLI is configured.");
    }
} else {
    header("Location: admin_dashboard.php?error=No file uploaded.");
}
exit;
