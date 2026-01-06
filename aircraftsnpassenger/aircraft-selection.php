<!DOCTYPE html>
<html>
<head>
     <title>Aircraft Selection & Booking</title>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
	<link rel="stylesheet" href="css/aircraft-selection.css">

</head>
<body>
    <?php
        session_start(); // Start session for logout handling
        // Fallback logout logic (optional, remove if using logout.php exclusively)
        if (isset($_GET['logout']) && $_GET['logout'] == 'true') {
            session_unset();
            session_destroy();
            header("Location: ../airlyft_login/index.php");
            exit();
        }
    ?>
    <div class="panel">
        <div class="bg-blur"></div>

        <div class="scroll-wrapper">
            <header class="header-bar">
                <div class="header-left">
                    <img src="../AirlyftGallery/LOGO.png" alt="Airlyft Logo" class="logo">
                    <div class="header-title">Airlyft Travel</div>
                </div>
                <nav class="navbar">
                    <ul>
                        <li><a href="../airlyft/panel.php">Home</a></li>
                        <li><a href="../airlyft/panel.php#about">About Us</a></li>
                        <li><a href="../airlyft/panel.php#contacts">Contacts</a></li>
                        <li><a href="login.php">Login</a></li>
                    </ul>
                </nav>
            </header>
        </div>
    </div>

    <div class="main-content-wrapper">
        <div class="main-content">
            <h1>Select Your Luxury Aircraft & Confirm Booking</h1>

            <form id="bookingForm" action="../payment/payment2.php" method="POST">
                <div class="selection-container">
                    <label for="aircraft-select">Choose an Aircraft:</label>
                    <select id="aircraft-select" name="aircraftName" required>
                        <option value="">--Please choose an option--</option>
                        <option value="Cessna Turbo Stationair HD (T206H)">Cessna Turbo Stationair HD (T206H)</option>
                        <option value="Cessna Grand Caravan EX (Deluxe Config)">Cessna Grand Caravan EX (Deluxe Config)</option>
                        <option value="Airbus H160">Airbus H160</option>
                        <option value="Sikorsky S-76D">Sikorsky S-76D</option>
                    </select>
                    </div>

                <div id="selected-aircraft-details">
                    <h2>Aircraft Details:</h2>
                    <p><strong>Selected Date:</strong> <span id="display-selected-date">No date selected</span></p>
                    <input type="hidden" id="hidden-selected-date" name="selectedDate">
                    <input type="hidden" id="hidden-aircraft-rate" name="aircraftRate">

                    <p><strong>Name:</strong> <span id="aircraft-name"></span></p>
                    <p><strong>Capacity:</strong> <span id="aircraft-capacity"></span></p>
                    <p><strong>Description:</strong> <span id="aircraft-description"></span></p>
                    <p><strong>Rate per Hour:</strong> <span id="aircraft-rate-display"></span></p>

                    <div class="aircraft-image-container">
                        <img id="aircraft-image" src="" alt="Aircraft Image" style="max-width: 100%; height: auto; margin-top: 20px; display: none;">
                    </div>
                </div>

                <hr class="form-separator">

                <div class="passenger-details-section">
                    <h2>Passenger Information</h2>
                    <div class="form-group">
                        <label for="passenger-count">Number of Passengers:</label>
                        <input type="number" id="passenger-count" name="passengerCount" min="1" max="100" value="1" required>
                    </div>

                    <div id="passenger-fields-container">
                        </div>

                    <div class="form-group">
                        <button type="submit" class="confirm-button">Confirm Booking</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <script src="aircraft-selection.js" defer></script>
</body>
</html>