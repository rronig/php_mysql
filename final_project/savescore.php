<?php
session_start();
require 'db_connection.php'; // Replace with your actual database connection script

// Redirect to login if the user is not logged in
if (empty($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

// Get the difficulty value from the request
$difficulty = isset($_POST['difficulty']) ? (int) $_POST['difficulty'] : 0;

// Check if difficulty is valid
if ($difficulty <= 0 || $difficulty > 20) {
    echo 'Invalid difficulty level';
    exit;
}

// Get the user ID from session
$user_id = $_SESSION['user_id']; // Assuming you store the user_id in session

// Insert or update the highest difficulty in the database
$sql = "INSERT INTO scores (user_id, difficulty) VALUES (?, ?)
        ON DUPLICATE KEY UPDATE difficulty = GREATEST(difficulty, ?)";

$stmt = $conn->prepare($sql);
$stmt->bind_param('iii', $user_id, $difficulty, $difficulty);

if ($stmt->execute()) {
    echo 'Score saved successfully!';
} else {
    echo 'Error saving score: ' . $stmt->error;
}

$stmt->close();
$conn->close();
?>
