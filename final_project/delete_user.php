<?php
session_start();

// Redirect to login if the user is not logged in or not an admin
if (empty($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

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

// Delete user
$user_id = $_GET['id'];
$sql = "DELETE FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);

if ($stmt->execute()) {
    echo "User deleted successfully!";
} else {
    echo "Error deleting user: " . $stmt->error;
}

$stmt->close();
$conn->close();

header("Location: dashboard.php");
exit;
?>