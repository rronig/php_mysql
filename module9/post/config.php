<?php
$server = "localhost";
$username = "root";
$password = "";
$dbname = "another_db";

try {
    $connect = new PDO("mysql:host=$server;dbname=$dbname", $username, $password);
    $connect->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Database connection successful.";
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    exit;
}
?>
