<?php
$servername = "localhost";
$username = "root";
$password = "root";
$database = "form_data";
$port = 8889;

$conn = new mysqli($servername, $username, $password, $database, $port);

if ($conn->connect_error) {
    die("❌ Connection failed: " . $conn->connect_error);
}
echo "✅ Connected successfully to form_data!";
$conn->close();
?>
