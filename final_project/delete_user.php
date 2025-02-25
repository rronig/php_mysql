<?php
session_start();

// Redirect to login if the user is not logged in or not an admin
if (empty($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

// Check if user_id is set and valid
if (!isset($_GET['id']) || !is_numeric($_GET['id']) || $_GET['id'] <= 0) {
    echo "Invalid user ID.";
    exit;
}

// Get the user ID from the URL
$user_id = (int) $_GET['id'];

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

// Start transaction
$conn->begin_transaction();

try {
    // Delete related scores for the user first
    $deleteScoresSql = "DELETE FROM scores WHERE user_id = ?";
    $stmt = $conn->prepare($deleteScoresSql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->close();

    // Now, delete the user
    $deleteUserSql = "DELETE FROM users WHERE id = ?";
    $stmt = $conn->prepare($deleteUserSql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->close();

    // Commit transaction
    $conn->commit();

    // Redirect to dashboard with success message
    header("Location: dashboard.php?message=User+deleted+successfully");
    exit;
} catch (Exception $e) {
    // Rollback in case of error
    $conn->rollback();
    echo "Error deleting user: " . $e->getMessage();
}

$conn->close();
?>
