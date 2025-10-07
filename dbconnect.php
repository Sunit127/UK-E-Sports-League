<?php
// Database connection configuration
$host = 'localhost';
$username = 'root'; // Change this to your database username
$password = ''; // Change this to your database password
$database = 'esports'; // Database name

// Create connection
$conn = mysqli_connect($host, $username, $password, $database);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Set charset to UTF8
mysqli_set_charset($conn, "utf8");

// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>