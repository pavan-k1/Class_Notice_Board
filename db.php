<?php
$host = 'localhost';
$user = 'root';
$password = '';
$database = 'notice_board_db'; // Make sure this DB exists in phpMyAdmin

$conn = new mysqli($host, $user, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("❌ Connection failed: " . $conn->connect_error);
}
?>
