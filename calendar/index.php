<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Airlyft Travel - Calendar</title>
    <link href="style.css?v=5" rel="stylesheet">
    <!-- Boxicons CDN; if 404, download boxicons.min.css to /airlyft/calendar/css/ and use <link href="css/boxicons.min.css" rel="stylesheet"> -->
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet" integrity="sha384-n24+1jR2tSuK7+3qcX3N1jG3TkJmc8YpXAv8zYp0fO+Ujo0B32jooDe3JmzQ6lX" crossorigin="anonymous">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
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
    <header class="header-bar">
        <div class="header-left">
            <img src="logo.png" alt="Airlyft Travel Logo" class="logo">
            <span class="header-title">Airlyft Travel</span>
        </div>
        <nav class="navbar">
            <ul>
                <li><a href="../airlyft/panel.php">Home</a></li>
                <li><a href="../airlyft_login/index.php">Back</a></li>
                <li><a href="../airlyft/panel.php#contacts">Contacts</a></li>
                <li><a href="?logout=true">Logout</a></li>
            </ul>
        </nav>
    </header>
    <main class="main-content-wrapper">
        <div class="top-bar">
            <?php
                $currentYear = date('Y');
                $currentMonth = date('n') - 1; // JavaScript months are 0-based
                $monthName = date('F');
            ?>
            <h1 class="schedule-header" id="displayed-month-title">
                <?php echo $monthName; ?> 
                <span id="year-left" class="year-arrow"><</span>
                <span id="displayed-year"><?php echo $currentYear; ?></span>
                <span id="year-right" class="year-arrow">></span>
                Schedule
            </h1>
            <div class="month-navigation">
                <?php
                    $months = [
                        'January', 'February', 'March', 'April', 'May', 'June',
                        'July', 'August', 'September', 'October', 'November', 'December'
                    ];
                    foreach ($months as $index => $month) {
                        $class = ($index == $currentMonth && $currentYear == date('Y')) ? 'nav-button current-month' : 'nav-button';
                        echo "<button class='$class' data-year='$currentYear' data-month-index='$index'>$month</button>";
                    }
                ?>
            </div>
        </div>
        <div id="single-calendar-display-container"></div>
    </main>
    <script src="script.js?v=5"></script>
</body>
</html>