<?php
session_start();

// Redirect to login if the user is not logged in or not an admin
if (empty($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

// Check if the user ID is provided
if (!isset($_GET['id'])) {
    header("Location: dashboard.php");
    exit;
}

$user_id = $_GET['id'];

// Database connection
$host = "127.0.0.1";
$dbUsername = "root"; // Replace with your MySQL username
$dbPassword = ""; // Replace with your MySQL password
$dbName = "final_project_php_mysql";

$conn = new mysqli($host, $dbUsername, $dbPassword, $dbName);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Promote the user to admin
$sql = "UPDATE users SET role = 'admin' WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);

if ($stmt->execute()) {
    echo "User promoted to admin successfully!";
} else {
    echo "Error promoting user: " . $stmt->error;
}

$stmt->close();
$conn->close();

// Redirect back to the dashboard
header("Location: dashboard.php");
exit;
?>