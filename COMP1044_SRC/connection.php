<?php

// Database connection settings
$host = "localhost";
$user = "root";
$password = "root";
$database = "COMP1044_Database";

// Create connection
$conn = mysqli_connect($host, $user, $password, $database);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>