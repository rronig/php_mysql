<?php
session_start();
include_once('config.php');

$error = "";

// Check if user is already logged in
if (!empty($_SESSION['username'])) {
    header("Location: dashboard.php");
    exit;
}

if (isset($_POST['submit'])) {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if (empty($username) || empty($password)) {
        $error = "Please fill in all fields";
    } else {
        // Check if user exists
        $sql = "SELECT * FROM users WHERE username = :username";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':username', $username, PDO::PARAM_STR);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Secure password verification
            if ($password === $user['password']) {
                session_regenerate_id(true); // Prevent session fixation
                
                // Store user details in session
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];

                header("Location: dashboard.php");
                exit;
            } else {
                $error = "Incorrect password";
            }
        } else {
            $error = "User not found";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
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
        width: 800px;
        height: 700px;
        margin: auto;
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
        height: 30%;
        margin-bottom: -5px;
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
    .bab{
        margin-bottom:-20px;
    }
</style>

</head>
<body>

<div class="login-container">
    <h1 class="bab">Login</h1>
    <?php if (!empty($error)) { echo "<div class='alert alert-danger'>$error</div>"; } ?>
    <form action="login.php" method="POST">
        <div class="form-group">
            <label for="username" class="form-label">Username</label>
            <input type="text" id="username" name="username" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="password" class="form-label">Password</label>
            <input type="password" id="password" name="password" class="form-control" required>
        </div>
        <button type="submit" name="submit" class="btn btn-primary">Login</button>
    </form>
    <div class="text-center mt-3">
        <h3>Don't have an account? <a href="register.php">Register here</a></h3>
    </div>
</div>

</body>
</html>