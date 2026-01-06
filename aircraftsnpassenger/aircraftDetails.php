<?php
// aircraftDetails.php (example backend file)
header('Content-Type: application/json');

$servername = "localhost"; // your database host
$username = "your_db_username";
$password = "your_db_password";
$dbname = "airlyft"; // your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die(json_encode(["error" => "Connection failed: " . $conn->connect_error]));
}

$aircraftName = $_GET['name'] ?? '';

if ($aircraftName) {
    // IMPORTANT: Use prepared statements to prevent SQL injection!
    $stmt = $conn->prepare("SELECT Aircraft_Name, Capacity, Description, Rate FROM lifts WHERE Aircraft_Name = ?");
    $stmt->bind_param("s", $aircraftName);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        echo json_encode([
            "name" => $row['Aircraft_Name'],
            "capacity" => $row['Capacity'],
            "description" => $row['Description'],
            "rate" => $row['Rate']
        ]);
    } else {
        echo json_encode(["error" => "Aircraft not found"]);
    }
    $stmt->close();
} else {
    echo json_encode(["error" => "No aircraft name provided"]);
}

$conn->close();
?>