<?php
session_start();
$data = json_decode(file_get_contents('php://input'), true);
if ($data) {
    foreach ($data as $key => $value) {
        $_SESSION[$key] = $value;
    }
    echo json_encode(['status' => 'success']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'No data received']);
}
?>