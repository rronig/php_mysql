<?php
session_start();

// Redirect to login if the user is not logged in
if (empty($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

// Check if the role is set in the session
if (!isset($_SESSION['role'])) {
    $_SESSION['role'] = 'user'; // Default to 'user' if role is not set
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

// Fetch all users (only for admins)
$users = [];
if ($_SESSION['role'] === 'admin') {
    $sql = "SELECT id, name, username, email, role FROM users";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $users[] = $row;
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css">
    <style>
        body {
    background: linear-gradient(135deg, #66785F, white);
    background-attachment: fixed; /* Ensures the gradient covers the entire page */
    color: #fff;
    font-family: 'Arial', sans-serif;
    padding: 20px;
    margin: 0; /* Remove default margin */
    min-height: 100vh; /* Ensure the gradient covers the full viewport height */
}
        .card {
            background-color: #fff;
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 4px 10px #91AC8F;
            border: 2px solid transparent;
            background-clip: padding-box;
            margin-bottom: 20px;
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
        h2 {
            color: #4B5945;
        }
        .logout-button {
            position: absolute;
            top: 10px;
            right: 10px;
            padding: 10px 20px;
            background: #4B5945;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }
        .logout-button:hover {
            background: #66785F;
        }
    </style>
</head>
<body>
    <!-- Logout Button -->
    <form action="logout.php" method="POST">
        <button type="submit" class="logout-button">Logout</button>
    </form>

    <div class="container">
        <h1 class="text-center mb-4">Welcome, <?php echo $_SESSION['username']; ?>!</h1>

        <!-- Game Links -->
        <div class="card">
            <h2>Games</h2>
            <div class="d-grid gap-2">
                <a href="snake.php" class="btn btn-primary">Play Snake</a>
                <a href="tetris.php" class="btn btn-primary">Play Tetris</a>
            </div>
        </div>

        <!-- User Management (Admin Only) -->
        <?php if ($_SESSION['role'] === 'admin'): ?>
    <div class="card">
        <h2>User Management</h2>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?php echo $user['id']; ?></td>
                        <td><?php echo $user['name']; ?></td>
                        <td><?php echo $user['username']; ?></td>
                        <td><?php echo $user['email']; ?></td>
                        <td><?php echo $user['role']; ?></td>
                        <td>
                            <a href="edit_user.php?id=<?php echo $user['id']; ?>" class="btn btn-sm btn-warning">Edit</a>
                            <a href="delete_user.php?id=<?php echo $user['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this user?');">Delete</a>
                            <?php if ($user['role'] !== 'admin'): ?>
                                <a href="promote_user.php?id=<?php echo $user['id']; ?>" class="btn btn-sm btn-success" onclick="return confirm('Are you sure you want to promote this user to admin?');">Promote to Admin</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>
    </div>
</body>
</html>