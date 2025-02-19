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
    .login-container {
        width: 100%;
        max-width: 800px;
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
        margin-bottom: 20px;
    }
    .btn-primary {
        background: #3673b5;
        border: none;
        color: white;
        font-size: 30px; /* Reduced font size */
        padding: 10px 20px; /* Adjusted padding */
        border-radius: 5px;
        cursor: pointer;
        width: 100%;
        height: auto; /* Allow button height to adjust based on content */
        margin-bottom: -5px;
        margin-top:15px;
    }

    .btn-primary:hover {
        background: #5C8FC7;
    }
    a {
        color: white;
        text-decoration: none;
        font-family: "Rubik Spray Paint", serif;
    }
    .alert {
        font-family: "Rubik Dirt", serif;
        font-size: 20px;
    }
    form {
        padding: 30px;
        margin-top: 20px;
        width: 100%;
        display: flex;
        flex-direction: column;
        align-items: center;
    }
    .form-group {
        width: 100%;
    }
    .bab {
        margin-bottom: -20px;
    }
    @media (max-width: 767px) {
        .login-container {
            padding: 15px;
        }
        .btn-primary {
            font-size: 25px;
            height: 35px;
        }
        h1 {
            font-size: 40px;
        }
        label {
            font-size: 25px;
        }
    }
</style>

</head>
<body>

<div class="login-container">
    <h1 class="bab">Register</h1>
    <?php if (!empty($error)) { echo "<div class='alert alert-danger'>$error</div>"; } ?>
    <form action="register.php" method="POST">
        <div class="form-group">
            <label for="name" class="form-label">Full Name</label>
            <input type="text" id="name" name="name" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="username" class="form-label">Username</label>
            <input type="text" id="username" name="username" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="email" class="form-label">Email</label>
            <input type="email" id="email" name="email" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="password" class="form-label">Password</label>
            <input type="password" id="password" name="password" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="confirm_password" class="form-label">Confirm Password</label>
            <input type="password" id="confirm_password" name="confirm_password" class="form-control" required>
        </div>
        <button type="submit" name="submit" class="btn btn-primary">Register</button>
    </form>
    <div class="text-center mt-3">
        <h3>Already have an account? <a href="login.php">Login here</a></h3>
    </div>
</div>

</body>
</html>
