<?php 
session_start();

if (!isset($_SESSION['userlogin'])) {
    header("Location: ../airlyft_login/login.php"); // Corrected path to login.php
    exit;
}

if (isset($_GET['logout'])) {
    session_destroy();
    unset($_SESSION);
    header("Location: ../airlyft_login/login.php"); // Corrected path to login.php
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Airlyft | Select Destination</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" xintegrity="sha512-Fo3rlrZj/kTcXnD+2zYdDsd2jw0m/2qRY3A2r2lxKp6GgStLSF3Qo7hwX2ZxU1oG8iZL5Nn5wTkSU5GZDQJhxQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet" />
    
    <!-- Google Fonts - Poppins for general text, Playfair Display for headings -->
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/index.css"> 
</head>
<body>

<header class="header-bar">
    <div class="header-left">
        <!-- Path adjusted for this index.php residing in /airlyft/ -->
        <img src="../AirlyftGallery/LOGO.png" alt="Airlyft Logo" class="logo">
        <div class="header-title">Airlyft Travel</div>
    </div>
    <nav class="navbar">
        <ul>
            <!-- Paths adjusted assuming this index.php is in /airlyft/ -->
            <li><a href="../airlyft/panel.php"><i class='bx bxs-home'></i> Home</a></li>
            <li><a href="../airlyft/panel.php#about"><i class='bx bxs-user-detail'></i> About Us</a></li>
            <li><a href="../airlyft/panel.php#contacts"><i class='bx bxs-phone'></i> Contacts</a></li>
            <li><a href="?logout=true"><i class='bx bxs-log-out'></i> Logout</a></li> <!-- Logout icon for logout link -->
        </ul>
    </nav>
</header>

<div class="main-content-wrapper">
    <h2 class="section-title">Select Your Destination</h2>

    <div class="destination-grid">
        <?php
        $destinations = [
            "Amanpulo", "Balesin Island", "Amorita Resort", "Huma Island Resort", "El Nido Resorts", "Banwa",
            "Nay Palad", "Alphaland Baguio", "Shangri-La Boracay", "Farm San Benito Lipa", "Aureo La Union"
        ];

        $images = [
            "amanpulo.png", "balesin.png", "amorita.png", "huma.png", "elnido.png", "banwa.png",
            "naypalad.png", "alphaland.png", "shangrila.png", "thefarmbenito.png", "aureolu.png"
        ];

        foreach ($destinations as $index => $dest) {
            // Corrected image path to explicitly go up one level then into AirlyftGallery
            $imagePath = '../AirlyftGallery/' . $images[$index]; 
            echo '<div class="destination-box" data-destination="' . htmlspecialchars($dest) . '" style="background-image: url(\'' . htmlspecialchars($imagePath) . '\');">';
            echo '<div class="destination-label">' . htmlspecialchars($dest) . '</div>';
            echo '</div>';
        }
        ?>
    </div>

    <!-- The Logout link should be styled as a button for better UX -->
    <a href="?logout=true" class="logout-link">Logout Now</a>

</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script>
$(document).ready(function(){
    $('.destination-box').click(function(){
        $('.destination-box').removeClass('selected');
        $(this).addClass('selected');
        const destination = $(this).data('destination');
        // Store destination in sessionStorage and redirect to calendar/index.php
        sessionStorage.setItem('selectedDestination', destination); // Store for use in calendar.php
        window.location.href = '../calendar/index.php'; // Path to calendar folder, assuming it's a sibling of airlyft
    });
});
</script>

</body>
</html>
