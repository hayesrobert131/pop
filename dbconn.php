<?php
// script to create the connection between the web server and the database server

// Database connection parameters
$host = "localhost";
$username = "root";
$password = ""; // Empty password (change this in production)
$database = "poke";

// Create a MySQLi connection
$dbConn = new mysqli($host, $username, $password, $database);

// Check for connection errors
if ($dbConn->connect_error) {
    die("Failed to connect to the database: " . $dbConn->connect_error);
}
?>
