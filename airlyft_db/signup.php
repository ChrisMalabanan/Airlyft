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
    <title>AirLyft Sign Up</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" xintegrity="sha512-Fo3rlrZj/kTcXnD+2zYdDsd2jw0m/2qRY3A2r2lxKp6GgStLSF3Qo7hwX2ZxU1oG8iZL5Nn5wTkSU5GZDQJhxQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet" />
    
    <!-- Google Fonts - Poppins for general text, Playfair Display for headings -->
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/signup.css">
    
    
</head>
<body>

    <header class="header-bar">
        <div class="header-left">
            <!-- Adjusted logo path to be relative assuming signup.php is in airlyft_db/ and AirlyftGallery is in airlyft/ -->
            <img src="../airlyft\AirlyftGallery\LOGO.png" alt="Airlyft Logo" class="logo">
            <div class="header-title">Airlyft Travel</div>
        </div>
        <nav class="navbar">
            <ul>
                <!-- Adjusted paths to be relative assuming signup.php is in airlyft_db/ and others are in airlyft/ or airlyft_login/ -->
                <li><a href="../airlyft/panel.php"><i class='bx bxs-home'></i> Home</a></li>
                <li><a href="../airlyft_login/login.php"><i class='bx bx-arrow-back'></i> Back</a></li>
                <li><a href="../airlyft/panel.php#contacts"><i class='bx bxs-phone'></i> Contacts</a></li>
                <li><a href="../airlyft_login/login.php"><i class='bx bxs-user-plus'></i> Login</a></li>
            </ul>
        </nav>
    </header>

<div class="main-content-wrapper">
    <div class="signup_card">
        <h1>Sign Up</h1>
        <p>Fill up the form with correct values to create your account.</p>
        <hr class="mb-3">
        <form action="process.php" method="post">
            <label for="username"><b>Username</b></label>
            <input class="form-control" id="username" type="text" name="username" placeholder="Enter Username" required>
            
            <label for="email"><b>Email</b></label>
            <input class="form-control" id="email" type="email" name="email" placeholder="Enter Email" required>
            
            <label for="phonenumber"><b>Phone Number</b></label>
            <input class="form-control" id="phonenumber" type="text" name="phonenumber" placeholder="Enter Phone Number" required>
            
            <label for="password"><b>Password</b></label>
            <input class="form-control" id="password" type="password" name="password" placeholder="Enter Password" required>
            <hr class="mb-3">
            <input class="btn btn-primary" type="submit" id="register" name="create" value="Sign Up">
        </form>
    </div>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script type="text/javascript">
$(function() {
    $('#register').click(function(e) {
        var valid = this.form.checkValidity();
        if (valid) {
            e.preventDefault(); // Prevent default form submission initially

            var username = $('#username').val();
            var email = $('#email').val();
            var phonenumber = $('#phonenumber').val();
            var password = $('#password').val();

            $.ajax({
                type: 'POST',
                url: 'process.php',
                data: {
                    username: username,
                    email: email,
                    phonenumber: phonenumber,
                    password: password
                },
                success: function(data) {
                    if (data.includes("Successfully saved")) {
                        Swal.fire({
                            title: 'Success',
                            text: data,
                            icon: 'success',
                            confirmButtonColor: '#f0c300' // Gold confirm button
                        }).then(() => {
                            window.location.href = '../airlyft_login/login.php'; // Redirect to login page
                        });
                    } else {
                        Swal.fire({
                            title: 'Registration Failed',
                            text: data,
                            icon: 'error',
                            confirmButtonColor: '#f0c300' // Gold confirm button
                        });
                    }
                },
                error: function(xhr, status, error) {
                    Swal.fire({
                        title: 'Error',
                        text: 'There were errors while processing your request.',
                        icon: 'error',
                        confirmButtonColor: '#f0c300' // Gold confirm button
                    });
                }
            });
        } else {
            // If form is not valid, trigger native HTML5 validation messages
            this.form.reportValidity();
        }
    });
});
</script>

</body>
</html>
