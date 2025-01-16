<?php include_once('config.php'); 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="
https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css
">
    <style>
        body {
            background: linear-gradient(135deg, #66785F, white);
            color: #4B5945;
            font-family: 'Arial', sans-serif;
        }

        .card {
            background-color: #fff;
            border-radius: 15px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
            border: 2px solid transparent;
        }

        h2 {
            color: #4B5945;
            text-align: center;
            margin-bottom: 20px;
        }

        /* Success and Error Alerts */
        .alert-success {
            background: linear-gradient(to right, #91AC8F, #B2C9AD);
            color: #4B5945;
            border: none;
            border-radius: 10px;
        }

        .alert-danger {
            background: linear-gradient(to right, #E57373, #C62828);
            color: #fff;
            border: none;
            border-radius: 10px;
        }

        /* Register Button */
        .btn-primary {
            background: linear-gradient(to right, #66785F, #B2C9AD);
            color: white;
            border: none;
            font-weight: bold;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background: linear-gradient(to right, #4B5945, #91AC8F);
            transform: scale(1.05);
        }

        /* Links */
        a {
            color: #4B5945;
            text-decoration: underline;
            font-weight: bold;
            transition: color 0.3s ease;
        }

        a:hover {
            color: #66785F;
        }
    </style>
</head>
<body class="d-flex align-items-center justify-content-center vh-100">
    <div class="card shadow-sm p-4" style="width: 400px;">
        <h2>Register</h2>

       

        <form action="register.php" method="POST">
            <div class="mb-3">
                <label for="name" class="form-label">Name</label>
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
                <input type="confirm_password" id="confirm_password" name="confirm_password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Register</button>
        </form>
        <div class="text-center mt-3">
            <p>Already have an account? <a href="login.php">Login here</a></p>
        </div>
    </div>
</body>
</html> 