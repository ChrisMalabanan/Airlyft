<?php
class Database {
    private $host = 'localhost';
    private $db_name = 'airlyft'; // Adjust to your database name
    private $username = 'root';
    private $password = ''; // Default for XAMPP
    public function getConnection() {
        try {
            $conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->password);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $conn;
        } catch (PDOException $e) {
            return false; // Indicate failure
        }
    }
}
?>