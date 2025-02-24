<?php
// Database configuration
$host = 'localhost';      
$username = 'root';        
$password = '';            
$database = 'essyjo_management';   

// Create connection
$conn = new mysqli($host, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Optional: Uncomment this line to verify the connection during development
// echo "Connected successfully!";
?>
