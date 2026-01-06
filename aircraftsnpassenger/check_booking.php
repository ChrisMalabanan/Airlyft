<?php
ob_start();
header('Content-Type: application/json');

error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', 'C:/xampp/htdocs/airlyft/aircraftsnpassenger/check_booking_errors.log');

try {
    if (!file_exists('database.php')) {
        throw new Exception('Database configuration file not found in current directory');
    }
    require_once 'database.php';

    $input = json_decode(file_get_contents('php://input'), true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception('Invalid JSON input: ' . json_last_error_msg());
    }

    $date = $input['date'] ?? null;
    $aircraftId = $input['aircraft'] ?? null;

    if (empty($date) || empty($aircraftId)) {
        throw new Exception('Missing date or aircraftId');
    }
    if (!DateTime::createFromFormat('Y-m-d', $date)) {
        throw new Exception('Invalid date format, expected YYYY-MM-DD');
    }
    if (!is_numeric($aircraftId)) {
        throw new Exception('Invalid aircraftId, must be numeric');
    }

    $db = new Database();
    $conn = $db->getConnection();
    if ($conn === false) {
        throw new Exception('Failed to establish database connection');
    }

    $stmt = $conn->prepare("
        SELECT COUNT(*) AS booked 
        FROM schedule 
        WHERE Aircraft_Id = :aircraftId 
        AND DATE(Dep_Date_Time) = :date
    ");
    if ($stmt === false) {
        throw new Exception('Prepare failed: ' . $conn->errorInfo()[2]);
    }
    $stmt->bindParam(':aircraftId', $aircraftId, PDO::PARAM_INT);
    $stmt->bindParam(':date', $date, PDO::PARAM_STR);
    if (!$stmt->execute()) {
        throw new Exception('Execute failed: ' . $conn->errorInfo()[2]);
    }

    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $booked = $result['booked'] ?? 0;

    ob_end_clean();
    echo json_encode([
        'available' => $booked == 0,
        'message' => $booked == 0 ? 'Available' : 'Already booked'
    ]);

} catch (PDOException $e) {
    ob_end_clean();
    http_response_code(500);
    $errorMsg = 'Database error: ' . $e->getMessage();
    echo json_encode(['error' => $errorMsg, 'available' => false]);
    error_log('PDO Error: ' . $errorMsg);
} catch (Exception $e) {
    ob_end_clean();
    http_response_code(400);
    $errorMsg = $e->getMessage();
    echo json_encode(['error' => $errorMsg, 'available' => false]);
    error_log('General Error: ' . $errorMsg);
}
?>