<?php
date_default_timezone_set('Asia/Manila');

$filename = 'backup_airlyft_' . date('Y-m-d_H-i-s') . '.sql';
$mysqldump = 'C:\\xampp\\mysql\\bin\\mysqldump.exe'; // double backslashes!
$command = "$mysqldump -u root airlyft > backups/$filename";

// Make sure the backups folder exists
if (!file_exists('backups')) {
    mkdir('backups', 0777, true);
}

exec($command, $output, $return_var);

if ($return_var === 0) {
    // If AJAX, return JSON
    if (isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
        echo json_encode([
            'status' => 'success',
            'message' => 'Backup created successfully!',
            'filename' => $filename,
            'time' => date("F j, Y \\a\\t h:i A")
        ]);
        exit;
    }

    header("Location: admin_dashboard.php?success=Backup created successfully: $filename");
} else {
    if (isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Backup failed. Make sure mysqldump is configured correctly.'
        ]);
        exit;
    }

    header("Location: admin_dashboard.php?error=Backup failed.");
}
exit;
