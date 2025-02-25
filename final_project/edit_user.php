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
        header("Location: dashboard.php");
        exit;
    } else {
        $error = "Error updating user: " . $update_stmt->error;
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
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Rubik+Dirt&family=Rubik+Spray+Paint&family=Sigmar&display=swap" rel="stylesheet">
    <style>
        body {
            text-align: center;
            background: linear-gradient(87deg, rgb(87, 143, 202), rgb(209, 248, 239));
            background-attachment: fixed;
            color: white;
            font-family: "Sigmar", serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .edit-container {
            width: 800px;
            padding: 20px;
            background-color: rgba(255, 255, 255, 0.1);
            border-radius: 15px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        h1 {
            font-family: "Sigmar", serif;
            font-size: 50px;
            padding: 20px;
        }
        label {
            font-family: "Rubik Dirt", serif;
            font-size: 30px;
        }
        input.form-control {
            border-radius: 10px;
            border: 1px solid #ddd;
            padding: 10px;
            margin-bottom: 40px;
        }
        .btn-primary {
            background: #3673b5;
            border: none;
            color: white;
            font-size: 30px;
            padding: 10px;
            border-radius: 5px;
            cursor: pointer;
            width: 90%;
        }
        .btn-primary:hover {
            background: #5C8FC7;
        }
        form {
            width: 100%;
            padding: 30px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .form-group {
            width: 100%;
        }
    </style>
</head>
<body>

<div class="edit-container">
    <h1>Edit User</h1>
    <?php if (!empty($error)) { echo "<div class='alert alert-danger'>$error</div>"; } ?>
    <form method="POST">
        <div class="form-group">
            <label for="name" class="form-label">Name</label>
            <input type="text" id="name" name="name" class="form-control" value="<?php echo htmlspecialchars($name); ?>" required>
        </div>
        <div class="form-group">
            <label for="username" class="form-label">Username</label>
            <input type="text" id="username" name="username" class="form-control" value="<?php echo htmlspecialchars($username); ?>" required>
        </div>
        <div class="form-group">
            <label for="email" class="form-label">Email</label>
            <input type="email" id="email" name="email" class="form-control" value="<?php echo htmlspecialchars($email); ?>" required>
        </div>
        <button type="submit" class="btn btn-primary">Update User</button>
    </form>
</div>

</body>
</html>
