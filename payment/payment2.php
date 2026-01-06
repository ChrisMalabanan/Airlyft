<?php
// Enable full error reporting at the very top
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__.'/php_errors.log');

session_start();
date_default_timezone_set('Asia/Manila');

// Debug session
error_log("Session data: ".print_r($_SESSION, true));

if (!isset($_SESSION['userlogin'])) {
    error_log("User not logged in, redirecting to login.php");
    header("Location: login.php");
    exit;
}

if (isset($_GET['logout'])) {
    session_destroy();
    unset($_SESSION);
    header("Location: login.php");
    exit;
}

// Database connection with error handling
require_once '../aircraftsnpassenger/db_connect.php';
if ($conn->connect_error) {
    error_log("Database connection failed: ".$conn->connect_error);
    die(json_encode(["status" => "error", "message" => "Database connection failed"]));
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    header('Content-Type: application/json');
    
    // Debug input data
    error_log("POST data: ".print_r($_POST, true));
    error_log("FILES data: ".print_r($_FILES, true));

    $conn->begin_transaction();

    try {
        // Validate and sanitize inputs
        $aircraftRate = filter_var($_POST['aircraftRate'] ?? 0, FILTER_VALIDATE_FLOAT);
        if ($aircraftRate === false) {
            throw new Exception("Invalid aircraft rate");
        }

        $aircraftName = urldecode($_POST['lift_model'] ?? '');
        if (empty($aircraftName)) {
            throw new Exception("Aircraft name is required");
        }

        // Improved date handling
        $dep_date_time = $_POST['dep_date_time'] ?? '';
        if (empty($dep_date_time) || !DateTime::createFromFormat('Y-m-d H:i:s', $dep_date_time)) {
            // Default to tomorrow 10:00 AM if invalid
            $dep_date_time = date('Y-m-d 10:00:00', strtotime('+1 day'));
            $selectedDate = date('Y-m-d', strtotime('+1 day'));
            error_log("Invalid dep_date_time format, using default: $dep_date_time");
        } else {
            $dt = new DateTime($dep_date_time);
            $dep_date_time = $dt->format('Y-m-d H:i:s');
            $selectedDate = $dt->format('Y-m-d');
        }

        $passengerCount = filter_var($_POST['passenger_count'] ?? 0, FILTER_VALIDATE_INT);
        if ($passengerCount === false || $passengerCount < 1) {
            throw new Exception("Invalid passenger count");
        }

        $passengersJsonString = $_POST['passengers'] ?? '[]';
        if (is_array($passengersJsonString)) {
            $passengersJsonString = json_encode($passengersJsonString);
        }
        $passengers = json_decode($passengersJsonString, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception("Invalid passenger data format");
        }

        $paymentMode = $_POST['payment_mode'] ?? '';
        $accountNumber = $_POST['account'] ?? '';
        $surcharge = filter_var($_POST['surcharge'] ?? 0, FILTER_VALIDATE_FLOAT);
        if ($surcharge === false) {
            throw new Exception("Invalid surcharge value");
        }

        // Additional validation
        if (count($passengers) != $passengerCount) {
            throw new Exception("Passenger count mismatch");
        }
        if (empty($paymentMode) || empty($accountNumber)) {
            throw new Exception("Payment details are required");
        }
        if (!preg_match('/^\d{1,9}$/', $accountNumber)) {
            throw new Exception("Invalid reference number (1-9 digits required)");
        }

        // File upload handling
        $filePath = null;
        if (!isset($_FILES['proof']) || $_FILES['proof']['error'] !== UPLOAD_ERR_OK) {
            throw new Exception("Proof of payment is required");
        }

        $allowedTypes = ['image/jpeg', 'image/png'];
        $maxSize = 5 * 1024 * 1024; // 5MB
        if (!in_array($_FILES['proof']['type'], $allowedTypes) || $_FILES['proof']['size'] > $maxSize) {
            throw new Exception("Invalid file type or size (max 5MB JPEG/PNG allowed)");
        }

        $uploadDir = '../Uploads/';
        if (!is_dir($uploadDir) && !mkdir($uploadDir, 0755, true)) {
            throw new Exception("Failed to create upload directory");
        }

        $fileName = time().'_'.preg_replace('/[^a-zA-Z0-9\.\-]/', '_', basename($_FILES['proof']['name']));
        $filePath = $uploadDir.$fileName;
        if (!move_uploaded_file($_FILES['proof']['tmp_name'], $filePath)) {
            throw new Exception("Failed to save uploaded file");
        }

        // Get user ID
        $user_id = $_SESSION['userlogin']['id'] ?? 0;
        if ($user_id === 0) {
            throw new Exception("User authentication failed");
        }

        // Calculate total cost
        $flightDurationHours = 1;
        $totalCost = ($aircraftRate * $flightDurationHours) + $surcharge;

        // Get aircraft info
        $stmt_get_aircraft_info = $conn->prepare("SELECT Aircraft_Id, Capacity FROM lift WHERE Aircraft_Name = ?");
        if (!$stmt_get_aircraft_info) {
            throw new Exception("Prepare failed: ".$conn->error);
        }
        $stmt_get_aircraft_info->bind_param("s", $aircraftName);
        if (!$stmt_get_aircraft_info->execute()) {
            throw new Exception("Execute failed: ".$stmt_get_aircraft_info->error);
        }
        $result_aircraft_info = $stmt_get_aircraft_info->get_result();
        $aircraft_data = $result_aircraft_info->fetch_assoc();
        $stmt_get_aircraft_info->close();

        if (!$aircraft_data) {
            throw new Exception("Aircraft not found: $aircraftName");
        }

        $aircraft_id = $aircraft_data['Aircraft_Id'];
        preg_match('/Up to (\d+) passengers/', $aircraft_data['Capacity'], $matches);
        $max_aircraft_capacity = isset($matches[1]) ? (int)$matches[1] : 0;

        if ($passengerCount > $max_aircraft_capacity) {
            throw new Exception("Passenger count exceeds aircraft capacity");
        }

        // Schedule handling - Check if aircraft is already booked for this date
        $stmt_check_schedule = $conn->prepare("
            SELECT Sched_Id, Booked_Capacity 
            FROM schedule 
            WHERE Aircraft_Id = ? 
            AND DATE(Dep_Date_Time) = ?
            AND Status = 'Confirmed'
            FOR UPDATE
        ");
        if (!$stmt_check_schedule) {
            throw new Exception("Prepare failed: ".$conn->error);
        }
        $stmt_check_schedule->bind_param("is", $aircraft_id, $selectedDate);
        if (!$stmt_check_schedule->execute()) {
            throw new Exception("Execute failed: ".$stmt_check_schedule->error);
        }
        $result_check_schedule = $stmt_check_schedule->get_result();
        $existing_schedule = $result_check_schedule->fetch_assoc();
        $stmt_check_schedule->close();

        if ($existing_schedule) {
            // Aircraft already has a confirmed booking for this date
            throw new Exception("This aircraft is already booked for the selected date. Please choose another date or aircraft.");
        }

        $arr_date_time = $_POST['arr_date_time'] ?? '';
        if (empty($arr_date_time) || !DateTime::createFromFormat('Y-m-d H:i:s', $arr_date_time)) {
            $arr_date_time = date('Y-m-d H:i:s', strtotime($dep_date_time . ' +4 hours'));
            error_log("Invalid arr_date_time format, using dep_date_time + 4 hours: $arr_date_time");
        }

        $departure_coords = $_POST['departure'] ?? 'MNL';
        $arrival_coords = $_POST['arrival'] ?? 'CEB';
        $status = 'Confirmed';

        // Debug schedule data before insertion
        error_log("Schedule data before insertion:");
        error_log("Aircraft_Id: $aircraft_id");
        error_log("Departure_Coordinates: $departure_coords");
        error_log("Arrival_Coordinates: $arrival_coords");
        error_log("Arr_Date_Time: $arr_date_time");
        error_log("Status: $status");
        error_log("Booked_Capacity: $passengerCount");
        error_log("Dep_Date_Time: $dep_date_time");

        // Insert new schedule
        $stmt_insert_schedule = $conn->prepare("
            INSERT INTO schedule 
            (Aircraft_Id, Departure_Coordinates, Arrival_Coordinates, Arr_Date_Time, Status, Booked_Capacity, Dep_Date_Time) 
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        if (!$stmt_insert_schedule) {
            throw new Exception("Prepare failed: ".$conn->error);
        }
        $stmt_insert_schedule->bind_param(
            "issssis", 
            $aircraft_id, 
            $departure_coords, 
            $arrival_coords, 
            $arr_date_time, 
            $status, 
            $passengerCount, 
            $dep_date_time
        );
        
        if (!$stmt_insert_schedule->execute()) {
            // Check if this is a duplicate entry error
            if ($conn->errno == 1062) { // MySQL duplicate entry error code
                throw new Exception("This aircraft is already booked for the selected date.");
            }
            throw new Exception("Execute failed: ".$stmt_insert_schedule->error);
        }
        
        $sched_id = $conn->insert_id;
        error_log("New schedule created with ID: $sched_id");
        $stmt_insert_schedule->close();

        // Insert booking
        $stmt_booking = $conn->prepare("INSERT INTO booking (User_Id, Aircraft_Id, Selected_Date_of_Flight, Sched_Id, Total_Cost, Booking_Date) VALUES (?, ?, ?, ?, ?, NOW())");
        if (!$stmt_booking) {
            throw new Exception("Prepare failed: ".$conn->error);
        }
        $stmt_booking->bind_param("iisds", $user_id, $aircraft_id, $selectedDate, $sched_id, $totalCost);
        if (!$stmt_booking->execute()) {
            throw new Exception("Execute failed: ".$stmt_booking->error);
        }
        $bookingId = $conn->insert_id;
        $stmt_booking->close();

        // Prepare statements for addresses and passengers
        $stmt_address = $conn->prepare("INSERT INTO addresses (street, barangay, city, province) VALUES (?, ?, ?, ?)");
        if (!$stmt_address) {
            throw new Exception("Prepare failed: ".$conn->error);
        }
        
        $stmt_passenger = $conn->prepare("INSERT INTO passengers (Booking_Id, FName, LName, Age, Address_Id, Has_Insurance, Insurance_Details) VALUES (?, ?, ?, ?, ?, ?, ?)");
        if (!$stmt_passenger) {
            throw new Exception("Prepare failed: ".$conn->error);
        }

        // Process passengers
        foreach ($passengers as $passenger) {
            $fname = $passenger['fname'] ?? '';
            $lname = $passenger['lname'] ?? '';
            $age = filter_var($passenger['age'] ?? 0, FILTER_VALIDATE_INT);
            $street = $passenger['street'] ?? '';
            $barangay = $passenger['barangay'] ?? '';
            $city = $passenger['municipality'] ?? '';
            $province = $passenger['province'] ?? '';
            $has_insurance = strtolower($passenger['hasInsurance'] ?? 'no') === 'yes' ? 1 : 0;
            $insurance_details = $passenger['insuranceDetails'] ?? '';

            if (empty($fname) || empty($lname) || $age === false || $age <= 0 || empty($street) || empty($barangay) || empty($city) || empty($province)) {
                throw new Exception("Invalid passenger data");
            }

            // Insert address
            $stmt_address->bind_param("ssss", $street, $barangay, $city, $province);
            if (!$stmt_address->execute()) {
                throw new Exception("Address insert failed: ".$stmt_address->error);
            }
            $address_id = $conn->insert_id;

            // Insert passenger
            $stmt_passenger->bind_param("isssiss", $bookingId, $fname, $lname, $age, $address_id, $has_insurance, $insurance_details);
            if (!$stmt_passenger->execute()) {
                throw new Exception("Passenger insert failed: ".$stmt_passenger->error);
            }
        }

        $stmt_address->close();
        $stmt_passenger->close();

        // Insert payment
        $stmt_payment = $conn->prepare("INSERT INTO payments (Booking_Id, amount_paid, payment_date, payment_mode, ref_number, proof_file) VALUES (?, ?, NOW(), ?, ?, ?)");
        if (!$stmt_payment) {
            throw new Exception("Prepare failed: ".$conn->error);
        }
        $stmt_payment->bind_param("idsss", $bookingId, $totalCost, $paymentMode, $accountNumber, $filePath);
        if (!$stmt_payment->execute()) {
            throw new Exception("Execute failed: ".$stmt_payment->error);
        }
        $payment_id = $conn->insert_id;
        $stmt_payment->close();

        // Store all data in session for receipt
        $_SESSION['booking_id'] = $bookingId;
        $_SESSION['passengers'] = $passengers;
        $_SESSION['booking_date'] = date('F d, Y, h:i A');
        $_SESSION['lift_model'] = $aircraftName;
        $_SESSION['lift_capacity'] = $aircraft_data['Capacity'];
        $_SESSION['departure'] = $departure_coords;
        $_SESSION['arrival'] = $arrival_coords;
        $_SESSION['dep_date_time'] = $dep_date_time;
        $_SESSION['arr_date_time'] = $arr_date_time;
        $_SESSION['payment_mode'] = $paymentMode;
        $_SESSION['account'] = $accountNumber;
        $_SESSION['base_price'] = $aircraftRate;
        $_SESSION['surcharge'] = $surcharge;
        $_SESSION['total_paid'] = $totalCost;

        $conn->commit();
        
        // Successful response
        echo json_encode([
            "status" => "success",
            "message" => "Booking confirmed!",
            "bookingId" => $bookingId,
            "paymentId" => $payment_id
        ]);

    } catch (Exception $e) {
        $conn->rollback();
        error_log("Error: ".$e->getMessage()."\nTrace: ".$e->getTraceAsString());
        
        // Clean up uploaded file if error occurred
        if (isset($filePath) && file_exists($filePath)) {
            unlink($filePath);
        }
        
        http_response_code(400);
        echo json_encode([
            "status" => "error",
            "message" => $e->getMessage()
        ]);
    } finally {
        $conn->close();
    }
    exit;
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Booking Summary and Payment</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="payment2.css" rel="stylesheet">
</head>
<body>
    <header class="header-bar">
  <div class="header-left">
    <img src="/airlyft/AirlyftGallery/LOGO.png" alt="Airlyft Logo" class="logo">
    <div class="header-title">Airlyft Travel</div>
  </div>
  <nav class="navbar">
    <ul>
      <li><a href="../airlyft/panel.php">Home</a></li>
      <li><a href="../airlyft/panel.php#about">About Us</a></li>
      <li><a href="../airlyft/panel.php#contacts">Contacts</a></li>
      <li><a href="../airlyft_login/login.php?logout=true">Logout</a></li>
    </ul>
  </nav>
</header>

    <div class="container">
        <section class="section">
            <h2>Booking Summary</h2>
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Booking ID:</strong> <span id="booking-id">Pending</span></p>
                    <p><strong>Destination:</strong> <span id="place-name">Not specified</span></p>
                    <p><strong>Route:</strong> <span id="dep-coords">MNL</span> → <span id="arr-coords">Not specified</span></p>
                    <p><strong>Departure:</strong> <span id="dep-time">Not specified</span></p>
                    <p><strong>Arrival:</strong> <span id="arr-time">Not specified</span></p>
                </div>
                <div class="col-md-6">
                    <p><strong>Aircraft:</strong> <span id="lift-model">Not specified</span></p>
                    <p><strong>Capacity:</strong> <span id="lift-capacity">Not specified</span></p>
                    <p><strong>Base Price:</strong> ₱<span id="lift-price">0.00</span></p>
                    <p><strong>Surcharge:</strong> ₱<span id="lift-surcharge">0.00</span></p>
                    <p><strong>Total Cost:</strong> ₱<span id="total-amount">0.00</span></p>
                </div>
            </div>
            <div class="passenger-list">
                <h4>Passengers</h4>
                <div id="passenger-list"></div>
            </div>
            <div class="button-group">
                <a href="aircraft-selection.php" class="btn btn-secondary">Back to Aircraft Selection</a>
                <button id="proceed-payment" class="btn btn-primary">Proceed to Payment</button>
            </div>
        </section>

        <!-- Payment Form Section -->
        <section class="payment-section" id="payment-section">
            <h2>Complete Your Payment</h2>
            <form id="paymentForm" enctype="multipart/form-data">
                <div class="form-group">
                    <label>Booking ID</label>
                    <p class="static-value" id="bookingIdDisplay">Pending</p>
                </div>
                <div class="form-group">
                    <label>Total Amount to Pay</label>
                    <p class="static-value" id="totalAmountDisplay">₱0.00</p>
                </div>
                <div class="form-group">
                    <label>Payment Method</label>
                    <select name="paymentMethod" id="paymentMethod" required>
                        <option value="">-- Select Method --</option>
                        <option value="Gcash">Gcash</option>
                        <option value="Bank Transfer">Bank Transfer</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Proof of Payment (Screenshot)</label>
                    <input type="file" name="proof" accept="image/*" required />
                </div>
                <div class="form-group">
                    <label>Reference Number</label>
                    <input type="text" name="referenceNumber" required pattern="[0-9]+" title="Reference number must be numeric." />
                </div>
                <button type="submit" class="btn btn-primary">Submit Payment</button>
                <input type="hidden" name="aircraftRate" id="aircraftRate">
                <input type="hidden" name="lift_model" id="lift_model">
                <input type="hidden" name="passenger_count" id="passenger_count">
                <input type="hidden" name="passengers" id="passengers">
                <input type="hidden" name="dep_date_time" id="dep_date_time">
                <input type="hidden" name="arr_date_time" id="arr_date_time">
                <input type="hidden" name="departure" id="departure">
                <input type="hidden" name="arrival" id="arrival">
                <input type="hidden" name="surcharge" id="surcharge">
            </form>
        </section>
    </div>

     <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', () => {
        // Embedded durations data
        const durations = {
            "Amanpulo": 70,
            "Balesin Island": 25,
            "Amorita Resort": 105,
            "Huma Island Resort": 110,
            "El Nido Resort": 115,
            "Banwa": 120,
            "Nay Palad": 150,
            "Alphaland Baguio": 60,
            "Shangri-La Boracay": 90,
            "The Farm San Benito": 20,
            "Aureo La Union": 60
        };

        // Retrieve URL parameters
        function getUrlParams() {
            const params = {};
            try {
                const search = window.location.search.substring(1);
                console.log('Raw URL search:', search);
                if (search) {
                    search.split('&').forEach(param => {
                        const [key, value] = param.split('=');
                        params[decodeURIComponent(key)] = decodeURIComponent(value || '');
                    });
                }
                console.log('Parsed URL params:', params);
            } catch (e) {
                console.error('Error parsing URL params:', e);
            }
            return params;
        }

        // Improved date parsing function
        function parseDate(dateStr) {
            if (!dateStr) {
                console.warn('No depDateTime provided, using tomorrow 10:00:00');
                const tomorrow = new Date();
                tomorrow.setDate(tomorrow.getDate() + 1);
                tomorrow.setHours(10, 0, 0, 0);
                return tomorrow;
            }

            try {
                let normalizedStr = decodeURIComponent(dateStr).replace(/\+/g, ' ').trim();
                console.log('Normalized depDateTime:', normalizedStr);

                const formats = [
                    { regex: /^(\d{4})-(\d{2})-(\d{2})T(\d{2}):(\d{2}):(\d{2})$/, handler: ([_, year, month, day, hours, minutes, seconds]) => new Date(year, month - 1, day, hours, minutes, seconds) },
                    { regex: /^(\d{4})-(\d{2})-(\d{2})\s(\d{2}):(\d{2}):(\d{2})$/, handler: ([_, year, month, day, hours, minutes, seconds]) => new Date(year, month - 1, day, hours, minutes, seconds) },
                    { regex: /^(\d{4})-(\d{2})-(\d{2})T(\d{2}):(\d{2})$/, handler: ([_, year, month, day, hours, minutes]) => new Date(year, month - 1, day, hours, minutes) },
                    { regex: /^(\d{4})-(\d{2})-(\d{2})$/, handler: ([_, year, month, day]) => new Date(year, month - 1, day, 10, 0, 0) },
                    { regex: /^(\d{2})\/(\d{2})\/(\d{4})\s(\d{1,2}):(\d{2})\s(AM|PM)$/, handler: ([_, month, day, year, hours, minutes, period]) => {
                        let h = parseInt(hours);
                        if (period === 'PM' && h !== 12) h += 12;
                        if (period === 'AM' && h === 12) h = 0;
                        return new Date(year, month - 1, day, h, minutes);
                    }}
                ];

                for (const { regex, handler } of formats) {
                    const match = normalizedStr.match(regex);
                    if (match) {
                        const parsedDate = handler(match);
                        if (!isNaN(parsedDate.getTime())) {
                            console.log('Parsed date:', parsedDate);
                            return parsedDate;
                        }
                    }
                }

                console.error('Failed to parse depDateTime:', normalizedStr);
                const tomorrow = new Date();
                tomorrow.setDate(tomorrow.getDate() + 1);
                tomorrow.setHours(10, 0, 0, 0);
                return tomorrow;
            } catch (e) {
                console.error('Error in parseDate:', e, 'Input:', dateStr);
                const tomorrow = new Date();
                tomorrow.setDate(tomorrow.getDate() + 1);
                tomorrow.setHours(10, 0, 0, 0);
                return tomorrow;
            }
        }

        // Format date to MySQL-compatible format (YYYY-MM-DD HH:MM:SS)
        function formatMySQLDateTime(date) {
            const pad = (num) => String(num).padStart(2, '0');
            return `${date.getFullYear()}-${pad(date.getMonth() + 1)}-${pad(date.getDate())} ${pad(date.getHours())}:${pad(date.getMinutes())}:${pad(date.getSeconds())}`;
        }

        const urlParams = getUrlParams();
        const selectedDestination = sessionStorage.getItem('selectedDestination') || urlParams.placeName || 'Not specified';
        console.log('Selected destination:', selectedDestination);

        let passengersData = [];
        try {
            passengersData = JSON.parse(decodeURIComponent(urlParams.passengers || '[]'));
            console.log('Parsed passengersData:', passengersData);
        } catch (e) {
            console.error('Error parsing passengers:', e);
            passengersData = [];
        }

        // Populate summary fields
        try {
            document.getElementById('booking-id').textContent = 'Pending';
            document.getElementById('bookingIdDisplay').textContent = 'Pending';
            document.getElementById('place-name').textContent = selectedDestination;
            document.getElementById('dep-coords').textContent = urlParams.departureCoords || 'MNL';
            document.getElementById('arr-coords').textContent = selectedDestination || 'Not specified';
            document.getElementById('lift-model').textContent = urlParams.liftModel || 'Not specified';
            document.getElementById('lift-capacity').textContent = urlParams.liftCapacity || 'Not specified';
            document.getElementById('lift-price').textContent = (urlParams.basePrice ? parseFloat(urlParams.basePrice).toFixed(2) : '0.00');
        } catch (e) {
            console.error('Error populating summary fields:', e);
        }

        // Calculate and display departure and arrival times
        const depTimeElement = document.getElementById('dep-time');
        const arrTimeElement = document.getElementById('arr-time');
        const depTimeStr = urlParams.depDateTime;
        const durationMinutes = durations[selectedDestination] || 60;
        console.log('Duration minutes for', selectedDestination, ':', durationMinutes);

        try {
            let baseTime = parseDate(depTimeStr);
            if (isNaN(baseTime.getTime())) {
                console.error('Invalid baseTime, using default');
                baseTime = new Date();
                baseTime.setDate(baseTime.getDate() + 1);
                baseTime.setHours(10, 0, 0, 0);
            }
            depTimeElement.textContent = baseTime.toLocaleString('en-PH', {
                year: 'numeric',
                month: 'long',
                day: 'numeric',
                hour: 'numeric',
                minute: 'numeric',
                hour12: true,
                timeZone: 'Asia/Manila',
            });
            const arrTime = new Date(baseTime.getTime() + durationMinutes * 60000);
            arrTimeElement.textContent = arrTime.toLocaleString('en-PH', {
                year: 'numeric',
                month: 'long',
                day: 'numeric',
                hour: 'numeric',
                minute: 'numeric',
                hour12: true,
                timeZone: 'Asia/Manila',
            });
            document.getElementById('dep_date_time').value = formatMySQLDateTime(baseTime);
            document.getElementById('arr_date_time').value = formatMySQLDateTime(arrTime);
        } catch (e) {
            console.error('Error setting times:', e);
            const defaultTime = new Date();
            defaultTime.setDate(defaultTime.getDate() + 1);
            defaultTime.setHours(10, 0, 0, 0);
            depTimeElement.textContent = 'Not specified';
            arrTimeElement.textContent = 'Not specified';
            document.getElementById('dep_date_time').value = formatMySQLDateTime(defaultTime);
            document.getElementById('arr_date_time').value = formatMySQLDateTime(new Date(defaultTime.getTime() + 60 * 60000));
        }

        // Calculate surcharge: ₱500 per passenger with no insurance
        let surcharge = 0.00;
        try {
            if (passengersData.length > 0) {
                const uninsuredCount = passengersData.filter(passenger => (passenger.hasInsurance || 'no').toLowerCase() === 'no').length;
                surcharge = uninsuredCount * 500.00;
                console.log('Uninsured passengers:', uninsuredCount, 'Surcharge:', surcharge);
            }
            document.getElementById('lift-surcharge').textContent = surcharge.toFixed(2);
        } catch (e) {
            console.error('Error calculating surcharge:', e);
            document.getElementById('lift-surcharge').textContent = '0.00';
        }

        // Calculate total amount
        let totalAmount = 0.00;
        try {
            const basePrice = parseFloat(urlParams.basePrice || '0.00');
            totalAmount = basePrice + surcharge;
            document.getElementById('total-amount').textContent = totalAmount.toFixed(2);
            document.getElementById('totalAmountDisplay').textContent = `₱${totalAmount.toFixed(2)}`;
        } catch (e) {
            console.error('Error calculating total amount:', e);
            document.getElementById('total-amount').textContent = '0.00';
            document.getElementById('totalAmountDisplay').textContent = 'Not available';
        }

        // Populate passenger list
        const passengerList = document.getElementById('passenger-list');
        try {
            if (passengersData.length > 0) {
                passengersData.forEach(passenger => {
                    const passengerDiv = document.createElement('div');
                    passengerDiv.className = 'passenger-item';
                    passengerDiv.innerHTML = `
                        <p class="mb-1"><strong>Name:</strong> ${passenger.fname || ''} ${passenger.lname || ''}</p>
                        <p class="mb-1"><strong>Age:</strong> ${passenger.age || 'N/A'}</p>
                        <p class="mb-1"><strong>Address:</strong> ${passenger.street || ''}, Barangay ${passenger.barangay || 'N/A'}, ${passenger.municipality || 'N/A'}, ${passenger.province || 'N/A'}</p>
                        <p class="mb-1"><strong>Insurance:</strong> ${(passenger.hasInsurance || 'no').toLowerCase() === 'yes' ? 'Yes' : 'No'}</p>
                        ${(passenger.hasInsurance || 'no').toLowerCase() === 'yes' ? `<p class="mb-1"><strong>Insurance Details:</strong> ${passenger.insuranceDetails || 'N/A'}</p>` : ''}
                    `;
                    passengerList.appendChild(passengerDiv);
                });
            } else {
                passengerList.innerHTML = '<p>No passenger information available.</p>';
            }
        } catch (e) {
            console.error('Error populating passenger list:', e);
            passengerList.innerHTML = '<p>Error loading passenger information.</p>';
        }

        // Populate hidden form fields
        try {
            document.getElementById('aircraftRate').value = urlParams.aircraftRate || '0.00';
            document.getElementById('lift_model').value = urlParams.liftModel || '';
            document.getElementById('passenger_count').value = urlParams.passengerCount || '0';
            document.getElementById('passengers').value = JSON.stringify(passengersData);
            document.getElementById('departure').value = urlParams.departureCoords || 'MNL';
            document.getElementById('arrival').value = selectedDestination;
            document.getElementById('surcharge').value = surcharge.toFixed(2);
        } catch (e) {
            console.error('Error populating hidden fields:', e);
        }

        // Proceed to Payment button handler
        try {
            const proceedButton = document.getElementById('proceed-payment');
            const paymentSection = document.getElementById('payment-section');
            if (!proceedButton || !paymentSection) {
                console.error('DOM elements missing:', { proceedButton, paymentSection });
                throw new Error('Proceed button or payment section not found');
            }
            proceedButton.addEventListener('click', () => {
                paymentSection.classList.add('visible');
                console.log('Payment section display:', getComputedStyle(paymentSection).display);
            });
        } catch (e) {
            console.error('Error setting up Proceed to Payment button:', e);
        }

        // Payment form submission handler
        document.getElementById('paymentForm').addEventListener('submit', async function (e) {
            e.preventDefault();
            console.log('Payment form submitted');
            
            // Validate dep_date_time before submission
            const depDateTime = document.getElementById('dep_date_time').value;
            if (!depDateTime || !/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/.test(depDateTime)) {
                alert('Invalid departure date/time. Please select a valid date.');
                return;
            }

            const formData = new FormData(e.target);
            formData.append('payment_mode', document.getElementById('paymentMethod').value);
            formData.append('account', document.querySelector('input[name="referenceNumber"]').value);

            // Log FormData entries for debugging
            for (let [key, value] of formData.entries()) {
                console.log(`FormData: ${key} = ${value}`);
            }

            try {
                const response = await fetch('payment2.php', {
                    method: 'POST',
                    body: formData
                });
                const result = await response.json();
                console.log('Server response:', result);
                if (result.status === 'success') {
                    document.getElementById('booking-id').textContent = result.bookingId;
                    document.getElementById('bookingIdDisplay').textContent = result.bookingId;
                    alert(result.message);
                    window.location.href = 'Ticket Receipt.php';
                } else {
                    alert(result.message);
                }
            } catch (error) {
                console.error('Error:', error);
                alert('An error occurred during booking confirmation.');
            }
        });
    });
    </script>
</body>
</html>