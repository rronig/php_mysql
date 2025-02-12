<?php
session_start();

// Redirect to login if the user is not logged in or not an admin
if (empty($_SESSION['username'])) {
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

// Fetch user details
$user_id = $_GET['id'];
$sql = "SELECT id, name, username, email FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($id, $name, $username, $email);
$stmt->fetch();
$stmt->close();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $username = $_POST['username'];
    $email = $_POST['email'];

    $update_sql = "UPDATE users SET name = ?, username = ?, email = ? WHERE id = ?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("sssi", $name, $username, $email, $user_id);

    if ($update_stmt->execute()) {
        echo "User updated successfully!";
        header("Location: dashboard.php");
        exit;
    } else {
        echo "Error updating user: " . $update_stmt->error;
    }

    $update_stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container">
        <h1>Edit User</h1>
        <form method="POST">
            <div class="mb-3">
                <label for="name" class="form-label">Name</label>
                <input type="text" id="name" name="name" class="form-control" value="<?php echo $name; ?>" required>
            </div>
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" id="username" name="username" class="form-control" value="<?php echo $username; ?>" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" id="email" name="email" class="form-control" value="<?php echo $email; ?>" required>
            </div>
            <button type="submit" class="btn btn-primary">Update User</button>
        </form>
    </div>
</body>
</html>