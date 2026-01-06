<?php
session_start();
date_default_timezone_set('Asia/Manila'); // Force Philippine Time (UTC+8)

function formatToLocalTime($datetimeString) {
    if (empty($datetimeString)) return 'Not specified';
    
    try {
        // Parse as MySQL datetime (2025-06-20 10:00:00) assuming local time (Asia/Manila)
        $date = new DateTime($datetimeString, new DateTimeZone('Asia/Manila'));
        return $date->format('F j, Y, g:i A'); // e.g., "June 20, 2025, 8:00 AM"
    } catch (Exception $e) {
        error_log("Date formatting error: " . $e->getMessage() . " for input: " . $datetimeString);
        return $datetimeString; // Return original string if formatting fails
    }
}

// Retrieve data from session (replace your existing depDateTime/arrDateTime assignments)
$depDateTime = formatToLocalTime($_SESSION['dep_date_time'] ?? '');
$arrDateTime = formatToLocalTime($_SESSION['arr_date_time'] ?? '');
// Retrieve data from session or URL parameters
$bookingId = isset($_SESSION['booking_id']) ? htmlspecialchars($_SESSION['booking_id']) : (isset($_GET['booking_id']) ? htmlspecialchars($_GET['booking_id']) : 'BOOKING12345');
$passengers = isset($_SESSION['passengers']) ? $_SESSION['passengers'] : (isset($_GET['passengers']) ? json_decode($_GET['passengers'], true) : []);
$bookingDate = isset($_SESSION['booking_date']) ? htmlspecialchars($_SESSION['booking_date']) : (isset($_GET['booking_date']) ? htmlspecialchars($_GET['booking_date']) : date('F d, Y, h:i A'));
$liftModel = isset($_SESSION['lift_model']) ? htmlspecialchars($_SESSION['lift_model']) : (isset($_GET['lift_model']) ? htmlspecialchars($_GET['lift_model']) : 'Model [model]');
$liftCapacity = isset($_SESSION['lift_capacity']) ? htmlspecialchars($_SESSION['lift_capacity']) : (isset($_GET['lift_capacity']) ? htmlspecialchars($_GET['lift_capacity']) : 'Capacity [capacity]');
$departure = isset($_SESSION['departure']) ? htmlspecialchars($_SESSION['departure']) : (isset($_GET['departure']) ? htmlspecialchars($_GET['departure']) : '[Departure Coordinates]');
$arrival = isset($_SESSION['arrival']) ? htmlspecialchars($_SESSION['arrival']) : (isset($_GET['arrival']) ? htmlspecialchars($_GET['arrival']) : '[Arrival Coordinates]');
$paymentMode = isset($_SESSION['payment_mode']) ? htmlspecialchars($_SESSION['payment_mode']) : (isset($_GET['payment_mode']) ? htmlspecialchars($_GET['payment_mode']) : 'GCash');
$account = isset($_SESSION['account']) ? htmlspecialchars($_SESSION['account']) : (isset($_GET['account']) ? htmlspecialchars($_GET['account']) : '#5ghdrj5j');
$basePrice = isset($_SESSION['base_price']) ? htmlspecialchars($_SESSION['base_price']) : (isset($_GET['base_price']) ? htmlspecialchars($_GET['base_price']) : '0.00');
$surcharge = isset($_SESSION['surcharge']) ? htmlspecialchars($_SESSION['surcharge']) : (isset($_GET['surcharge']) ? htmlspecialchars($_GET['surcharge']) : '0.00');
$totalPaid = isset($_SESSION['total_paid']) ? htmlspecialchars($_SESSION['total_paid']) : (isset($_GET['total_paid']) ? htmlspecialchars($_GET['total_paid']) : number_format(floatval($basePrice) + floatval($surcharge), 2));

// Calculate booked capacity based on number of passengers
$bookedCapacity = count($passengers);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>E-Ticket Receipt</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="payment2.css" rel="stylesheet">
    <style>
        .receipt-container {
            max-width: 700px; /* Increased width */
            margin: 30px auto;
            padding: 24px;     /* Slightly more padding for balance */
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            font-family: Arial, sans-serif;
            color: #111;
        }
        .receipt-header {
            text-align: center;
            margin-bottom: 20px;
        }
        .receipt-section {
            margin-bottom: 15px;
        }
        .receipt-section span {
            display: inline-block;
            min-width: 120px;
            font-weight: bold;
        }
        .passenger-item {
            padding: 10px 0;
            border-bottom: 1px solid #eee;
        }
        .passenger-item:last-child {
            border-bottom: none;
        }
        .status-badge {
            background-color: #28a745;
            color: #fff;
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 14px;
        }
        .print-btn {
            display: block;
            margin: 20px auto;
            background-color: #007bff;
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
        }
        .print-btn:hover {
            background-color: #0056b3;
        }
        .plane-icon {
            font-size: 24px;
            color: #007bff;
            margin: 0 10px;
        }
    </style>
</head>
<body>
    <div class="receipt-container">
        <div class="receipt-header">
            <h2>E-Ticket Receipt</h2>
            <span class="status-badge">PAID</span>
        </div>
        <div class="receipt-section">
            <?php foreach ($passengers as $index => $passenger): ?>
                <div class="passenger-item">
                    <p><span>Passenger <?php echo $index + 1; ?> Name:</span> <?php echo htmlspecialchars($passenger['fname'] . ' ' . $passenger['lname']); ?></p>
                    <p><span>Age:</span> <?php echo htmlspecialchars($passenger['age'] ?? 'N/A'); ?></p>
                    <p><span>Insurance:</span> <?php echo htmlspecialchars($passenger['hasInsurance'] ?? 'No'); ?></p>
                </div>
            <?php endforeach; ?>
            <p><span>Booking #:</span> <?php echo $bookingId; ?></p>
            <p><span>Booking Date:</span> <?php echo $bookingDate; ?></p>
        </div>
        <div class="receipt-section">
            <p><span>Lift Model:</span> <?php echo $liftModel; ?></p>
            <p><span>Booked Capacity:</span> <?php echo $bookedCapacity; ?></p>
        </div>
        <div class="receipt-section">
            <p><span>Route:</span> <?php echo $departure; ?> <span class="plane-icon">✈</span><?php echo $arrival; ?></p>
            <p><span>Departure Time:</span> <?php echo $depDateTime; ?></p>
            <p><span>Arrival Time:</span> <?php echo $arrDateTime; ?></p>
        </div>
        <div class="receipt-section">
            <p><span>Payment Mode:</span> <?php echo $paymentMode; ?></p>
            <p><span>Reference No:</span> <?php echo $account; ?></p>
        </div>
        <div class="receipt-section">
            <p><span>Base Price:</span> ₱<?php echo $basePrice; ?></p>
            <p><span>Surcharge:</span> ₱<?php echo $surcharge; ?></p>
            <p><span>Total Paid:</span> ₱<?php echo $totalPaid; ?></p>
        </div>
        <p class="text-center">This receipt serves as proof of payment and confirms your reservation. Please present a copy when needed.</p>
        <button class="print-btn" onclick="window.print()">Print Receipt</button>
        <button class="print-btn" onclick="window.location.href='/airlyft/airlyft/panel.php'">DONE</button>
    </div>
</body>
</html>