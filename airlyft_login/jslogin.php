<?php
session_start();
require_once('config.php'); // make sure this points to your DB connection

if (isset($_POST['username']) && isset($_POST['password'])) {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE username = ? LIMIT 1";
    $stmt = $db->prepare($sql);
    $stmt->execute([$username]);

    if ($stmt->rowCount() > 0) {
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Check password (hashed or plain text fallback for admin accounts if not hashed yet)
        if (password_verify($password, $user['password']) || $password === $user['password']) {
            // Determine role
            if ($user['role'] === 'admin') {
                $_SESSION['adminlogin'] = $user;
                echo "admin";
            } else {
                $_SESSION['userlogin'] = $user;
                echo "user";
            }
        } else {
            echo "Invalid password";
        }
    } else {
        echo "User not found";
    }
} else {
    echo "Missing fields";
}
?>
