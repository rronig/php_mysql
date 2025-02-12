<?php
session_start();

// Check if the user is logged in
if (empty($_SESSION['user_id'])) {
    die("User not logged in.");
}

// Get the score from the POST request
if (!isset($_POST['score'])) {
    die("Score not provided.");
}

$score = intval($_POST['score']);

// Get the user ID from the session
$user_id = $_SESSION['user_id'];

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

// Check if the user already has a score
$sql = "SELECT snake FROM scores WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    // User already has a score, fetch it
    $stmt->bind_result($existing_score);
    $stmt->fetch();

    // Compare the new score with the existing score
    if ($score > $existing_score) {
        // Update the score if the new score is higher
        $update_sql = "UPDATE scores SET snake = ? WHERE user_id = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("ii", $score, $user_id);

        if ($update_stmt->execute()) {
            echo "High score updated successfully!";
        } else {
            echo "Error updating high score: " . $update_stmt->error;
        }

        $update_stmt->close();
    } else {
        echo "New score is not higher than the existing high score.";
    }
} else {
    // User does not have a score yet, insert the new score
    $insert_sql = "INSERT INTO scores (snake, user_id) VALUES (?, ?)";
    $insert_stmt = $conn->prepare($insert_sql);
    $insert_stmt->bind_param("ii", $score, $user_id);

    if ($insert_stmt->execute()) {
        echo "High score saved successfully!";
    } else {
        echo "Error saving high score: " . $insert_stmt->error;
    }

    $insert_stmt->close();
}

$stmt->close();
$conn->close();
?>