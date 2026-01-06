<?php
// db_connect.php

// Database Configuration
$servername = "localhost"; // Your database host
$username = "root";       // Your database username
$password = "";           // Your database password
$dbname = "airlyft";      // Your database name

// Create Database Connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    // For production, you might log the error and display a generic message
    die("Connection failed: " . $conn->connect_error);
}

// Set character set to UTF-8
$conn->set_charset("utf8");

// Optional: You could add a function here to close the connection,
// but typically for simple scripts, PHP closes it automatically at the end.
// function closeDbConnection($conn) {
//     $conn->close();
// }

?>