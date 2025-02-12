<?php
session_start(); // Start the session
include_once('config.php'); 

$error = ""; // Variable to store error messages
if (!empty($_SESSION['username'])) {
    header("Location: dashboard.php");  // Redirect to dashboard if logged in
    exit;
}
if (isset($_POST['submit'])) {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);
    $name = trim($_POST['name']); // Get name input
    $email = trim($_POST['email']); // Get email input

    if (empty($name) || empty($username) || empty($password) || empty($confirm_password) || empty($email)) {
        $error = "Please fill in all fields";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match";
    } else {
        // Check if the username or email already exists
        $sql = "SELECT * FROM users WHERE username = :username OR email = :email";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':username', $username, PDO::PARAM_STR);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            $error = "Username or email already taken";
        } else {
            $sql = "INSERT INTO users (name, username, email, password, role) VALUES (:name, :username, :email, :password, :role)";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':name', $name, PDO::PARAM_STR);
            $stmt->bindParam(':username', $username, PDO::PARAM_STR);
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);
            $stmt->bindParam(':password', $password, PDO::PARAM_STR); // Storing password in plain text (not recommended)
            $role = 'user';
            $stmt->bindParam(':role', $role, PDO::PARAM_STR);
            if ($stmt->execute()) {
                // Fetch the newly created user's ID
                $user_id = $conn->lastInsertId();
                $_SESSION['user_id'] = $user_id; // Store the user ID in the session
                $_SESSION['username'] = $username;
                $_SESSION['isadmin'] = 'false';
                header("Location: dashboard.php");
                exit;
            } else {
                $error = "Registration failed. Please try again.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #66785F, white);
            color: #fff;
            font-family: 'Arial', sans-serif;
        }
        .card {
            background-color: #fff;
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 4px 10px #91AC8F;
            border: 2px solid transparent;
            background-clip: padding-box;
        }
        .card:hover {
            border-image: linear-gradient(to right, #66785F, #B2C9AD) 1;
            transform: translateY(-5px);
            transition: all 0.3s ease;
        }
        .btn-primary {
            background: linear-gradient(to right, #66785F, #B2C9AD);
            border: none;
            transition: all 0.3s ease;
        }
        .btn-primary:hover {
            background: linear-gradient(to right, #66785F, #B2C9AD);
            transform: scale(1.05);
        }
        a {
            color: #6a11cb;
            text-decoration: none;
            font-weight: bold;
            transition: color 0.3s ease;
        }
        a:hover {
            color: #4B5945;
        }
        input.form-control {
            border-radius: 10px;
            border: 1px solid #ddd;
            transition: border 0.3s ease, box-shadow 0.3s ease;
        }
        input.form-control:focus {
            border-color: #4B5945;
            box-shadow: 0 0 8px #91AC8F;
        }
        h2 {
            color: #4B5945;
        }
    </style>
</head>
<body class="d-flex align-items-center justify-content-center vh-100">
    <div class="card shadow-sm p-4" style="width: 400px;">
        <h2 class="text-center mb-4">Register</h2>
        <?php if (!empty($error)) { echo "<div class='alert alert-danger'>$error</div>"; } ?>
        <form action="register.php" method="POST">
            <div class="mb-3">
                <label for="name" class="form-label">Full Name</label>
                <input type="text" id="name" name="name" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" id="username" name="username" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" id="email" name="email" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" id="password" name="password" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="confirm_password" class="form-label">Confirm Password</label>
                <input type="password" id="confirm_password" name="confirm_password" class="form-control" required>
            </div>
            <button type="submit" name="submit" class="btn btn-primary w-100">Register</button>
        </form>
        <div class="text-center mt-3">
            <p>Already have an account? <a style="color: #4B5945" href="login.php">Login here</a></p>
        </div>
    </div>
</body>
</html>
