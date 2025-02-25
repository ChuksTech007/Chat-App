<?php
$host = "localhost";  // Change if using a remote server
$user = "root";       // MySQL username
$password = "";       // MySQL password
$dbname = "chat_app"; // Database name

// Create connection
$conn = new mysqli($host, $user, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
