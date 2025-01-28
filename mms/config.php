<?php
$server = "localhost";
$user = "root";
$password = ""; // Leave blank for default local MySQL installations
$dbname = "mms";

try {
    $conn = new PDO("mysql:host=$server;dbname=$dbname", $user, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>
