<?php

$env = parse_ini_file(__DIR__ . "/.env");

$servername = $env["DB_HOST"];
$username   = $env["DB_USER"];
$password   = $env["DB_PASS"];
$database   = $env["DB_NAME"];

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("❌ Connection failed: " . $conn->connect_error);
}
?>
<!-- Developed by Niranjan singh -->
