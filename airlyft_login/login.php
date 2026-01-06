<?php
session_start();

if (isset($_SESSION['adminlogin'])) {
    header("Location: admin_dashboard.php");
    exit;
}

if (isset($_SESSION['userlogin'])) {
    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>AirLyft Login</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" xintegrity="sha512-Fo3rlrZj/kTcXnD+2zYdDsd2jw0m/2qRY3A2r2lxKp6GgStLSF3Qo7hwX2ZxU1oG8iZL5Nn5wTkSU5GZDQJhxQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet" />
    
    <!-- Google Fonts - Poppins for general text, Playfair Display for headings -->
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
	<link rel="stylesheet" href="css/login.css">
    
    <!-- Custom Font - Ensure 'OXYNEO.otf' is in a 'fonts' folder relative to this HTML -->
    
    
</head>

<body class="h-100">

    <header class="header-bar">
        <div class="header-left">
            <!-- Adjusted logo path assuming AirlyftGallery is one level up from airlyft_login -->
            <img src="../AirlyftGallery/LOGO.png" alt="Airlyft Logo" class="logo">
            <div class="header-title">Airlyft Travel</div>
        </div>
        <nav class="navbar">
            <ul>
                <!-- Adjusted paths to be relative assuming login.php is in airlyft_login/ and panel.php/welcome.php are in airlyft/ -->
                <li><a href="../airlyft/panel.php"><i class='bx bxs-home'></i> Home</a></li>
                <li><a href="../airlyft/panel.php#about"><i class='bx bxs-user-detail'></i> About Us</a></li>
                <li><a href="../airlyft/panel.php#contacts"><i class='bx bxs-phone'></i> Contacts</a></li>
                <li><a href="login.php"><i class='bx bxs-user-plus'></i> Login</a></li> <!-- Self-referencing link -->
            </ul>
        </nav>
    </header>

<div class="container h-100">
    <div class="d-flex justify-content-center align-items-center h-100">
        <div class="user_card">
            <div class="d-flex justify-content-center">
                <div class="brand_logo_container">
                    <!-- Adjusted logo path assuming img/logo.png is relative to login.php -->
                    <img src="img/logo.png" class="brand_logo" alt="AirLyft logo">
                </div>
            </div>
            <div class="d-flex justify-content-center form_container">
                <form>
                    <div class="input-group mb-3">
                        <span class="input-group-text"><i class="fas fa-user"></i></span>
                        <input type="text" name="username" id="username" class="form-control" placeholder="Username" required>
                    </div>

                    <div class="input-group mb-2">
                        <span class="input-group-text"><i class="fas fa-key"></i></span>
                        <input type="password" name="password" id="password" class="form-control" placeholder="Password" required>
                    </div>
                    
                    <div class="d-flex justify-content-center mt-3 login_container">
                        <button type="button" name="button" id="login" class="btn login_btn">Login</button>
                    </div>
                </form>
            </div>
            
            <div class="mt-4">
                <div class="d-flex justify-content-center links">
                    Don't have an account?   <a href="/airlyft/airlyft_db/signup.php" class="ml-2"> Sign Up</a>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    $(function(){
    $('#login').click(function(e){
        e.preventDefault();

        let form = this.form;

        
        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }

        var username = $('#username').val();
        var password = $('#password').val(); 

        $.ajax({
            type: 'POST',
            url: 'jslogin.php',
            data: {username: username, password: password},
            success: function(data){
                // Removed alert(data) for cleaner user experience, keeping for console debugging.
                console.log("Login response:", data); 
                if ($.trim(data) === "admin") {
                    window.location.href = "admin_dashboard.php";
                } else if ($.trim(data) === "user") {
                    window.location.href = "index.php";
                } else {
                    // Using a custom message box or div for errors instead of alert()
                    // For now, retaining alert() as it's part of the original script,
                    // but usually you'd replace this with a styled modal for UX.
                    alert(data); 
                }
            },
            error: function(xhr, status, error){
                console.log("AJAX Error:", status, error);
                alert('There were errors while processing your request.'); // Generic error message for user
            }
        });

    });
});

</script>
</body>
</html>
