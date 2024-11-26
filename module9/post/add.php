<?php
// Include database connection
include_once("config.php");

if (isset($_POST['submit'])) {
    // Sanitize input data
    $name = htmlspecialchars($_POST['name']);
    $username = htmlspecialchars($_POST['username']);
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);

    // Validate email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "Invalid email address.";
        exit;
    }

    try {
        // Insert data into the database
        $sql = "INSERT INTO users (name, username, email) VALUES (:name, :username, :email)";
        $sqlQuery = $connect->prepare($sql);
        $sqlQuery->bindParam(':name', $name);
        $sqlQuery->bindParam(':username', $username);
        $sqlQuery->bindParam(':email', $email);
        $sqlQuery->execute();

        echo "User added successfully.";
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>
